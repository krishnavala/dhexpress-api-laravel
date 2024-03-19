<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\CmsPageController;
use App\Http\Controllers\Api\DeeplinkController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserWishlistController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\PrescriptionController;
use App\Http\Controllers\Api\V2\OrderController as OrderControllerV2;


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
Route::prefix('v1')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login', [UserAuthController::class, 'login']);
    Route::post('social-login', [UserAuthController::class, 'socialLogin']);
    Route::post('forgot-password', [UserAuthController::class, 'forgotPassword']);
    Route::post('reset-password', [UserAuthController::class, 'resetPassword']);
    Route::get('get-working-hours', [OrderController::class, 'workingHours']);
    /* cms pages */
    Route::get('cms/{slug}', [CmsPageController::class, 'show']);
    /* Get deeplink url */
    Route::get('/deeplink', [DeeplinkController::class,'createDeeplink']);
    /* guest */
    Route::prefix('guest')->group(function () {
        //login
        Route::post('login', [UserAuthController::class, 'login']);
        //dashboard 
        Route::get('dashboard', [DashboardController::class,'index']);
        //Category
        Route::get('get-category', [CategoryController::class,'getAllCategory']);
        //Product
        Route::post('get-product-detail', [ProductController::class,'getProductById']);
        Route::post('get-product-list', [ProductController::class,'getProductList']);
        /*Address API*/
        Route::get('get-state', [StateController::class,'getStateByCountry']);
        Route::post('get-city', [CityController::class,'getCityByState']);
        Route::post('check-state-restricted', [StateController::class, 'CheckStateRestricted']); 
        /* cms pages */
        Route::get('cms/{slug}', [CmsPageController::class, 'show']);       
    });
    /* guest end*/
    Route::middleware('auth:api')->group(function () {
        Route::get('dashboard', [DashboardController::class,'index']);
        Route::get('user-detail', [UserController::class,'userDetail']);
        Route::post('logout', [UserController::class,'logout']);
        Route::post('change-password', [UserController::class, 'changePassword']);
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::post('test-notification', [UserController::class, 'testNotification']);

        //notification api
        Route::post('user-notification', [NotificationController::class,'getUserNotifications']);
        Route::post('read-notification', [NotificationController::class,'readNotification']);

        //Category
        Route::get('get-category', [CategoryController::class,'getAllCategory']);

        //Product
        Route::post('get-product-detail', [ProductController::class,'getProductById']);
        Route::post('get-product-list', [ProductController::class,'getProductList']);



        //Wishlist
        Route::get('get-wishlist', [UserWishlistController::class,'userWishlist']);
        Route::post('manage-wishlist', [UserWishlistController::class,'manageProductWishlist']);

        //Cart
        Route::post('manage-cart', [CartController::class, 'manageCart']);
        Route::get('get-cart', [CartController::class, 'getCart']);
        Route::get('check-cart-item', [CartController::class, 'checkCartItem']);

        //Order
        Route::post('place-order', [OrderController::class,'placeOrder']);
        Route::post('order-list', [OrderController::class,'getOrdersList']);
        Route::post('order-details', [OrderController::class,'getOrderDetails']);
        Route::post('cancel-order',[OrderController::class, 'cancelOrder']);
        Route::post('re-order',[OrderController::class, 'reOrder']);
        
        //Offer
        Route::get('get-offer-list', [OfferController::class,'getOffers']);

        //Prescription
        Route::post('add-prescription', [PrescriptionController::class,'addPrescription']);
        Route::get('get-prescriptions', [PrescriptionController::class,'getPrescription']);
        
        //Update-device-information
        Route::post('update-device-information', [UserAuthController::class, 'updateDeviceInformation']);

        //notification api
        Route::post('notification-list', [NotificationController::class, 'getUserNotifications']);
        Route::post('read-notification', [NotificationController::class, 'readNotification']);
        /*Address API*/
        Route::get('get-state', [StateController::class,'getStateByCountry']);
        Route::post('get-city', [CityController::class,'getCityByState']);
        Route::post('check-state-restricted', [StateController::class, 'CheckStateRestricted']);
        
        
    });
});

Route::prefix('v2')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login', [UserAuthController::class, 'login']);
    Route::post('social-login', [UserAuthController::class, 'socialLogin']);
    Route::post('forgot-password', [UserAuthController::class, 'forgotPassword']);
    Route::post('reset-password', [UserAuthController::class, 'resetPassword']);
    Route::get('get-working-hours', [OrderController::class, 'workingHours']);
    /* cms pages */
    Route::get('cms/{slug}', [CmsPageController::class, 'show']);
    /* Get deeplink url */
    Route::get('/deeplink', [DeeplinkController::class,'createDeeplink']);
    /* guest */
    Route::prefix('guest')->group(function () {
        //login
        Route::post('login', [UserAuthController::class, 'login']);
        //dashboard 
        Route::get('dashboard', [DashboardController::class,'index']);
        //Category
        Route::get('get-category', [CategoryController::class,'getAllCategory']);
        //Product
        Route::post('get-product-detail', [ProductController::class,'getProductById']);
        Route::post('get-product-list', [ProductController::class,'getProductList']);
        /*Address API*/
        Route::get('get-state', [StateController::class,'getStateByCountry']);
        Route::post('get-city', [CityController::class,'getCityByState']);
        Route::post('check-state-restricted', [StateController::class, 'CheckStateRestricted']); 
        /* cms pages */
        Route::get('cms/{slug}', [CmsPageController::class, 'show']);       
    });
    /* guest end*/
    Route::middleware('auth:api')->group(function () {
        Route::get('dashboard', [DashboardController::class,'index']);
        Route::get('user-detail', [UserController::class,'userDetail']);
        Route::post('logout', [UserController::class,'logout']);
        Route::post('change-password', [UserController::class, 'changePassword']);
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::post('test-notification', [UserController::class, 'testNotification']);

        //notification api
        Route::post('user-notification', [NotificationController::class,'getUserNotifications']);
        Route::post('read-notification', [NotificationController::class,'readNotification']);

        //Category
        Route::get('get-category', [CategoryController::class,'getAllCategory']);

        //Product
        Route::post('get-product-detail', [ProductController::class,'getProductById']);
        Route::post('get-product-list', [ProductController::class,'getProductList']);

        //Wishlist
        Route::get('get-wishlist', [UserWishlistController::class,'userWishlist']);
        Route::post('manage-wishlist', [UserWishlistController::class,'manageProductWishlist']);

        //Cart
        Route::post('manage-cart', [CartController::class, 'manageCart']);
        Route::get('get-cart', [CartController::class, 'getCart']);
        Route::get('check-cart-item', [CartController::class, 'checkCartItem']);
        //Order
        Route::post('place-order', [OrderControllerV2::class,'placeOrder']);
        Route::post('order-list', [OrderController::class,'getOrdersList']);
        Route::post('order-details', [OrderController::class,'getOrderDetails']);
        Route::post('cancel-order',[OrderController::class, 'cancelOrder']);
        Route::post('re-order',[OrderControllerV2::class, 'reOrder']);

        //Offer
        Route::get('get-offer-list', [OfferController::class,'getOffers']);

        //Prescription
        Route::post('add-prescription', [PrescriptionController::class,'addPrescription']);
        Route::get('get-prescriptions', [PrescriptionController::class,'getPrescription']);
        
        //Update-device-information
        Route::post('update-device-information', [UserAuthController::class, 'updateDeviceInformation']);

        //notification api
        Route::post('notification-list', [NotificationController::class, 'getUserNotifications']);
        Route::post('read-notification', [NotificationController::class, 'readNotification']);
        /*Address API*/
        Route::get('get-state', [StateController::class,'getStateByCountry']);
        Route::post('get-city', [CityController::class,'getCityByState']);
        Route::post('check-state-restricted', [StateController::class, 'CheckStateRestricted']);
    });
});

Route::fallback(function () {
    return response()->json(['error' => true,'status' => 404,'message' => 'Api Not Found!'], 404);
});