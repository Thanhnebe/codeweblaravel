@extends('Admin.layouts.master')

@section('title')
    Thêm mới sản phẩm
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Thêm mới sản phẩm</h4>
                    </div><!-- end card header -->

                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="live-preview">
                                <div class="col-md-12 mt-3">
                                    <label for="inputEmail4" class="form-label">Hình ảnh</label>
                                    <input type="file" class="form-control" name="product[image][]" id="inputImage"
                                        multiple accept="image/*">
                                </div>
                                @error('product.image')
                                <span class="text-danger mt-3">{{ $message }}</span>
                                @enderror

                                <div class="col-md-12 mt-3">
                                    <div id="imagePreview"></div>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="productSku" class="form-label">Mã sản phẩm</label>
                                    <input type="text" class="form-control" value="{{ old('product.sku') }}" name="product[sku]" id="productSku"
                                        placeholder="Nhập mã sản phẩm...">
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="productName" class="form-label">Tên sản phẩm</label>
                                    <input type="text" class="form-control" value="{{ old('product.name') }}" name="product[name]" id="productName"
                                        placeholder="Nhập tên sản phẩm...">
                                </div>
                                @error('product.name')
                                    <span class="text-danger mt-3">{{ $message }}</span>
                                @enderror

                                <div class="col-md-4 mt-3">
                                    <label for="productCategories" class="form-label">Loại sản phẩm</label>
                                    <select id="productCategories" name="product[category_id]" class="form-select">
                                        <option disabled selected>-- Chọn loại sản phẩm --</option>
                                        @if ($listCategories->toArray())
                                            @foreach ($listCategories as $cate)
                                                <option
                                                    @selected(old('product.category_id') == $cate->id)
                                                    value="{{ $cate->id }}">{{ $cate->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                @error('product.category_id')
                                    <span class="text-danger mt-3">{{ $message }}</span>
                                @enderror

                                <div class="col-md-12 mt-3">
                                    <label for="productDescribe" class="form-label">Mô tả sản phẩm</label>
                                    <textarea name="product[description]" id="productDescribe" placeholder="Nhập mô tả sản phẩm..." class="form-control ckeditor"
                                        cols="30" rows="10">{{ old('product.description') }}</textarea>
                                </div>

                                <div class="col-12 mt-3 mb-3">
                                    <label for="productSize" class="form-label">Nhập thông tin biến thể</label>
                                    <div
                                        style="border: 1px solid #494949; padding: 20px 0 0 18px; border-radius: 5px; background-color: #27272714;">
                                        <div id="variantContainer">
                                            <div class="variantColor d-flex align-items-center">
                                                <div class="mb-3 rightInputColor"
                                                     style="margin-right: 18px;width: 190px !important;">
                                                    <select name="productVariant[weight_id][]" class="form-select weight_id">
                                                        <option value="">--Chọn trọng lượng (carat)--</option>
                                                        @foreach ($weights as $key => $weight)
                                                            <option
                                                                @selected(old('productVariant.weight_id.' . $key) == $weight->id)
                                                                value="{{ $weight->id }}">{{ number_format($weight->weight, 2) }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('productVariant.weight_id.0'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('productVariant.weight_id.0') }}</span>
                                                    @endif
                                                </div>

                                                <div class="mb-3 rightInputColor"
                                                     style="margin-right: 18px; width: 200px;">
                                                    <input type="number" value="{{ old('productVariant.import_price.0') }}" name="productVariant[import_price][]"
                                                           class="form-control" placeholder="Nhập giá nhập...">
                                                    @if ($errors->has('productVariant.import_price.0'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('productVariant.import_price.0') }}</span>
                                                    @endif
                                                </div>

                                                <div class="mb-3 rightInputColor"
                                                     style="margin-right: 18px; width: 200px">
                                                    <input type="number" value="{{ old('productVariant.selling_price.0') }}" name="productVariant[selling_price][]"
                                                           class="form-control" placeholder="Nhập giá bán...">
                                                    @if ($errors->has('productVariant.selling_price.0'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('productVariant.selling_price.0') }}</span>
                                                    @endif
                                                </div>

                                                <div class="mb-3 rightInputColor"
                                                     style="margin-right: 18px; width: 200px;">
                                                    <input type="number" value="{{ old('productVariant.listed_price.0') }}" name="productVariant[listed_price][]"
                                                           class="form-control" placeholder="Nhập giá niêm yết...">
                                                    @if ($errors->has('productVariant.listed_price.0'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('productVariant.listed_price.0') }}</span>
                                                    @endif
                                                </div>

                                                <div class="mb-3 rightInputColor"
                                                     style="margin-right: 18px;width: 200px !important;">
                                                    <input type="number" name="productVariant[quantity][]"
                                                           class="form-control" value="{{ old('productVariant.quantity.0') }}" placeholder="Nhập số lượng...">
                                                    @if ($errors->has('productVariant.quantity.0'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('productVariant.quantity.0') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="text-start">
                                        <a href="{{ route('products.index') }}" class="btn btn-outline-danger">Quay
                                            lại</a>

                                        <button type="button" id="addBienTheMauSac" class="btn btn-outline-info">Thêm
                                            biến thể</button>

                                        <button type="submit" id="btnAddProduct" class="btn btn-primary">Thêm sản
                                            phẩm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
@endsection

@section('script')
@endsection
