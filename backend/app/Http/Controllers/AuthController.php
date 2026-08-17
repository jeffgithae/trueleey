<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\PasswordEmail;
use Illuminate\Support\Facades\Mail;
use DB;

/**
 * AuthController
 *
 * Handles unified authentication across VP (Trueleey) and Beauty Express.
 *
 * Database connections:
 *  - default (mysql)  → VP database  (Users, Accounts, …)
 *  - be_mysql         → Beauty Express database (users, salons, …)
 *
 * Role mapping:
 *  Beauty Express role  │ VP userType │ VP Accounts.accountType
 *  ─────────────────────┼─────────────┼────────────────────────
 *  CLIENT               │ 0 (personal)│ —
 *  SALON_ADMIN          │ 1 (business)│ Retailer  (salons are retailers)
 *  SUPER_ADMIN          │ 1 (business)│ —  (BE-only, VP login blocked)
 *
 * Login precedence:
 *  1. Check beauty-express `users` table first (bcrypt passwordHash).
 *  2. If not found there, fall back to VP `Users` table (bcrypt password).
 *
 * Register:
 *  - New VP registrations are mirrored into beauty-express `users` with the
 *    same bcrypt hash so the user can log into BE immediately.
 *  - The beauty-express `users.vpUserId` column stores the VP Users.id.
 *  - SALON_ADMIN accounts also get an Accounts row with accountType=Retailer.
 */
class AuthController extends Controller
{

    // ─────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Map a beauty-express `users` row to the shape VP's frontend expects.
     * VP frontend needs: id, email, fullName, phoneNumber, balance,
     * userType, companyName, profilePicture, address.
     */
    private function mapBeUserToVp(object $beUser, ?object $vpUser = null): array
    {
        // If there is a linked VP user, merge so VP-specific fields (balance etc.) are present.
        $balance    = $vpUser?->balance     ?? '0';
        $address    = $vpUser?->address     ?? $beUser->phone ?? '';
        $company    = $vpUser?->companyName ?? '';
        $picture    = $vpUser?->profilePicture ?? $beUser->avatarUrl ?? '';
        $phone      = $vpUser?->phoneNumber ?? $beUser->phone ?? '';
        $fullName   = $vpUser?->fullName    ?? $beUser->displayName ?? '';

        // Determine VP userType from BE role
        $userType = match($beUser->role ?? 'CLIENT') {
            'SALON_ADMIN' => '1',
            default       => '0',
        };

        // Use VP id if linked, otherwise BE id (negative to avoid collisions in client code)
        $id = $vpUser?->id ?? $beUser->id;

        return [
            'id'             => $id,
            'be_id'          => $beUser->id,
            'email'          => $beUser->email,
            'fullName'       => $fullName,
            'phoneNumber'    => $phone,
            'balance'        => $balance,
            'userType'       => $userType,
            'companyName'    => $company,
            'profilePicture' => $picture,
            'address'        => $address,
            'source'         => 'be', // so VP frontend knows this came from BE auth
        ];
    }

    /**
     * Map a VP `Users` row to the shape VP's frontend expects (unchanged).
     */
    private function mapVpUser(object $vpUser): array
    {
        return [
            'id'             => $vpUser->id,
            'email'          => $vpUser->email,
            'fullName'       => $vpUser->fullName,
            'phoneNumber'    => $vpUser->phoneNumber,
            'balance'        => $vpUser->balance,
            'userType'       => $vpUser->userType,
            'companyName'    => $vpUser->companyName ?? '',
            'profilePicture' => $vpUser->profilePicture ?? '',
            'address'        => $vpUser->address ?? '',
            'source'         => 'vp',
        ];
    }


    // ─────────────────────────────────────────────────────────────────────
    // LOGIN
    // ─────────────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $email    = strtolower(trim($request->email ?? ''));
        $password = $request->password ?? '';

        if (!$email || !$password) {
            return [];
        }

        // ── 1. Try beauty-express `users` table first ──────────────────
        try {
            $beUsers = DB::connection('be_mysql')
                ->select('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);
        } catch (\Exception $e) {
            // BE DB unreachable — fall through to VP-only auth
            $beUsers = [];
        }

        if (count($beUsers) > 0) {
            $beUser = $beUsers[0];

            if (!password_verify($password, $beUser->passwordHash)) {
                return []; // wrong password — don't fall through
            }

            // SUPER_ADMIN accounts are BE-only; block VP login
            if (($beUser->role ?? '') === 'SUPER_ADMIN') {
                return [];
            }

            // Look up the linked VP user (if any) via vpUserId cross-ref
            $vpUser = null;
            if (!empty($beUser->vpUserId)) {
                $vp = DB::select('SELECT * FROM Users WHERE id = ? LIMIT 1', [$beUser->vpUserId]);
                $vpUser = $vp[0] ?? null;
            } else {
                // Try to find by email in VP Users
                $vp = DB::select('SELECT * FROM Users WHERE email = ? LIMIT 1', [$email]);
                if (count($vp) > 0) {
                    $vpUser = $vp[0];
                    // Persist the link so next lookup is faster
                    try {
                        DB::connection('be_mysql')->update(
                            'UPDATE users SET vpUserId = ? WHERE id = ?',
                            [$vpUser->id, $beUser->id]
                        );
                    } catch (\Exception $ignored) {}
                }
            }

            return [$this->mapBeUserToVp($beUser, $vpUser)];
        }

        // ── 2. Fall back to VP `Users` table ──────────────────────────
        $vpUsers = DB::select('SELECT * FROM Users WHERE email = ? LIMIT 1', [$email]);

        if (count($vpUsers) === 0) {
            return [];
        }

        $vpUser = $vpUsers[0];

        if (!password_verify($password, $vpUser->password)) {
            return [];
        }

        // Lazy-mirror this VP user into BE so future logins also work from BE side
        $this->mirrorVpUserToBe($vpUser);

        return [$this->mapVpUser($vpUser)];
    }


    // ─────────────────────────────────────────────────────────────────────
    // REGISTER  (VP-originated registration)
    // ─────────────────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $email = strtolower(trim($request->email ?? ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Enter a valid email address';
        }

        if (trim($request->first_name ?? '') === '' || trim($request->last_name ?? '') === '') {
            return 'First name and last name are required';
        }

        if (trim($request->password ?? '') === '') {
            return 'Password is required';
        }

        // Check both DBs for existing email
        $vpExists = DB::select('SELECT id FROM Users WHERE LOWER(email) = ? LIMIT 1', [$email]);
        if (count($vpExists) > 0) {
            return 'An account with that email already exists';
        }

        try {
            $beExists = DB::connection('be_mysql')
                ->select('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
            if (count($beExists) > 0) {
                return 'An account with that email already exists';
            }
        } catch (\Exception $e) {
            // BE DB unreachable — continue with VP-only registration
        }

        $fullName    = trim($request->first_name) . ' ' . trim($request->last_name);
        $hash        = bcrypt($request->password);
        $accountType = $request->account_type; // '0' personal, '1' business
        $phone       = $request->phone ?? '';
        $address     = $request->address ?? '';
        $companyName = $request->company_name ?? '';

        // ── Insert into VP Users ───────────────────────────────────────
        DB::insert(
            'INSERT INTO Users(userType, fullName, email, phoneNumber, address, password, companyName)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$accountType, $fullName, $email, $phone, $address, $hash, $companyName]
        );

        $vpUserId = DB::getPdo()->lastInsertId();

        // Determine BE role from account_type
        // '1' = business. If company name suggests salon or explicitly passed, use SALON_ADMIN.
        // Default business accounts start as CLIENT; they become SALON_ADMIN when they
        // create a salon on beauty-express. VP Distributor/Manufacturer = CLIENT in BE.
        $beRole = 'CLIENT';

        // ── Mirror into BE users ───────────────────────────────────────
        $beId = $this->createBeUser($email, $hash, $fullName, $phone, $beRole, $vpUserId);

        return 'Account created successfully';
    }


    // ─────────────────────────────────────────────────────────────────────
    // CHECK EMAIL  (real-time validation)
    // ─────────────────────────────────────────────────────────────────────

    public function check_email(Request $request)
    {
        $email = strtolower(trim($request->email ?? ''));

        if ($email === '') {
            return 'Email is required';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Enter a valid email address';
        }

        $vpResult = DB::select('SELECT id FROM Users WHERE LOWER(email) = ? LIMIT 1', [$email]);
        if (count($vpResult) > 0) {
            return 'An account with that email already exists';
        }

        try {
            $beResult = DB::connection('be_mysql')
                ->select('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
            if (count($beResult) > 0) {
                return 'An account with that email already exists';
            }
        } catch (\Exception $e) {
            // BE DB unavailable — skip BE check
        }

        return 'available';
    }


    // ─────────────────────────────────────────────────────────────────────
    // EDIT ACCOUNT
    // ─────────────────────────────────────────────────────────────────────

    public function edit_account(Request $request)
    {
        $email = strtolower(trim($request->email ?? ''));

        DB::update(
            'UPDATE Users SET fullName = ?, address = ?, phoneNumber = ? WHERE LOWER(email) = ?',
            [$request->full_name, $request->address, $request->phone, $email]
        );

        if (!empty($request->password)) {
            $hash = bcrypt($request->password);
            DB::update('UPDATE Users SET password = ? WHERE LOWER(email) = ?', [$hash, $email]);

            // Keep BE password in sync
            try {
                DB::connection('be_mysql')->update(
                    'UPDATE users SET passwordHash = ? WHERE email = ?',
                    [$hash, $email]
                );
            } catch (\Exception $ignored) {}
        }

        // Sync displayName + phone to BE
        try {
            DB::connection('be_mysql')->update(
                'UPDATE users SET displayName = ?, phone = ? WHERE email = ?',
                [$request->full_name, $request->phone, $email]
            );
        } catch (\Exception $ignored) {}

        return 'Changes saved successfully';
    }


    // ─────────────────────────────────────────────────────────────────────
    // CREATE SHOP
    // ─────────────────────────────────────────────────────────────────────

    public function create_shop(Request $request)
    {
        $nameResult  = DB::select('SELECT id FROM Accounts WHERE name = ?', [$request->company_name]);
        $emailResult = DB::select('SELECT id FROM Accounts WHERE email = ?', [$request->email]);

        if (count($nameResult) > 0 || count($emailResult) > 0) {
            return 'A shop with that name or email already exists';
        }

        DB::insert(
            'INSERT INTO Accounts(name, email, location, accountType, userId) VALUES (?, ?, ?, ?, ?)',
            [$request->company_name, $request->email, $request->location, $request->account_type, $request->user_id]
        );

        // If this is a Salon account, upgrade the BE user to SALON_ADMIN
        if (in_array($request->account_type, ['Salon', 'Retailer', 'Manufacturer'])) {
            $this->upgradeBeUserToSalonAdmin($request->email);
        }

        return 'Account created successfully';
    }


    // ─────────────────────────────────────────────────────────────────────
    // FORGOT PASSWORD
    // ─────────────────────────────────────────────────────────────────────

    public function forgot_password(Request $request)
    {
        $email = strtolower(trim($request->email ?? ''));

        $result = DB::select('SELECT * FROM Users WHERE LOWER(email) = ? LIMIT 1', [$email]);

        if (count($result) === 0) {
            // Also check BE users
            try {
                $beResult = DB::connection('be_mysql')
                    ->select('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
                if (count($beResult) === 0) {
                    return 'Email address not found!';
                }
            } catch (\Exception $e) {
                return 'Email address not found!';
            }
        }

        $password = rand(100000, 999999); // 6-digit for security
        $hash     = bcrypt($password);

        DB::update('UPDATE Users SET password = ? WHERE LOWER(email) = ?', [$hash, $email]);

        // Sync to BE
        try {
            DB::connection('be_mysql')->update(
                'UPDATE users SET passwordHash = ? WHERE email = ?',
                [$hash, $email]
            );
        } catch (\Exception $ignored) {}

        $details = ['message' => 'Your new password is: ' . $password];
        Mail::to($email)->send(new PasswordEmail($details));

        return 'A message with your new password has been sent to your email address. Use it to login to your account';
    }


    // ─────────────────────────────────────────────────────────────────────
    // READ-ONLY METHODS (unchanged behaviour)
    // ─────────────────────────────────────────────────────────────────────

    public function business_details(Request $request)
    {
        return DB::select('SELECT * FROM Accounts WHERE userId = ?', [$request->user_id]);
    }

    public function user_details(Request $request)
    {
        return DB::select('SELECT * FROM Users WHERE LOWER(email) = ?', [strtolower(trim($request->email ?? ''))]);
    }

    public function user_transactions(Request $request)
    {
        return DB::select('SELECT * FROM orderPayments WHERE paidBy = ?', [$request->user_id]);
    }

    public function send_money(Request $request)
    {
        $data = DB::select('SELECT * FROM Users WHERE LOWER(email) = ? LIMIT 1', [strtolower($request->receiver)]);

        if (count($data) === 0) {
            return 'no-email';
        }

        $senderRows = DB::select('SELECT * FROM Users WHERE LOWER(email) = ? LIMIT 1', [strtolower($request->sender)]);

        if (count($senderRows) === 0) {
            return 'no-email';
        }

        $senderBalance  = $senderRows[0]->balance;
        $receiverBalance = $data[0]->balance;

        if ($request->amount > $senderBalance) {
            return 'insufficient';
        }

        DB::update('UPDATE Users SET balance = ? WHERE LOWER(email) = ?',
            [$senderBalance  - $request->amount, strtolower($request->sender)]);
        DB::update('UPDATE Users SET balance = ? WHERE LOWER(email) = ?',
            [$receiverBalance + $request->amount, strtolower($request->receiver)]);

        DB::insert(
            'INSERT INTO money_sent(sender, receiver, amount, comment) VALUES (?, ?, ?, ?)',
            [$request->sender, $request->receiver, $request->amount, 'Amount sent/received']
        );

        return 'success';
    }

    public function money_sent(Request $request)
    {
        return DB::select('SELECT * FROM money_sent WHERE sender = ? LIMIT 5', [$request->email]);
    }


    // ─────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Create a new record in beauty-express `users` table.
     * Returns the new BE user id, or null on failure.
     */
    private function createBeUser(
        string $email,
        string $bcryptHash,
        string $displayName,
        string $phone,
        string $role,
        ?int $vpUserId
    ): ?int {
        try {
            DB::connection('be_mysql')->insert(
                'INSERT INTO users
                    (email, passwordHash, displayName, phone, role, emailVerified, vpUserId, createdAt, updatedAt)
                 VALUES (?, ?, ?, ?, ?, 1, ?, NOW(), NOW())',
                [$email, $bcryptHash, $displayName, $phone, $role, $vpUserId]
            );
            return DB::connection('be_mysql')->getPdo()->lastInsertId();
        } catch (\Exception $e) {
            // Log but don't fail the VP registration
            \Log::warning('BE user mirror failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lazily mirror a VP user into beauty-express `users` if not already there.
     * Called when a VP-only user logs in so future BE logins also work.
     */
    private function mirrorVpUserToBe(object $vpUser): void
    {
        try {
            $existing = DB::connection('be_mysql')
                ->select('SELECT id FROM users WHERE email = ? LIMIT 1', [$vpUser->email]);

            if (count($existing) > 0) {
                // Already exists — just ensure vpUserId is linked
                DB::connection('be_mysql')->update(
                    'UPDATE users SET vpUserId = ?, passwordHash = ? WHERE email = ?',
                    [$vpUser->id, $vpUser->password, $vpUser->email]
                );
                return;
            }

            // Determine BE role: VP userType=1 + Accounts row with accountType=Salon → SALON_ADMIN
            $beRole = 'CLIENT';
            if ($vpUser->userType === '1') {
                $salonAccount = DB::select(
                    "SELECT id FROM Accounts WHERE userId = ? AND accountType IN ('Salon','Retailer','Manufacturer') LIMIT 1",
                    [$vpUser->id]
                );
                if (count($salonAccount) > 0) {
                    $beRole = 'SALON_ADMIN';
                }
            }

            $this->createBeUser(
                $vpUser->email,
                $vpUser->password, // already bcrypt from VP
                $vpUser->fullName ?? '',
                $vpUser->phoneNumber ?? '',
                $beRole,
                $vpUser->id
            );
        } catch (\Exception $e) {
            \Log::warning('mirrorVpUserToBe failed: ' . $e->getMessage());
        }
    }

    /**
     * Upgrade an existing BE user's role to SALON_ADMIN when they create
     * a Salon/Retailer/Manufacturer account on VP.
     */
    private function upgradeBeUserToSalonAdmin(string $email): void
    {
        try {
            DB::connection('be_mysql')->update(
                "UPDATE users SET role = 'SALON_ADMIN', updatedAt = NOW()
                 WHERE email = ? AND role = 'CLIENT'",
                [strtolower($email)]
            );
        } catch (\Exception $ignored) {}
    }
}
