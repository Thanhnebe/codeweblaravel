<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\WeightController;
use App\Http\Controllers\OrderController as ControllersOrderController;
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
    return view('Client.checkout');
});
Route::post('/orders/store', [ControllersOrderController::class, 'storeOrder'])->name('orders.storeOrder');


Route::prefix('admin/sliders')->name('sliders.')->group(function () {
    Route::get('/', [SlideController::class, 'index'])->name('index');
    Route::get('/create', [SlideController::class, 'create'])->name('create');
    Route::post('/', [SlideController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [SlideController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SlideController::class, 'update'])->name('update');
    Route::delete('/{id}', [SlideController::class, 'delete'])->name('delete');
});

Route::prefix('admin')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::controller(CategoryController::class)
        ->name('categories.')
        ->prefix('categories')
        ->group(function () {
            Route::get('/', 'listCategories')
                ->name('listCategories');

            Route::get('/create', 'createCategories')
                ->name('createCategories');

            Route::post('/store', 'storeCategories')
                ->name('storeCategories');

            Route::get('/edit/{id}', 'editCategories')
                ->name('editCategories');

            Route::post('/update/{id}', 'updateCategories')
                ->name('updateCategories');

            Route::post('/delete/{id}', 'deleteCategories')
                ->name('deleteCategories');
        });

    Route::controller(ProductController::class)
        ->name('products.')
        ->prefix('products')
        ->group(function () {
            Route::get('/', 'index')
                ->name('index');

            Route::get('/create', 'create')
                ->name('create');

            Route::post('/store', 'store')
                ->name('store');

            Route::get('/edit/{id}', 'edit')
                ->name('edit');

            Route::post('/update/{id}', 'update')
                ->name('update');

            Route::post('/removeImage', 'removeImage')
                ->name('removeImage');

            Route::post('/removeVariant', 'removeVariant')
                ->name('removeVariant');

            Route::post('/delete/{id}', 'delete')
                ->name('delete');
        });

    Route::controller(WeightController::class)
        ->name('weights.')
        ->prefix('weights')
        ->group(function () {
            Route::get('/', 'index')
                ->name('index');

            Route::get('/create', 'create')
                ->name('create');

            Route::get('/weights', 'weights')
                ->name('weights');

            Route::post('/store', 'store')
                ->name('store');

            Route::get('/edit/{id}', 'edit')
                ->name('edit');

            Route::post('/update/{id}', 'update')
                ->name('update');

            Route::post('/delete/{id}', 'delete')
                ->name('delete');
        });

    Route::controller(OrderController::class)
        ->name('orders.')
        ->prefix('orders')
        ->group(function () {
            Route::get('/', 'index')
                ->name('index');

            Route::post('/update-order-status/{id}', 'updateOrderStatus')
                ->name('updateOrderStatus');

            Route::delete('/delete/{id}', 'delete')
                ->name('delete');
        });
});
