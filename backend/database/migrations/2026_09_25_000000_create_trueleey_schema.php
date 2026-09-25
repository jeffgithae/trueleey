<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * VP (Trueleey) schema, reconstructed from the queries in app/Http/Controllers.
 *
 * The production schema was never committed, so column names and types here are
 * inferred from how the controllers read and write them. Table names keep the
 * exact casing the code uses (MySQL on Linux is case-sensitive).
 *
 * Every table is skipped if it already exists, so running this against a
 * database that has the real schema is a no-op.
 */
class CreateTrueleeySchema extends Migration
{
    public function up()
    {
        $this->create('Users', function (Blueprint $table) {
            $table->id();
            $table->string('userType', 10)->default('0'); // '0' personal, '1' business
            $table->string('fullName')->nullable();
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('email')->unique();
            $table->string('phoneNumber')->nullable();
            $table->string('address')->nullable();
            $table->string('password');
            $table->string('companyName')->nullable();
            $table->string('profilePicture')->nullable();
            $table->decimal('balance', 14, 2)->default(0);
            $table->timestamp('created')->useCurrent();
        });

        $this->create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
        });

        // Businesses. accountType: Manufacturer | Distributor | Retailer | Salon
        $this->create('Accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('location')->nullable();
            $table->string('accountType', 50)->index();
            $table->unsignedBigInteger('userId')->index();
            $table->timestamp('created')->useCurrent();
        });

        $this->create('ProductCategories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        $this->create('Products', function (Blueprint $table) {
            $table->id();
            $table->string('productName');
            $table->string('category')->nullable(); // ProductCategories.id, stored as sent by the frontend
            $table->unsignedBigInteger('ownerId')->index(); // Accounts.id of the manufacturer
            $table->decimal('price', 14, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('dateManufactured')->nullable();
            $table->date('expiryDate')->nullable();
            $table->string('weight')->nullable();
            $table->decimal('shippingCostPerItem', 14, 2)->nullable();
            $table->decimal('productionCost', 14, 2)->nullable();
            $table->integer('reorder_level')->default(0);
            $table->string('productPicture')->nullable();
            $table->timestamp('created')->useCurrent();
        });

        // One row per stock top-up; `stock` holds the individual product codes it generated.
        $this->create('ProductStock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->integer('amount');
            $table->string('hash')->index();
            $table->timestamp('updated_on')->useCurrent()->useCurrentOnUpdate();
        });

        $this->create('stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('hash')->index();
            $table->string('product_code')->index();
            $table->string('used', 10)->default('No');
            $table->timestamp('created')->useCurrent();
        });

        $this->create('BatchProductDeals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('productId')->index();
            $table->unsignedBigInteger('userId')->index(); // Accounts.id
            $table->integer('numberOfProductCodes')->default(0);
            $table->string('batchQRCode')->index();
            $table->string('batchName')->nullable();
            $table->integer('boxes')->default(0);
            $table->integer('quantityPerBox')->default(0);
            $table->tinyInteger('assigned')->default(0);
            $table->tinyInteger('printed')->default(0);
            $table->timestamp('created')->useCurrent();
        });

        $this->create('Boxes', function (Blueprint $table) {
            $table->id();
            $table->string('batchCode')->index();
            $table->string('boxQrCode')->index();
            $table->integer('productQty')->default(0);
            $table->unsignedBigInteger('userId')->index();
            $table->unsignedBigInteger('productId')->index();
            $table->string('used', 10)->default('No');
            $table->timestamp('created')->useCurrent();
        });

        $this->create('BoxProducts', function (Blueprint $table) {
            $table->id();
            $table->string('boxQrCode')->index();
            $table->string('productQrCode')->index();
            $table->unsignedBigInteger('productId')->index();
            $table->unsignedBigInteger('userId')->index();
            $table->string('batchCode')->index();
            $table->timestamp('created')->useCurrent();
        });

        // Orders placed on manufacturers (Orders) and on distributors (RetailerOrders).
        // status: '0' new, '1' batch/box assigned, '2' dispatched (also set to 'Dispatched').
        foreach (['Orders', 'RetailerOrders'] as $name) {
            $this->create($name, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('supplier_id')->index();
                $table->unsignedBigInteger('owner_id')->index();
                $table->date('delivery_date')->nullable();
                $table->integer('quantity')->default(0);
                $table->unsignedBigInteger('product_id')->index();
                $table->text('specifications')->nullable();
                $table->text('instructions')->nullable();
                $table->string('deliveryLocation')->nullable();
                $table->decimal('totalPrice', 14, 2)->default(0);
                $table->integer('boxes')->default(0);
                $table->integer('quantity_per_box')->default(0);
                $table->string('productName')->nullable();
                $table->string('status', 20)->default('0');
                $table->string('paid', 20)->default('No');
                $table->timestamp('created')->useCurrent();
            });
        }

        $this->create('assigned_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('batch_code')->index();
            $table->unsignedBigInteger('sender')->index();
            $table->unsignedBigInteger('receiver')->index();
            $table->string('received', 10)->default('No');
            $table->timestamp('created')->useCurrent();
        });

        $this->create('DistributorProducts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('productId')->index();
            $table->unsignedBigInteger('distributor')->index();
            $table->unsignedBigInteger('manufacturer')->nullable();
            $table->string('productName')->nullable();
            $table->decimal('sellingPrice', 14, 2)->default(0);
            $table->integer('reorder_level')->default(0);
            $table->timestamp('created')->useCurrent();
        });

        // Note the column is reorderLevel here but reorder_level on the other product tables.
        $this->create('RetailerProducts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('productId')->index();
            $table->unsignedBigInteger('retailer')->index();
            $table->unsignedBigInteger('distributor')->nullable();
            $table->string('productName')->nullable();
            $table->decimal('sellingPrice', 14, 2)->default(0);
            $table->integer('reorderLevel')->default(0);
            $table->timestamp('created')->useCurrent();
        });

        $this->create('RetailerBoxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orderId')->index();
            $table->string('boxQrCode')->index();
            $table->integer('productQty')->default(0);
            $table->unsignedBigInteger('retailer')->index();
            $table->unsignedBigInteger('distributor')->index();
            $table->decimal('buyingPrice', 14, 2)->default(0);
            $table->decimal('sellingPrice', 14, 2)->default(0);
            $table->decimal('totalPrice', 14, 2)->default(0);
            $table->unsignedBigInteger('productId')->index();
            $table->string('batchCode')->index();
            $table->string('received', 10)->default('No');
            $table->timestamp('created')->useCurrent();
        });

        $this->create('RetailerBoxesProducts', function (Blueprint $table) {
            $table->id();
            $table->string('batchCode')->index();
            $table->string('boxQrCode')->index();
            $table->unsignedBigInteger('productId')->index();
            $table->string('productQrCode')->index();
            $table->decimal('buyingPrice', 14, 2)->default(0);
            $table->decimal('sellingPrice', 14, 2)->default(0);
            $table->unsignedBigInteger('retailer')->index();
            $table->unsignedBigInteger('distributor')->index();
            $table->unsignedBigInteger('buyer')->default(0); // Users.id, 0 until sold
            $table->string('bought', 10)->default('No');
            $table->string('used', 10)->default('No');
            $table->date('date_bought')->nullable();
            $table->unsignedBigInteger('shopping_order_id')->nullable()->index();
            $table->timestamp('created')->useCurrent();
        });

        $this->create('shopping_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index(); // random number generated in the controller
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->date('date_bought')->nullable();
            $table->unsignedBigInteger('buyer')->index();
            $table->unsignedBigInteger('retailer')->index();
            $table->tinyInteger('website_order')->default(0);
            $table->timestamp('created')->useCurrent();
        });

        $this->create('website_orders_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('product_name')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->unsignedBigInteger('order_id')->index();
            $table->timestamp('created')->useCurrent();
        });

        $this->create('orderPayments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orderId')->index();
            $table->unsignedBigInteger('paidBy')->index();
            $table->unsignedBigInteger('paidTo')->index();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('comment')->nullable();
            $table->timestamp('created')->useCurrent();
        });

        // sender/receiver are email addresses, not ids.
        $this->create('money_sent', function (Blueprint $table) {
            $table->id();
            $table->string('sender')->index();
            $table->string('receiver');
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('comment')->nullable();
            $table->timestamp('created')->useCurrent();
        });

        $this->create('Loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId')->index();
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('dueDate')->nullable();
            $table->tinyInteger('paid')->default(0);
            $table->tinyInteger('approved')->default(0);
            $table->timestamp('createdAt')->useCurrent();
        });

        $this->create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->decimal('amount', 14, 2)->default(0);
            $table->timestamp('created')->useCurrent();
        });

        $this->create('my_suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->index();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->timestamp('created')->useCurrent();
        });
    }

    public function down()
    {
        // These tables may be the real production ones (up() skips existing tables),
        // so only ever drop them in a local environment.
        if (! app()->environment('local')) {
            return;
        }

        foreach ([
            'my_suppliers', 'loan_payments', 'Loans', 'money_sent', 'orderPayments',
            'website_orders_items', 'shopping_orders', 'RetailerBoxesProducts', 'RetailerBoxes',
            'RetailerProducts', 'DistributorProducts', 'assigned_batches', 'RetailerOrders', 'Orders',
            'BoxProducts', 'Boxes', 'BatchProductDeals', 'stock', 'ProductStock', 'Products',
            'ProductCategories', 'Accounts', 'admin', 'Users',
        ] as $name) {
            Schema::dropIfExists($name);
        }
    }

    private function create(string $name, Closure $callback)
    {
        if (! Schema::hasTable($name)) {
            Schema::create($name, $callback);
        }
    }
}
