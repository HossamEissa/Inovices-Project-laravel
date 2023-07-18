<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\InvoicesDetailController;
use App\Http\Controllers\InvoiceAttachmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Invoices_Report;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SectionController;
use App\Models\invoices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
});

############################## Laravel Authentication Ui #########################################
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

############################## End Laravel Authentication Ui ######################################

############################### Invoices ###############################################
Route::resource('invoices', InvoicesController::class);
Route::controller(InvoicesController::class)->group(function () {
    Route::get('section/{id}', 'getProduct');
    Route::get('inovice/edit/{id}', 'edit')->name('invoice_edit');
    Route::get('status_show/{id}', 'show')->name('Status_show');
    Route::post('status_update/{id}', 'Status_Update')->name('Status_Update');
    Route::get('invoices_Paid', 'paid');
    Route::get('invoices_Partial', 'partial');
    Route::get('invoices_unPaid', 'unpaid');
    Route::get('Print_invoice/{id}', 'print');
});

################################ End Invoices ##########################################

################################ Invoices Archive ##########################################
Route::resource('Archive', ArchiveController::class);
################################ End Invoices Archive ##########################################

################################ Invoices Details ##########################################
Route::controller(InvoicesDetailController::class)->group(function () {
    Route::get('InvoiceDetails/{id}', 'show')->name('invoices_detalis');
    Route::get('download/{invoice_number}/{file_name}', 'get_file')->name('download');
    Route::get('view_file/{invoice_number}/{file_name}', 'open_file')->name('view_file');
    Route::post('delete_file', 'destroy')->name('delete_file');
});
################################ End Invoices Details ##########################################

################################ Invoices Attachments ##########################################
Route::controller()->group(function () {
    Route::post('invoiceAttachment', 'store')->name('invoice_attachment');
});
################################ End Invoices Attachments ##########################################

################################  Sections ##########################################
Route::resource('sections', SectionController::class);
Route::controller(SectionController::class)->group(function () {

});

################################ End Sections ##########################################


################################ Products ##########################################
Route::resource('products', ProductController::class);
Route::controller()->group(function () {

});

################################ End Product ##########################################

############################## Spatie Permission ##########################################
Route::group(['middleware' => ['auth']], function () {

    Route::resource('roles', RoleController::class);

    Route::resource('users', UserController::class);

});
############################## End Spatie Permission ########)##############################

############################## Reports ###############################################
Route::get('invoices_report', [Invoices_Report::class, 'index']);
Route::post('Search_invoices', [Invoices_Report::class,'Search_invoices']);
############################## End Reports ###############################################

############################## Admin Routes ###############################################
Route::controller(AdminController::class,)->group(function () {
    Route::get('/{page}', 'index');
});
############################## End Admin Routes ###############################################

