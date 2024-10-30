<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/checkRole', [RoleController::class, 'checkRole']);
Route::post('/register', [AuthenticationController::class, 'apiRegister']);
Route::post('/login', [AuthenticationController::class, 'apiLogin']);

// Đăng ký route cho đăng xuất người dùng
Route::post('/logout', [AuthenticationController::class, 'apiLogout'])->middleware('auth:sanctum');

Route::prefix('product')
    ->name('product.')
    ->controller(ProductController::class)
    ->group(function () {

        // api danh sách sản phẩm
        Route::get('/product-list', 'index')
            ->name('index');
        // end api

        // api top 5 sản phẩm mua nhất
        Route::get('/top5ProductNew', 'top5ProductNew')
            ->name('top5ProductNew');
        // end api

        // api chi tiết sản phẩm
        Route::get('/product-detail/{id}', 'show')
            ->name('show');
        // end api

        // api lấy sản phẩm theo danh mục
        Route::get('/category/{id}', 'getProductsByCategory')
            ->name('getProductsByCategory');
        // end api

        // api lọc sản phẩm (Tìm kiếm)
        Route::get('/filterProducts', 'filterProducts')
            ->name('filterProducts');
        // end api

    });


Route::prefix('categories')
    ->name('categories.')
    ->controller(CategoryController::class)
    ->group(function () {

        // api danh sách danh mục
        Route::get('/list-category', 'getAllCategory')
            ->name('getAllCategory');
        // end api

    });


Route::prefix('carts')
    ->name('carts.')
    ->controller(CartController::class)
    ->group(function () {

        // api danh sách đơn hàng
        Route::get('/cart-list/{userId}', 'cartByUserId')
            ->name('cartByUserId');
        // end api

        // api thêm vào giỏ hàng
        Route::post('/addToCart', 'addToCart')
            ->name('addToCart');
        // end api

        // api cập nhật số lượng đơn hàng
        Route::post('/update-quantity', 'updateQuantity')
            ->name('updateQuantity');
        // end api

        // api xóa giỏ hàng
        Route::delete('/delete-cart/{id}', 'deleteCart')
            ->name('deleteCart');
        // end api

    });


Route::prefix('orders')
    ->name('orders.')
    ->controller(OrderController::class)
    ->group(function () {

        // api danh sách đơn hàng theo người dùng (CÓ lọc)
        Route::get('/order-list/{userId}', 'getAllOrderByUser')
            ->name('getAllOrderByUser');
        // end api

        // api chi tiết đơn hàng theo id đơn hàng
        Route::get('/order-detail/{orderId}', 'getOrderDetails')
            ->name('getOrderDetails');
        // end api

        // api thay dổi trạng thái đơn hàng
        Route::get('/order-markAsCompleted/{orderId}', 'markAsCompleted')
            ->name('markAsCompleted');
        // end api
        
        // api hủy đơn hàng
        Route::get('/cancel-order/{orderId}', 'cancelOrder')
            ->name('cancelOrder');
        // end api

        // api thanh toán đơn hàng
        Route::post('/storeOrder', 'storeOrder')
            ->name('storeOrder');
        // end api

        // api thanh toán qua VNPay
        Route::get('/vnpayReturn', 'vnpayReturn')
            ->name('vnpayReturn');
        // end api
    });
