<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login',[App\Http\Controllers\AuthController::class,'login']);

Route::get('business-details/{user_id}',[App\Http\Controllers\AuthController::class,'business_details']);

Route::get('business-products/{business_id}',[App\Http\Controllers\ProductsController::class,'business_products']);

Route::post('search-product',[App\Http\Controllers\ProductsController::class,'search_product']);

Route::get('distributor-orders/{business_id}',[App\Http\Controllers\ProductsController::class,'distributor_orders']);

Route::post('distributor-order-details',[App\Http\Controllers\ProductsController::class,'distributor_order_details']);



Route::post('change-order-status',[App\Http\Controllers\ProductsController::class,'change_order_status']);

Route::post('delete-order',[App\Http\Controllers\ProductsController::class,'delete_order']);
Route::post('search-order',[App\Http\Controllers\ProductsController::class,'search_order']);


Route::get('outgoing-orders/{business_id}',[App\Http\Controllers\ProductsController::class,'outgoing_orders']);

Route::post('new-product',[App\Http\Controllers\ProductsController::class,'new_product']);

Route::get('product-categories',[App\Http\Controllers\ProductsController::class,'product_categories']);

Route::get('business-batches/{business_id}',[App\Http\Controllers\ProductsController::class,'business_batches']);

Route::post('add-batch-to-order',[App\Http\Controllers\ProductsController::class,'add_batch_to_order']);


Route::get('batches-to-assigned/{business_id}/{product_id}',[App\Http\Controllers\ProductsController::class,'batches_to_assign']);

Route::get('assigned-batches/{business_id}/{order_id}',[App\Http\Controllers\ProductsController::class,'assigned_batches']);


Route::post('delete-assigned-batch',[App\Http\Controllers\ProductsController::class,'delete_assigned_batch']);

Route::get('get-suppliers',[App\Http\Controllers\ProductsController::class,'get_suppliers']);

Route::post('new-supplier',[App\Http\Controllers\ProductsController::class,'new_supplier']);

Route::get('my-suppliers/{business_id}',[App\Http\Controllers\ProductsController::class,'my_suppliers']);


Route::get('get-products/{business_id}',[App\Http\Controllers\ProductsController::class,'get_products']);

Route::post('new-batch',[App\Http\Controllers\ProductsController::class,'new_batch']);

Route::get('supplier-products/{supplier_id}',[App\Http\Controllers\ProductsController::class,'supplier_products']);

Route::post('create-order',[App\Http\Controllers\ProductsController::class,'create_order']);

Route::get('supplier-details/{supplier_id}',[App\Http\Controllers\ProductsController::class,'supplier_details']);

Route::get('received-batches/{business_id}',[App\Http\Controllers\ProductsController::class,'received_batches']);

Route::get('d-received-batches/{business_id}',[App\Http\Controllers\ProductsController::class,'d_received_batches']);


Route::post('add-batch-to-shelf',[App\Http\Controllers\ProductsController::class,'add_batch_to_shelf']);

Route::get('check-batch/{batch_code}',[App\Http\Controllers\ProductsController::class,'check_batch']);


Route::get('get-products-to-add',[App\Http\Controllers\ProductsController::class,'get_products_to_add']);

Route::post('add-product',[App\Http\Controllers\ProductsController::class,'add_product']);
Route::post('add-retailer-product',[App\Http\Controllers\ProductsController::class,'add_retailer_product']);

Route::get('d-products/{business_id}',[App\Http\Controllers\ProductsController::class,'d_products']);
Route::get('r-products/{business_id}',[App\Http\Controllers\ProductsController::class,'r_products']);


Route::get('d-supplier-products/{supplier_id}',[App\Http\Controllers\ProductsController::class,'d_supplier_products']);

Route::post('create-retailer-order',[App\Http\Controllers\ProductsController::class,'create_retailer_order']);



Route::get('retailer-orders/{business_id}',[App\Http\Controllers\ProductsController::class,'retailer_orders']);

Route::post('retailer-order-details',[App\Http\Controllers\ProductsController::class,'retailer_order_details']);

Route::get('boxes-to-assign/{business_id}/{product_id}',[App\Http\Controllers\ProductsController::class,'boxes_to_assign']);


Route::post('add-box-to-order',[App\Http\Controllers\ProductsController::class,'add_box_to_order']);

Route::get('assigned-boxes/{business_id}/{order_id}',[App\Http\Controllers\ProductsController::class,'assigned_boxes']);

Route::post('delete-assigned-box',[App\Http\Controllers\ProductsController::class,'delete_assigned_box']);

Route::post('change-retailer-order-status',[App\Http\Controllers\ProductsController::class,'change_retailer_order_status']);



Route::post('generate-box-qr',[App\Http\Controllers\ProductsController::class,'generate_box_qr']);

Route::post('generate-product-qr',[App\Http\Controllers\ProductsController::class,'generate_product_qr']);


Route::get('retailer-outgoing-orders/{business_id}',[App\Http\Controllers\ProductsController::class,'retailer_outgoing_orders']);


Route::post('cart-details',[App\Http\Controllers\ProductsController::class,'cart_details']);

Route::post('check-qr',[App\Http\Controllers\ProductsController::class,'check_qr']);


Route::post('pay-shopping',[App\Http\Controllers\ProductsController::class,'pay_shopping']);

Route::post('user-details',[App\Http\Controllers\AuthController::class,'user_details']);

Route::get('user-transactions/{user_id}',[App\Http\Controllers\AuthController::class,'user_transactions']);

Route::post('send-money',[App\Http\Controllers\AuthController::class,'send_money']);

Route::get('manufacturer-inventory/{business_id}',[App\Http\Controllers\ProductsController::class,'manufacturer_inventory']);


Route::get('distributor-inventory/{business_id}',[App\Http\Controllers\ProductsController::class,'distributor_inventory']);


Route::get('retailer-inventory/{business_id}',[App\Http\Controllers\ProductsController::class,'retailer_inventory']);


Route::get('retailer-sales/{business_id}',[App\Http\Controllers\SalesController::class,'retailer_sales']);

Route::get('money-sent/{email}',[App\Http\Controllers\AuthController::class,'money_sent']);


Route::get('product-details/{product_id}',[App\Http\Controllers\ProductsController::class,'product_details']);
Route::post('edit-product',[App\Http\Controllers\ProductsController::class,'edit_product']);
Route::post('check-email',[App\Http\Controllers\AuthController::class,'check_email']);
Route::post('register',[App\Http\Controllers\AuthController::class,'register']);
Route::post('edit-account',[App\Http\Controllers\AuthController::class,'edit_account']);
Route::post('create-shop',[App\Http\Controllers\AuthController::class,'create_shop']);
Route::get('d-product-details/{id}',[App\Http\Controllers\ProductsController::class,'d_product_details']);
Route::get('r-product-details/{id}',[App\Http\Controllers\ProductsController::class,'r_product_details']);


Route::post('edit-d-product',[App\Http\Controllers\ProductsController::class,'edit_d_product']);
Route::post('edit-r-product',[App\Http\Controllers\ProductsController::class,'edit_r_product']);


Route::post('get-order-source',[App\Http\Controllers\ProductsController::class,'get_order_source']);

Route::get('fetch-manufacturers',[App\Http\Controllers\ProductsController::class,'fetch_manufacturers']);
Route::get('fetch-distributors',[App\Http\Controllers\ProductsController::class,'fetch_distributors']);
Route::get('fetch-retailers',[App\Http\Controllers\ProductsController::class,'fetch_retailers']);
Route::post('pay-order',[App\Http\Controllers\SalesController::class,'pay_order']);
Route::post('pay-retailer-order',[App\Http\Controllers\SalesController::class,'pay_retailer_order']);
Route::post('update-stock',[App\Http\Controllers\ProductsController::class,'update_stock']);
Route::get('stock-history/{product_id}/{business_id}',[App\Http\Controllers\ProductsController::class,'stock_history']);
Route::post('delete-stock',[App\Http\Controllers\ProductsController::class,'delete_stock']);

Route::post('mark-batch-received',[App\Http\Controllers\SalesController::class,'mark_batch_received']);

Route::post('mark-box-received',[App\Http\Controllers\SalesController::class,'mark_box_received']);

Route::post('scan-box',[App\Http\Controllers\SalesController::class,'scan_box']);

Route::post('scan-batch',[App\Http\Controllers\SalesController::class,'scan_batch']);

Route::get('fetch-manufacturer-products/{business_id}',[App\Http\Controllers\ProductsController::class,'fetch_manufacturer_products']);


Route::get('fetch-distributor-products/{business_id}',[App\Http\Controllers\ProductsController::class,'fetch_distributor_products']);


Route::get('fetch-retailer-products/{business_id}',[App\Http\Controllers\ProductsController::class,'fetch_retailer_products']);

Route::get('products-feed',[App\Http\Controllers\ProductsController::class,'products_feed']);
Route::get('products-feed/detail',[App\Http\Controllers\ProductsController::class,'feed_product_detail']);
Route::post('external-website-checkout',[App\Http\Controllers\ProductsController::class,'external_website_checkout']);
Route::post('external-business-checkout',[App\Http\Controllers\ProductsController::class,'external_business_checkout']);


Route::post('search-business',[App\Http\Controllers\SalesController::class,'search_business']);
Route::post('delete-product',[App\Http\Controllers\ProductsController::class,'delete_product']);

Route::post('forgot-password',[App\Http\Controllers\AuthController::class,'forgot_password']);

Route::get('order-details/{order_id}/{business_id}',[App\Http\Controllers\SalesController::class,'order_details']);

Route::get('my-loans/{user_id}',[App\Http\Controllers\SalesController::class,'my_loans']);
Route::post('apply-loan',[App\Http\Controllers\SalesController::class,'apply_loan']);
Route::post('pay-loan',[App\Http\Controllers\SalesController::class,'pay_loan']);



//admin routes
Route::post('admin-login',[App\Http\Controllers\AdminController::class,'admin_login']);
Route::get('personal-accounts',[App\Http\Controllers\AdminController::class,'personal_accounts']);
Route::get('business-accounts',[App\Http\Controllers\AdminController::class,'business_accounts']);
Route::get('account-details/{user_id}',[App\Http\Controllers\AdminController::class,'account_details']);
Route::post('change-account-password',[App\Http\Controllers\AdminController::class,'change_account_password']);
Route::post('delete-account',[App\Http\Controllers\AdminController::class,'delete_account']);
Route::get('get-shop/{user_id}',[App\Http\Controllers\AdminController::class,'get_shop']);
Route::get('get-retailers',[App\Http\Controllers\AdminController::class,'get_retailers']);
Route::get('get-distributors',[App\Http\Controllers\AdminController::class,'get_distributors']);
Route::get('get-manufacturers',[App\Http\Controllers\AdminController::class,'get_manufacturers']);
Route::get('get-salons',[App\Http\Controllers\AdminController::class,'get_salons']);
Route::get('loan-applications',[App\Http\Controllers\AdminController::class,'loan_applications']);
Route::post('approve-loan',[App\Http\Controllers\AdminController::class,'approve_loan']);
Route::post('view-website-cart',[App\Http\Controllers\ProductsController::class,'view_website_cart']);
Route::post('pay-website-order',[App\Http\Controllers\ProductsController::class,'pay_website_order']);

Route::get('retailer-website-orders/{business_id}',[App\Http\Controllers\SalesController::class,'retailer_website_orders']);

Route::get('website-order-details/{order_id}/{business_id}',[App\Http\Controllers\SalesController::class,'website_order_details']);

Route::post('add-item-to-order',[App\Http\Controllers\ProductsController::class,'add_item_to_order']);
Route::post('delete-website-order',[App\Http\Controllers\ProductsController::class,'delete_website_order']);

Route::get('my-shopping-orders/{buyer}',[App\Http\Controllers\ProductsController::class,'my_shopping_orders']);

// Health check — used by Docker healthcheck and load balancers
Route::get('health', function () {
    return response()->json(['status' => 'ok']);
});

// Temporary seeder endpoint - remove after use
Route::get('seed-admin', function () {
    try {
        \Illuminate\Support\Facades\DB::table('admin')->updateOrInsert(
            ['username' => 'admin'],
            ['password' => \Illuminate\Support\Facades\Hash::make('admin123456')]
        );
        return response()->json(['success' => true, 'message' => 'Admin seeded successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});








