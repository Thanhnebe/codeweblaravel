<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    // Danh sách danh mục
    public function getAllCategory()
    {
        try {
            $categorys = Category::with('products')->orderBy('id', 'desc')->get();

            if ($categorys->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có danh mục nào!'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => true,
                'message' => 'Danh sách danh mục đã được lấy',
                'data' => $categorys
            ], Response::HTTP_OK);

        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi với cơ sở dữ liệu.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi models không tạo.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Lỗi hệ thống
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi truy xuất dữ liệu',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
