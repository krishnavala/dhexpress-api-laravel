<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PDFDataController;

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
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::get('/', function () {
    return redirect(route('admin.dashboard'));
});

// Password Reset Routes...
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('auth.login');
    Route::post('login', [LoginController::class, 'login'])->name('auth.postlogin');

    Route::middleware(['auth','isSuperAdmin'])->group(function () {
        Route::get('logout', [LoginController::class, 'logout'])->name('auth.logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        //change password
        Route::get('change-password', [ProfileController::class, 'index'])->name('change.password');
        Route::post('submit-change-password', [ProfileController::class, 'submitChangePassword'])->name('submit.change.password');

        // user
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::post('users-list', [UserController::class, 'userList'])->name('users.list');
        Route::post('users-delete', [UserController::class, 'userDelete'])->name('users.delete');

        //Customer
                
        Route::get('customer', [CustomerController::class, 'index'])->name('customer');
        Route::post('customer-list', [CustomerController::class, 'customerList'])->name('customer.list');
        // Route::get('customer-add', [CustomerController::class, 'customerForm'])->name('customer.add');
        Route::get('manage-customer/{uuid?}', [CustomerController::class, 'customerForm'])->name('customer.manage.form');
        Route::post('customer-store', [CustomerController::class, 'store'])->name('customer.store');
        Route::post('customer-delete', [CustomerController::class, 'customerDelete'])->name('customer.delete');
        Route::get('customer-search', [CustomerController::class, 'customerSearch'])->name('customer.search');
        
        Route::get('customer-pdf', [PDFDataController::class, 'index'])->name('customer-pdf');
        Route::post('customer-pdf-list', [PDFDataController::class, 'customerPDFList'])->name('customer-pdf.list');
        Route::get('customer-downloadPDF', [PDFDataController::class, 'downloadPDF'])->name('customer-pdf.download');
        Route::post('pdf-delete', [PDFDataController::class, 'pdfDelete'])->name('pdf.delete');



    });
});
