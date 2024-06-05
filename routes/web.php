<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServicePartnerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PackingslipController;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

# Test Purpose
Route::prefix('test')->name('test.')->group(function(){
    Route::get('/index', [TestController::class, 'index'])->name('index');
    Route::get('/mail_send', [TestController::class, 'mail_send'])->name('mail_send');
    Route::get('/cookie', [TestController::class, 'cookie'])->name('cookie');
});


Route::get('/', [HomeController::class, 'index'])->name('home');
Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/myprofile', [HomeController::class, 'myprofile'])->name('myprofile');
Route::get('/changepassword', [HomeController::class, 'changepassword'])->name('changepassword');
Route::post('/saveprofile', [HomeController::class, 'saveprofile'])->name('saveprofile');
Route::post('/savepassword', [HomeController::class, 'savepassword'])->name('savepassword');
Route::get('/settings', [HomeController::class, 'settings'])->name('settings');
Route::post('/savesettings', [HomeController::class, 'savesettings'])->name('savesettings');
# Ajax Routes
Route::prefix('ajax')->name('ajax.')->group(function(){
    Route::post('/search-product-by-type', [AjaxController::class, 'search_product_by_type'])->name('search-product-by-type');
    Route::post('/po-bulk-scan', [AjaxController::class, 'pobulkscan'])->name('po-bulk-scan');
    Route::post('/check-po-scanned-boxes', [AjaxController::class, 'checkPOScannedboxes'])->name('check-po-scanned-boxes');
    Route::post('/check-ps-scanned-boxes', [AjaxController::class, 'checkPSScannedboxes'])->name('check-ps-scanned-boxes');
    Route::post('/subcategory-by-category', [AjaxController::class, 'subcategory_by_category'])->name('subcategory-by-category');
    Route::post('/get-single-product', [AjaxController::class, 'get_single_product'])->name('get-single-product');
});
# Managers
Route::prefix('manager')->name('manager.')->group(function(){
    Route::get('/list', [ManagerController::class, 'index'])->name('list');
    Route::get('/add', [ManagerController::class, 'create'])->name('add');
    Route::post('/store', [ManagerController::class, 'store'])->name('store');
    Route::get('/show/{id}/{getQueryString?}', [ManagerController::class, 'show'])->name('show');
    Route::get('/edit/{id}/{getQueryString?}', [ManagerController::class, 'edit'])->name('edit');
    Route::post('/update/{id}/{getQueryString?}', [ManagerController::class, 'update'])->name('update');
    Route::get('/toggle-status/{id}/{getQueryString?}',[ManagerController::class, 'toggle_status'])->name('toggle-status');
});
# Staffs
Route::prefix('staff')->name('staff.')->group(function(){
    Route::get('/list', [StaffController::class, 'index'])->name('list');
    Route::get('/add', [StaffController::class, 'create'])->name('add');
    Route::post('/store', [StaffController::class, 'store'])->name('store');
    Route::get('/show/{id}/{getQueryString?}', [StaffController::class, 'show'])->name('show');
    Route::get('/edit/{id}/{getQueryString?}', [StaffController::class, 'edit'])->name('edit');
    Route::post('/update/{id}/{getQueryString?}', [StaffController::class, 'update'])->name('update');
    Route::get('/toggle-status/{id}/{getQueryString?}',[StaffController::class, 'toggle_status'])->name('toggle-status');
});
# Customers
Route::prefix('customer')->name('customer.')->group(function(){
    Route::get('/list', [CustomerController::class, 'index'])->name('list');
    Route::get('/add', [CustomerController::class, 'create'])->name('add');
    Route::post('/store', [CustomerController::class, 'store'])->name('store');
    Route::get('/show/{id}/{getQueryString?}', [CustomerController::class, 'show'])->name('show');
    Route::get('/edit/{id}/{getQueryString?}', [CustomerController::class, 'edit'])->name('edit');
    Route::post('/update/{id}/{getQueryString?}', [CustomerController::class, 'update'])->name('update');
    Route::get('/toggle-status/{id}/{getQueryString?}',[CustomerController::class, 'toggle_status'])->name('toggle-status');
});
# Service Partners
Route::prefix('service-partner')->name('service-partner.')->group(function(){
    Route::get('/list', [ServicePartnerController::class, 'index'])->name('list');
    Route::get('/add', [ServicePartnerController::class, 'create'])->name('add');
    Route::post('/store', [ServicePartnerController::class, 'store'])->name('store');
    Route::get('/show/{id}/{getQueryString?}', [ServicePartnerController::class, 'show'])->name('show');
    Route::get('/edit/{id}/{getQueryString?}', [ServicePartnerController::class, 'edit'])->name('edit');
    Route::post('/update/{id}/{getQueryString?}', [ServicePartnerController::class, 'update'])->name('update');
    Route::get('/toggle-status/{id}/{getQueryString?}',[ServicePartnerController::class, 'toggle_status'])->name('toggle-status');
    // Route::get('/list-pincode', [ServicePartnerController::class, 'pincodes'])->name('list-pincode');
    // Route::post('/save-pincode',[ServicePartnerController::class, 'save_pincode'])->name('save-pincode');
    // Route::get('/toggle-status-pincode/{id}/{getQueryString?}',[ServicePartnerController::class, 'toggle_status_pincode'])->name('toggle-status-pincode');
    // Route::get('/view-pincodes/{id}/{getQueryString?}', [ServicePartnerController::class, 'view_pincodes'])->name('view-pincodes');
    Route::post('/asign-pincodes/{id}',[ServicePartnerController::class, 'asign_pincodes'])->name('asign-pincodes');
    Route::get('/upload-csv-order', [ServicePartnerController::class, 'upload_csv_order'])->name('upload-csv-order');
    Route::post('/assign-order-csv', [ServicePartnerController::class, 'assign_order_csv'])->name('assign-order-csv');
    Route::get('/upload-pincode-csv/{id}',[ServicePartnerController::class, 'upload_pincode_csv'])->name('upload-pincode-csv');
    Route::post('/assign-pincode-csv',[ServicePartnerController::class, 'assign_pincode_csv'])->name('assign-pincode-csv');
    Route::get('/view-duplicate-pincode-assignee', [ServicePartnerController::class, 'view_duplicate_pincode_assignee'])->name('view-duplicate-pincode-assignee');
    Route::post('/remove-duplicate-pincode-assignee', [ServicePartnerController::class, 'remove_duplicate_pincode_assignee'])->name('remove-duplicate-pincode-assignee');
    Route::get('/pincodelist/{id}', [ServicePartnerController::class, 'pincodelist'])->name('pincodelist');
    Route::post('/removepincdoebulk/{id}', [ServicePartnerController::class, 'removepincdoebulk'])->name('removepincdoebulk');
    Route::get('/removepincdoesingle/{id}/{service_partner_id}/{getQueryString?}', [ServicePartnerController::class, 'removepincdoesingle'])->name('removepincdoesingle');
});
# Suppliers
Route::prefix('supplier')->name('supplier.')->group(function(){
    Route::get('/list', [SupplierController::class, 'index'])->name('list');
    Route::get('/add', [SupplierController::class, 'create'])->name('add');
    Route::post('/store', [SupplierController::class, 'store'])->name('store');
    Route::get('/show/{id}/{getQueryString?}', [SupplierController::class, 'show'])->name('show');
    Route::get('/edit/{id}/{getQueryString?}', [SupplierController::class, 'edit'])->name('edit');
    Route::post('/update/{id}/{getQueryString?}', [SupplierController::class, 'update'])->name('update');
    Route::get('/toggle-status/{id}/{getQueryString?}',[SupplierController::class, 'toggle_status'])->name('toggle-status');
});
# Categories
Route::prefix('category')->name('category.')->group(function(){
    Route::get('/list', [CategoryController::class, 'index'])->name('list');
    Route::get('/add', [CategoryController::class, 'create'])->name('add');
    Route::post('/store', [CategoryController::class, 'store'])->name('store');
    Route::get('/show/{id}/{getQueryString?}', [CategoryController::class, 'show'])->name('show');
    Route::get('/edit/{id}/{getQueryString?}', [CategoryController::class, 'edit'])->name('edit');
    Route::post('/update/{id}/{getQueryString?}', [CategoryController::class, 'update'])->name('update');
    Route::get('/toggle-status/{id}/{getQueryString?}',[CategoryController::class, 'toggle_status'])->name('toggle-status');
});
# Products
Route::prefix('product')->name('product.')->group(function(){
    Route::get('/list', [ProductController::class, 'index'])->name('list');
    Route::get('/add', [ProductController::class, 'create'])->name('add');
    Route::post('/store', [ProductController::class, 'store'])->name('store');
    Route::get('/show/{id}/{getQueryString?}', [ProductController::class, 'show'])->name('show');
    Route::get('/edit/{id}/{getQueryString?}', [ProductController::class, 'edit'])->name('edit');
    Route::post('/update/{id}/{getQueryString?}', [ProductController::class, 'update'])->name('update');
    Route::get('/toggle-status/{id}/{getQueryString?}',[ProductController::class, 'toggle_status'])->name('toggle-status');
    Route::get('/copy/{id}/{getQueryString?}', [ProductController::class, 'copy'])->name('copy');
});
# PurchaseOrderController
Route::prefix('purchase-order')->name('purchase-order.')->group(function(){
    Route::get("/list", [PurchaseOrderController::class, 'index'])->name('list');
    Route::get("/add", [PurchaseOrderController::class, 'create'])->name('add');
    Route::post('/store', [PurchaseOrderController::class, 'store'])->name('store');
    
    Route::get("/cancel/{id}/{getQueryString?}", [PurchaseOrderController::class, 'cancel'])->name('cancel');
    Route::get("/make-grn/{id}/{getQueryString?}", [PurchaseOrderController::class, 'make_grn'])->name('make-grn');
    Route::get('/viewgrn/{id}/{getQueryString?}', [PurchaseOrderController::class, 'viewgrn'])->name('viewgrn');
    Route::post('/generate-grn', [PurchaseOrderController::class, 'generategrn'])->name('generate-grn');
    Route::get('/download/{id}', [PurchaseOrderController::class, 'download'])->name('download');
    Route::get("/show/{id}/{getQueryString?}", [PurchaseOrderController::class, 'show'])->name('show');
});
# Stock
Route::prefix('stock')->name('stock.')->group(function(){
    Route::get('/list', [StockController::class, 'index'])->name('list');
    Route::get('/logs/{id}', [StockController::class, 'logs'])->name('logs');
});
# SalesOrderController
Route::prefix('sales-order')->name('sales-order.')->group(function(){
    Route::get("/list", [SalesOrderController::class, 'index'])->name('list');
    Route::get("/add", [SalesOrderController::class, 'create'])->name('add');
    Route::post('/store', [SalesOrderController::class, 'store'])->name('store');
    Route::get("/cancel/{id}/{getQueryString?}", [SalesOrderController::class, 'cancel'])->name('cancel');
    Route::get("/show/{id}/{getQueryString?}", [SalesOrderController::class, 'show'])->name('show');
    Route::get("/generate-packing-slip/{id}/{getQueryString?}", [SalesOrderController::class, 'generate_packing_slip'])->name('generate-packing-slip');
    Route::post('/save-packing-slip/{id}/{getQueryString?}', [SalesOrderController::class, 'save_packing_slip'])->name('save-packing-slip');
});
# PackingslipController
Route::prefix('packingslip')->name('packingslip.')->group(function(){
    Route::get('/list', [PackingslipController::class, 'index'])->name('list');
    Route::get('/download/{id}', [PackingslipController::class, 'download'])->name('download');
    Route::get('/raise-invoice/{id}', [PackingslipController::class, 'raise_invoice'])->name('raise-invoice');
    Route::post('/save-invoice', [PackingslipController::class, 'save_invoice'])->name('save-invoice');
    Route::get('/goods-scan-out/{id}/{getQueryString?}', [PackingslipController::class, 'goods_scan_out'])->name('goods-scan-out');
    Route::post('/save-scan-out/{id}', [PackingslipController::class, 'save_scan_out'])->name('save-scan-out');
});
# InvoiceController
Route::prefix('invoice')->name('invoice.')->group(function(){
    Route::get('/list', [InvoiceController::class, 'index'])->name('list');
    Route::get('/download/{id}', [InvoiceController::class, 'download'])->name('download');
});
