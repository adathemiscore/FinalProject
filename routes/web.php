<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/





Route::group(['middleware' => 'visitors'],  function() {
    Route::get('/', function() { return view('authentication.login');});
    Route::get('/admin', function() { return view('authentication.admintoken');})->middleware('visitors')->name('admin');

    Route::post('/login', 'App\Http\Controllers\AuthController@postlogin')->middleware('visitors')->name('postlogin');

    Route::get('/forgotpassword', 'App\Http\Controllers\AuthController@forgotpassword')->middleware('visitors')->name('forgotpassword');
    Route::post('/forgotpasswordreset', 'App\Http\Controllers\AuthController@forgotpasswordreset')->middleware('visitors')->name('forgotpasswordreset');

    Route::get('/reset/{email}/{resetcode}', 'App\Http\Controllers\AuthController@resetpassword')->middleware('visitors')->name('resetpassword');
    Route::post('/reset/{email}/{resetcode}', 'App\Http\Controllers\AuthController@postresetpassword')->middleware('visitors')->name('postresetpassword');

    Route::get('/adminlogin', 'App\Http\Controllers\AuthController@adminlogin')->middleware('visitors')->name('postadmin');
    Route::post('/adminpost', 'App\Http\Controllers\AuthController@postadminlogin')->middleware('visitors')->name('postadminlogin');

    Route::post('/admintoken', 'App\Http\Controllers\AuthController@postadmintoken')->middleware('visitors')->name('postadmintoken');

    Route::get('/signup', 'App\Http\Controllers\AuthController@register')->middleware('visitors');
    Route::post('/signup', 'App\Http\Controllers\AuthController@postregister')->middleware('visitors')->name('signup');
});

//logout route
Route::post('/logout', 'App\Http\Controllers\AuthController@logout')->name('logout');
Route::post('/logoutadmin', 'App\Http\Controllers\AuthController@logoutadmin')->name('logoutadmin');






// Route::group(['middleware' => ['web', 'checkSessionTimeout']], function () {
// Route::group(['middleware' => 'preventLoginPageAccess'], function () {

    Route::group(['middleware' => 'buyer'], function(){
        Route::get('/buyer', 'App\Http\Controllers\BuyerController@index')->middleware('buyer');
        Route::get('/cart', 'App\Http\Controllers\BuyerController@cart')->middleware('buyer')->name('cart');
        Route::get('/addtocart/{user}/{seller}/{product}', 'App\Http\Controllers\BuyerController@addtocart')->middleware('buyer')->name('addtocart');
        Route::get('/deletecart/{user}/{id}', 'App\Http\Controllers\BuyerController@deletecart')->middleware('buyer')->name('deletecart');
        Route::get('/checkout/{id}', 'App\Http\Controllers\BuyerController@checkout')->middleware('buyer')->name('checkout');
        Route::post('/checkoutstore', 'App\Http\Controllers\BuyerController@checkoutstore')->middleware('buyer')->name('checkoutstore');
        Route::get('/feedback', 'App\Http\Controllers\BuyerController@feedback')->middleware('buyer')->name('feedback');
        Route::post('/feedbackstore', 'App\Http\Controllers\BuyerController@feedbackstore')->middleware('buyer')->name('feedbackstore');
        Route::get('/passwordresetbuyer', 'App\Http\Controllers\BuyerController@passwordresetbuyer')->middleware('buyer')->name('passwordresetbuyer');
        Route::post('/passwordresetbuyerstore', 'App\Http\Controllers\BuyerController@passwordresetbuyerstore')->middleware('buyer')->name('passwordresetbuyerstore');
        Route::get('/viewproductall', 'App\Http\Controllers\BuyerController@viewproductall')->middleware('buyer')->name('viewproductall');

    });
    // Your protected routes here
    Route::group(['middleware' => 'admin'], function(){
        Route::get('/home', 'App\Http\Controllers\AdminController@index')->middleware('admin');
        Route::get('/purchases', 'App\Http\Controllers\AdminController@purchases')->middleware('admin');
        Route::get('/viewsellers', 'App\Http\Controllers\AdminController@viewsellers')->middleware('admin')->name('viewsellers');
        Route::get('/viewbuyers', 'App\Http\Controllers\AdminController@viewbuyers')->middleware('admin')->name('viewbuyers');
        Route::get('/purchase', 'App\Http\Controllers\AdminController@purchase')->middleware('admin')->name('purchase');
        Route::get('/register', 'App\Http\Controllers\AdminController@register')->middleware('admin');
        Route::post('/register', 'App\Http\Controllers\AdminController@postregister')->middleware('admin')->name('postregister');
        Route::get('/feedbackadmin', 'App\Http\Controllers\AdminController@feedbackadmin')->middleware('admin')->name('feedbackadmin');
        Route::get('/feedbackview', 'App\Http\Controllers\AdminController@feedbackview')->middleware('admin')->name('feedbackview');
        Route::get('/passwordreset', 'App\Http\Controllers\AdminController@passwordreset')->middleware('admin')->name('passwordreset');
        Route::post('/passwordreset', 'App\Http\Controllers\AdminController@passwordresetstore')->middleware('admin')->name('passwordresetstore');
        Route::get('/deleteUser/{id}', 'App\Http\Controllers\AdminController@deleteUser')->middleware('admin')->name('deleteUser');
        Route::get('/deleteProduct/{id}', 'App\Http\Controllers\AdminController@deleteProduct')->middleware('admin')->name('deleteProduct');
        Route::get('/productsall', 'App\Http\Controllers\AdminController@productsall')->middleware('admin')->name('productsall');        
        Route::get('/deletefeedback/{id}', 'App\Http\Controllers\AdminController@deletefeedback')->middleware('admin')->name('deletefeedback');        
    
        
    });

    Route::group(['middleware' => 'seller'], function(){
        Route::get('/seller', 'App\Http\Controllers\SellerController@index')->middleware('seller')->name('seller');
        Route::get('/viewproduct', 'App\Http\Controllers\SellerController@viewproduct')->middleware('seller');
        Route::get('/viewproduct/{id}', 'App\Http\Controllers\SellerController@viewproductid')->middleware('seller');
        Route::get('/createproduct', 'App\Http\Controllers\SellerController@createproduct')->middleware('seller');
        Route::get('/sellerpurchases', 'App\Http\Controllers\SellerController@purchase')->middleware('seller');
        Route::post('/storeproduct', 'App\Http\Controllers\SellerController@storeproduct')->middleware('seller');
        // Route::get('/deliver', 'App\Http\Controllers\SellerController@deliver')->middleware('seller');
        Route::get('/feedbackseller', 'App\Http\Controllers\SellerController@feedbackseller')->middleware('seller')->name('feedbackseller');
        Route::post('/feedbacksellerstore', 'App\Http\Controllers\SellerController@feedbacksellerstore')->middleware('seller')->name('feedbacksellerstore');
        Route::get('/passwordresetseller', 'App\Http\Controllers\SellerController@passwordresetseller')->middleware('seller')->name('passwordresetseller');
        Route::post('/passwordresetsellerstore', 'App\Http\Controllers\SellerController@passwordresetsellerstore')->middleware('seller')->name('passwordresetsellerstore');
        Route::get('/deleteproduct/{id}', 'App\Http\Controllers\SellerController@deleteproduct')->middleware('seller')->name('deleteproduct');
    });
// });


