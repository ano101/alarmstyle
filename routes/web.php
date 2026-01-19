<?php

use App\Http\Controllers\CallbackRequestController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;


Route::get('/category/{path?}', [\App\Http\Controllers\CatalogController::class, 'index'])
    ->where('path', '.*')
    ->name('catalog');

Route::get('/product/{slug}', [\App\Http\Controllers\ProductController::class, 'index']);

Route::get('/img/{preset}/{path}', ImageController::class)
    ->where('path', '.*')   // разрешаем вложенные папки
    ->name('images.show');

Route::get('robots.txt', \App\Http\Controllers\RobotsController::class);

Route::post('/orders', [OrderController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('orders.store');

Route::post('/callback', [CallbackRequestController::class, 'store'])
    ->middleware('callback.throttle')
    ->name('callback.store');


Route::get('/{slug?}', [PageController::class, 'show'])
    ->where('slug', '.*')
    ->name('page.show');
