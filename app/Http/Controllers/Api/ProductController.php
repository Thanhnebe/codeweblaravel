<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    // Danh sách sản phẩm
    public function index()
    {
        try {
            // Lấy danh sách tất cả sản phẩm, kèm theo thông tin của 'category' và 'variants'
            $products = Product::with('category', 'variants')->get();

            // Kiểm tra xem có sản phẩm nào không
            if ($products->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sản phẩm trống'
                ], Response::HTTP_NOT_FOUND);
            }

            // Trả về danh sách sản phẩm
            return response()->json([
                'status' => true,
                'message' => 'Sản phẩm được lấy thành công',
                'data' => $products
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            // Lỗi hệ thống
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi truy xuất sản phẩm',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Chi tiết sản phẩm
    public function show(string $id)
    {
        try {
            $product = Product::with('category', 'variants')->findOrFail($id);

            $variants = Variant::with('product', 'weight')->where('product_id', $id)->get();

            $relatedProducts = Product::with('category', 'variants')
                ->where('id', '!=', $id)
                ->where('category_id', $product->category_id)->get();

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy sản phẩm: ' . $product->name
                ], Response::HTTP_NOT_FOUND);
            }

            // Thành công nếu tìm thấy sản phẩm
            return response()->json([
                'status' => true,
                'message' => 'Đã tìm thấy sản phẩm: ' . $product->name,
                'data' => [
                    'product' => $product,
                    'variants' => $variants,
                    'relatedProducts' => $relatedProducts
                ]
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            // Trường hợp không tìm thấy sản phẩm
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy sản phẩm',
                'errors' => [
                    'id' => 'Không tìm thấy sản phẩm có id: ' . $id,
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'line' => $e->getLine()
                ]
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            // Lỗi hệ thống
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi truy xuất sản phẩm',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Sản phẩm theo danh mục
    public function getProductsByCategory(Request $request, $id)
    {
        try {
            // Lấy sản pẩm theo danh mục
            $products = Product::with('category', 'variants')
                ->where('category_id', $id)
                ->get();

            $category = Category::with('products')->where('id', $id)->firstOrFail();

            // Kiểm tra xem có sản phẩm nào không
            if ($products->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy sản phẩm nào cho ID danh mục: ' . $id
                ], Response::HTTP_NOT_FOUND);
            }

            // Trả về danh sách sản phẩm nếu có
            return response()->json([
                'status' => true,
                'message' => 'Sản phẩm được lấy thành công',
                'data' => [
                    'products' => $products,
                    'category' => $category
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            // Xử lý lỗi hệ thống
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi truy xuất sản phẩm',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Top 5 sản phẩm mới nhất
    public function top5ProductNew()
    {
        try {
            // Lấy danh sách tất cả sản phẩm, kèm theo thông tin của 'category' và 'variants'
            $products = Product::with('category', 'variants')->limit(5)->orderBy('id', 'desc')->get();

            // Kiểm tra xem có sản phẩm nào không
            if ($products->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sản phẩm trống'
                ], Response::HTTP_NOT_FOUND);
            }

            // Trả về danh sách sản phẩm
            return response()->json([
                'status' => true,
                'message' => 'Sản phẩm được lấy thành công',
                'data' => $products
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            // Lỗi hệ thống
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi truy xuất sản phẩm',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // TÌm kiếm sản phẩm
    public function filterProducts(Request $request)
    {
        try {
            // Nhận tham số 'name' từ request
            $name = $request->input('name');

            // Khởi tạo truy vấn với Eloquent
            $query = Product::with('category', 'variants');

            // Nếu tham số 'name' có giá trị, thực hiện lọc
            if ($name) {
                $query->where('name', 'LIKE', "%{$name}%");
            }

            // Lấy danh sách sản phẩm đã lọc
            $products = $query->get();

            // Kiểm tra xem có sản phẩm nào không
            if ($products->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sản phẩm trống'
                ], Response::HTTP_NOT_FOUND);
            }

            // Trả về danh sách sản phẩm
            return response()->json([
                'status' => true,
                'message' => 'Sản phẩm được lấy thành công',
                'data' => $products
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            // Lỗi hệ thống
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi truy xuất sản phẩm',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
