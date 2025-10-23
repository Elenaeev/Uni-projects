<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
/*
| Web Routes
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/home', [HomeController::class, 'index'])->name("home.index");

//categories
Route::get('/categories', [CategoriesController::class, 'index'])->name("categories.index");
 
Route::get('/categories/{category_id_hierarchy}', [CategoriesController::class, 'show'])
    ->where('category_id_hierarchy', '^\d+(\/\d+)*$')
    ->name("categories.show");

//events
Route::get('/events', [EventsController::class, 'index'])->name("events.index")
    ->middleware('auth');

Route::get('/event/{event_id}', [EventsController::class, 'show'])->name("events.show")
    ->middleware('auth')
    ->where('event_id', '^\d+$');

Route::get('/event/create', [EventsController::class, 'createPage'])->name("events.createPage")
    ->middleware('auth');

Route::post('/event/create', [EventsController::class, 'createEventData'])->name("events.createEventData")
    ->middleware('auth');

Route::get('/event/{event_id}/edit', [EventsController::class, 'edit'])->name("events.edit")
    ->middleware('auth')
    ->where('event_id', '^\d+$');

Route::get('/event/{event_id}/add', [EventsController::class, 'add'])->name("events.add")
    ->middleware('auth')
    ->where('event_id', '^\d+$');

Route::get('/event/{event_id}/remove', [EventsController::class, 'remove'])->name("events.remove")
    ->middleware('auth')
    ->where('event_id', '^\d+$');
    
Route::post('/event/{event_id}/edit', [EventsController::class, 'saveEditedEventData'])->name("events.saveEditedEventData")
    ->middleware('auth')
    ->where('event_id', '^\d+$');


//orders
Route::get('/order/{order_id}', [OrderController::class, 'index'])->name("order.index")
    ->middleware('auth')
    ->where('event_id', '^\d+$');

Route::post('/order/{order_id}/create', [OrderController::class, 'create'])->name("order.create")
    ->middleware('auth')
    ->where('event_id', '^\d+$');

Route::patch('/order/{order_id}/edit', [OrderController::class, 'edit'])->name("order.edit")
    ->middleware('auth')
    ->where('event_id', '^\d+$');

Route::post('/order/{order_id}/delete', [OrderController::class, 'delete'])->name("order.delete")
    ->middleware('auth')
    ->where('event_id', '^\d+$');



//products
Route::get('/product/{product_id}', [ProductPageController::class, 'index'])->name("product.index")
    ->where('product_id', '^\d+$');

Route::post('/product/{product_id}', [ProductPageController::class, 'store'])->name("product.store")
    ->middleware('auth')
    ->where('product_id', '^\d+$');

Route::post('/product/{product_id}/rate', [ProductPageController::class, 'rate'])->name("product.rate")
    ->middleware('auth')
    ->where('product_id', '^\d+$');

Route::get('/product/{product_id}/edit', [ProductPageController::class, 'edit'])->name("product.edit")//routes to MANAGER page
    ->middleware('auth')
    ->where('product_id', '^\d+$');

Route::post('/product/{product_id}/edit', [ProductPageController::class, 'saveEditedProductData'])->name("product.saveEditedProductData")//routes to MANAGER page
    ->middleware('auth')
    ->where('product_id', '^\d+$');

Route::get('/product/create-in/{category_id}', [ProductPageController::class, 'createIn'])->name("product.createIn")//routes to MANAGER page
    ->middleware('auth')
    ->where('category_id', '^\d+$');

Route::post('/product/create-in/{category_id}', [ProductPageController::class, 'createProductData'])->name("product.createProductData")//routes to MANAGER page
    ->middleware('auth')
    ->where('category_id', '^\d+$');

//designs
Route::get('/design/{design_id}', [DesignController::class, 'index'])->name("design.index")
    ->middleware('auth')
    ->where('design_id', '^\d+$');


Route::get('/design/create-in/{category_id}', [DesignController::class, 'createIn'])->name("design.createIn")
    ->middleware('auth')
    ->where('category_id', '^\d+$');


Route::post('/design/create-in/{category_id}', [DesignController::class, 'createDesign'])->name("design.createDesign")
    ->middleware('auth')
    ->where('category_id', '^\d+$');

Route::post('/design/{design_id}/decline', [DesignController::class, 'declineDesign'])->name("design.acceptDesign")
    ->middleware('auth')
    ->where('design_id', '^\d+$');

Route::post('/design/{design_id}/accept', [DesignController::class, 'acceptDesign'])->name("design.declineDesign")
    ->middleware('auth')
    ->where('design_id', '^\d+$');

    
Route::patch('/design/{design_id}', [DesignController::class, 'delete'])->name("design.delete")
    ->middleware('auth')
    ->where('design_id', '^\d+$');

//user
Route::get('/profile/{user_id}', [ProfileController::class, 'index'])->name("profile.index")
    ->middleware('auth')
    ->where('user_id', '^\d+$');

Route::patch('/profile/{user_id}', [ProfileController::class, 'edit'])->name("profile.edit")
    ->middleware('auth')
    ->where('user_id', '^\d+$');


//authentication
Route::get('/register', [RegistrationController::class, 'index'])->name("register.index")
    ->middleware('guest');
Route::post('/register', [RegistrationController::class, 'store'])->name("register.store")  
    ->middleware('guest');

Route::get('/signin', [SignInController::class, 'index'])->name("login")
    ->middleware('guest');
Route::post('/signin', [SignInController::class, 'signin'])
    ->middleware('guest');
Route::patch('/signin', [SignInController::class, 'logout'])
    ->middleware('auth');
 



