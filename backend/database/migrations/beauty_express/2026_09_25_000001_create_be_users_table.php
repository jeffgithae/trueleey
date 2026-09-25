<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LOCAL DEVELOPMENT ONLY. Stand-in for the Beauty Express `users` table that
 * AuthController reads and writes over the be_mysql connection. The real table
 * belongs to the Beauty Express app; this only has the columns VP touches.
 *
 * Not in the default migrations path, so a plain `php artisan migrate` never runs it:
 *   php artisan migrate --database=be_mysql --path=database/migrations/beauty_express
 */
class CreateBeUsersTable extends Migration
{
    protected $connection = 'be_mysql';

    public function up()
    {
        if (Schema::connection($this->connection)->hasTable('users')) {
            return;
        }

        Schema::connection($this->connection)->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('passwordHash');
            $table->string('displayName')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatarUrl')->nullable();
            $table->string('role', 20)->default('CLIENT'); // CLIENT | SALON_ADMIN | SUPER_ADMIN
            $table->boolean('emailVerified')->default(false);
            $table->unsignedBigInteger('vpUserId')->nullable()->index();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent();
        });
    }

    public function down()
    {
        if (app()->environment('local')) {
            Schema::connection($this->connection)->dropIfExists('users');
        }
    }
}
