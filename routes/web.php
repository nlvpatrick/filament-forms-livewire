<?php

use App\Livewire\CreateProduct;
use App\Livewire\EditOrders;
use App\Livewire\EditProduct;
use App\Livewire\ListOrders;
use App\Livewire\ListProducts;
use App\Livewire\Orders;
use App\Livewire\Reports;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('products', ListProducts::class);
Route::get('products/create', CreateProduct::class);
Route::get('products/edit/{products}', EditProduct::class)->name('products.edit');
Route::get('report/{products}', Reports::class);

Route::get('orders/create', Orders::class);
Route::get('orders', ListOrders::class);
Route::get('orders/edit/{orders}', EditOrders::class);
