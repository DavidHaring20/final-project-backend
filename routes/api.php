<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileExportController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ItemController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/export-json/restaurant/{id}', [FileExportController::class, 'FileExport']);

//Restaurant
Route::get('/restaurants', [RestaurantController::class, 'show']);
Route::get('/restaurant/show/{id}', [RestaurantController::class, 'index']);
Route::get('/restaurant/{id}', [RestaurantController::class, 'destroy']);

//Category
Route::post('/restaurant/{id}/category', [CategoryController::class, 'store']);
Route::post('/category/update/{id}', [CategoryController::class, 'update']);
Route::get('/category/{id}', [CategoryController::class, 'destroy']);

//Subcategory
Route::post('/category/{id}/subcategory', [SubcategoryController::class, 'store']);
Route::post('/subcategory/update/{id}', [SubcategoryController::class, 'update']);
Route::get('/subcategory/{id}', [SubcategoryController::class, 'destroy']);

//Item
Route::post('/subcategory/{id}/item', [ItemController::class, 'store']);
Route::post('/item/update/{id}', [ItemController::class, 'update']);
Route::get('/item/{id}', [ItemController::class, 'destroy']);
