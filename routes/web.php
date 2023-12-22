<?php

use App\Livewire\CreateProduct;
use App\Livewire\EditProduct;
use App\Livewire\ListProducts;
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
Route::get('products/edit/{products}', EditProduct::class);
