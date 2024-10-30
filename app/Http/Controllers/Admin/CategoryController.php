<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function listCategories()
    {
        $categories = Category::orderBy('id', 'desc')->paginate(5);

        return view('Admin.pages.categories.listCategories')->with([
            'listCategories' => $categories
        ]);
    }

    public function createCategories()
    {
        return view('Admin.pages.categories.createCategories');
    }

    public function storeCategories(StoreCategoryRequest $request)
    {
        $category = new Category();
        $category->name = $request->name;
        $category->save();

        return response()->json([
            'messageCreate' => 'Thêm mới danh mục thành công'
        ], 201);
    }

    public function detailCategories($id)
    {
        $categoryById = Category::select('id', 'name','created_at')
            ->where('id', '=', $id)
            ->firstOrFail();

        return view('Admin.pages.categories.detailCategories', compact('categoryById'));
    }

    public function editCategories($id)
    {
        $categoryById = Category::select('id', 'name')
            ->where('id', $id)
            ->firstOrFail();

        return view('Admin.pages.categories.editCategories', compact('id', 'categoryById'));
    }

    public function updateCategories(UpdateCategoryRequest $request, $id)
    {
        $category = Category::where('id', $id)->firstOrFail();
        $category->name = $request->name;
        $category->save();

        return response()->json(['messageUpdate' => true], 201);
    }

    public function deleteCategories($id)
    {
        $category = Category::where('id', $id)->firstOrFail();
        $category->delete();

        return response()->json([
            'messageDeleteCategories' => 'Xóa danh mục thành công'
        ], 201);
    }
}

