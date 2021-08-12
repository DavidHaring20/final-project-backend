<?php

use App\Http\Controllers\AmountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogInController;
use App\Http\Controllers\FileExportController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileImportController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LanguageController;

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

// Login
Route::post('/login/e-mail', [LogInController::class, 'requestVerificationCode']);
Route::post('/login/e-mailAndPassword', [LogInController::class, 'authenticate']);

// Export and importing JSON file
Route::get('/export-json/restaurant/{id}', [FileExportController::class, 'FileExport']);
Route::get('/restaurant-json/{slug}', [FileExportController::class, 'ExportBySlug']);

Route::post('/import-json', [FileImportController::class, 'importJSON']);

//Languages
Route::get('/languages', [LanguageController::class, 'index']);
Route::post('/languages/new', [LanguageController::class, 'store']);

//Restaurant
Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurant/show/{id}', [RestaurantController::class, 'show']);
Route::get('/restaurant/{slug}', [RestaurantController::class, 'showBySlug']);
Route::get('/delete/restaurant/{id}', [RestaurantController::class, 'destroy']);
Route::post('/restaurant/new', [RestaurantController::class, 'store']);
Route::post('/restaurant/{id}/edit-footer', [RestaurantController::class, 'editFooter']);

//Category
Route::post('/restaurant/{id}/category', [CategoryController::class, 'store']);
Route::post('/category/update/{id}', [CategoryController::class, 'update']);
Route::get('/delete/category/{id}', [CategoryController::class, 'destroy']);

//Subcategory
Route::post('/category/{id}/subcategory', [SubcategoryController::class, 'store']);
Route::post('/subcategory/update/{id}', [SubcategoryController::class, 'update']);
Route::get('/delete/subcategory/{id}', [SubcategoryController::class, 'destroy']);

//Item
Route::post('/subcategory/{id}/item', [ItemController::class, 'store']);
Route::post('/item/update/{id}', [ItemController::class, 'update']);
Route::get('/delete/item/{id}', [ItemController::class, 'destroy']);

//Amount
Route::post('/item/{id}/amount', [AmountController::class, 'store']);
Route::get('/delete/amount/{id}', [AmountController::class, 'destroy']);
