<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Weight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $listProduct = Product::with('variants')
            ->orderBy('id', 'desc')
            ->paginate(5);

        return view('Admin.pages.products.listProduct', compact([
            'listProduct'
        ]));
    }

    public function create()
    {
        $listCategories = Category::all();
        $weights = Weight::all();

        return view('Admin.pages.products.createProduct', compact('listCategories', 'weights'));
    }

    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();

            // Lưu thông tin sản phẩm
            $product = new Product();
            $product->sku = $request->product['sku'];
            $product->name = $request->product['name'];
            $product->description = $request->product['description'];
            $product->category_id = $request->product['category_id'];

            // Kiểm tra và lưu hình ảnh sản phẩm
            if ($request->hasFile('product.image')) {
                $images = $request->file('product.image');
                $imagePaths = [];

                foreach ($images as $image) {
                    // Kiểm tra nếu tệp là hình ảnh hợp lệ
                    if ($image->isValid()) {
                        $path = $image->storePublicly('public/products');
                        $imagePaths[] = Storage::url($path);
                    } else {
                        throw new \Exception('Tệp ảnh sản phẩm không hợp lệ.');
                    }
                }

                $product->image = implode(',', $imagePaths);
            }

            // Lưu sản phẩm
            $product->save();
            $productId = $product->id;

            // Lưu thông tin biến thể sản phẩm
            if (!empty($request->productVariant)) {
                foreach ($request->productVariant['import_price'] as $key => $importPrice) {
                    // Kiểm tra nếu bất kỳ giá trị nào rỗng hoặc là null
                    if (!empty($request->productVariant['weight_id'][$key]) &&
                        !empty($request->productVariant['listed_price'][$key]) &&
                        !empty($importPrice) &&
                        !empty($request->productVariant['selling_price'][$key]) &&
                        !empty($request->productVariant['quantity'][$key])) {

                        // Nếu tất cả các giá trị hợp lệ, thêm biến thể vào DB
                        $variant = new Variant();
                        $variant->product_id = $productId;
                        $variant->weight_id = $request->productVariant['weight_id'][$key];
                        $variant->listed_price = $request->productVariant['listed_price'][$key];
                        $variant->import_price = $importPrice;
                        $variant->selling_price = $request->productVariant['selling_price'][$key];
                        $variant->quantity = $request->productVariant['quantity'][$key];

                        $variant->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('products.index')->with([
                'status_succeed' => 'Thêm sản phẩm thành công.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());

            return back()->with([
                'status_failed' => 'Đã xảy ra lỗi khi thêm.'
            ]);
        }
    }


    public function edit($id)
    {
        try {
            $product = Product::where('id', $id)->find($id);

            if (!$product) {
                return redirect()->route('products.index')->with([
                    'status_failed' => 'Sản phẩm không tìm thấy.'
                ]);
            }

            $listCategories = Category::all();

            $categories = Category::where('id', $product->category_id)->firstOrFail();

            $variants = Variant::where('product_id', $product->product_id)->get();

            $weights = Weight::all();

            return view('Admin.pages.products.editProduct', compact([
                'listCategories',
                'product',
                'categories',
                'variants',
                'id',
                'weights'
            ]));
        } catch (\Exception $e) {
            echo "Lỗi id sản phẩm không tồn tại: " . $e->getMessage();
            return response()->json(['errorIdProduct' => true]);
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Tìm sản phẩm theo ID
            $product = Product::findOrFail($id);

            // Cập nhật thông tin sản phẩm
            $product->sku = $request->product['sku'];
            $product->name = $request->product['name'];
            $product->description = $request->product['description'];
            $product->category_id = $request->product['category_id'];

            // Cập nhật hình ảnh sản phẩm nếu có
            if ($request->hasFile('product.image')) {
                if ($product->image) {
                    $oldImages = explode(',', $product->image);
                    foreach ($oldImages as $oldImage) {
                        Storage::delete($oldImage);
                    }
                }

                $images = $request->file('product.image');
                $imagePaths = [];

                foreach ($images as $image) {
                    if ($image->isValid()) {
                        $path = $image->storePublicly('public/products');
                        $imagePaths[] = Storage::url($path);
                    } else {
                        throw new \Exception('Tệp ảnh sản phẩm không hợp lệ.');
                    }
                }

                $product->image = implode(',', $imagePaths);
            }

            // Lưu sản phẩm
            $product->save();

            // Cập nhật hoặc thêm mới biến thể
            $variantIds = [];

            if (!empty($request->productVariant)) {
                foreach ($request->productVariant['weight_id'] as $key => $weightId) {
                    $variantId = isset($request->productVariant['id'][$key]) ? $request->productVariant['id'][$key] : null;
                    $importPrice = $request->productVariant['import_price'][$key];
                    $listedPrice = $request->productVariant['listed_price'][$key];
                    $sellingPrice = $request->productVariant['selling_price'][$key];
                    $quantity = $request->productVariant['quantity'][$key];

                    // Kiểm tra nếu các giá trị biến thể không rỗng hoặc null
                    if (!empty($weightId) && !empty($listedPrice) && !empty($importPrice) && !empty($sellingPrice) && !empty($quantity)) {
                        if (!empty($variantId)) {
                            // Nếu có ID, cập nhật biến thể
                            $variant = Variant::find($variantId);
                            if ($variant) {
                                $variant->update([
                                    'weight_id' => $weightId,
                                    'listed_price' => $listedPrice,
                                    'import_price' => $importPrice,
                                    'selling_price' => $sellingPrice,
                                    'quantity' => $quantity,
                                ]);
                                $variantIds[] = $variant->id; // Lưu lại ID của biến thể đã cập nhật
                            }
                        } else {
                            // Nếu không có ID, tạo mới biến thể
                            $newVariant = Variant::create([
                                'product_id' => $product->id,
                                'weight_id' => $weightId,
                                'listed_price' => $listedPrice,
                                'import_price' => $importPrice,
                                'selling_price' => $sellingPrice,
                                'quantity' => $quantity,
                            ]);
                            $variantIds[] = $newVariant->id; // Lưu lại ID của biến thể mới
                        }
                    }
                }
            }



            // Xóa các biến thể không còn tồn tại trong danh sách gửi lên
            Variant::where('product_id', $product->id)
                ->whereNotIn('id', $variantIds)
                ->delete();

            DB::commit();

            return redirect()->route('products.index')->with([
                'status_succeed' => 'Cập nhật sản phẩm thành công.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with([
                'status_failed' => 'Đã xảy ra lỗi khi cập nhật.'
            ]);
        }
    }

    public function removeImage(Request $request)
    {
        $product = Product::where('id', $request->id)->first();

        if (!$product) {
            return response()->json(['successDeleteImage' => false, 'message' => 'Biến thể không tồn tại.'], 404);
        }

        // Đường dẫn ảnh
        $imagePath = $request->image;
        $path = 'public/products/' . basename($imagePath);

        if (Storage::exists($path)) {
            Storage::delete($path);
        }

        $images = explode(',', $product->image);
        $images = array_filter($images, function ($img) use ($imagePath) {
            return trim($img) !== trim($imagePath); // Loại bỏ ảnh vừa xóa
        });

        $product->image = implode(',', $images);
        $product->save();

        return response()->json(['successDeleteImage' => true], 201);
    }

    public function removeVariant(Request $request)
    {
        try {
            $variant = Variant::where('id', $request->id)->first();

            $variant->delete();

            return response()->json(['successDeleteVariant' => true], 201);
        } catch (\Exception $e) {
            echo "Lỗi khi xóa: " . $e->getMessage();
            return response()->json(['errorVariant' => true]);
        }
    }

    public function delete($id)
    {
        try {
            // Tìm sản phẩm theo id
            $product = Product::where('id', $id)->first();

            // Kiểm tra xem sản phẩm có tồn tại không
            if (!$product) {
                return response()->json(['errorProduct' => true, 'message' => 'Sản phẩm không tồn tại.'], 404);
            }

            // Xóa ảnh của sản phẩm
            $imagePaths = explode(',', $product->image);
            foreach ($imagePaths as $path) {
                $fileName = basename($path);
                Storage::delete('public/products/' . $fileName);
            }

            // Lưu lại ID các biến thể
            $variantIds = Variant::where('product_id', $product->id)->pluck('id')->toArray();

            // Xóa biến thể và ảnh của biến thể
            foreach ($variantIds as $variantId) {
                $variant = Variant::find($variantId);
                if ($variant) {
                    $variant->delete(); // Xóa biến thể
                }
            }

            $product->delete();

            return response()->json(['successDeleteProduct' => true], 201);
        } catch (\Exception $e) {
            echo "Lỗi khi xóa: " . $e->getMessage();
            return response()->json(['errorProduct' => true, 'message' => 'Có lỗi xảy ra khi xóa sản phẩm.'], 500);
        }
    }
}
