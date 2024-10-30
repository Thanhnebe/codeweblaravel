@extends('Admin.layouts.master')

@section('title')
    Cập nhật sản phẩm
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Cập nhật sản phẩm</h4>
                    </div><!-- end card header -->

                    <form method="POST" action="{{ route('products.update', $product->id) }}"
                          enctype="multipart/form-data">
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
                                    <div id="imagePreview">
                                        @if(!empty($product->image))
                                            @foreach(explode(',', $product->image) as $image)
                                                <div class="box_img">
                                                    <img src="{{ asset($image) }}" alt="">
                                                    <button type="button"
                                                            data-id="{{ $product->id }}"
                                                            data-image="{{ $image }}"
                                                            data-url="{{ route('products.removeImage') }}"
                                                            id="btnDeleteImage">x
                                                    </button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="productSku" class="form-label">Mã sản phẩm</label>
                                    <input type="text" class="form-control" name="product[sku]" id="productSku"
                                           placeholder="Nhập mã sản phẩm..."
                                           value="{{ old('product.sku', $product->sku) }}">
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="productName" class="form-label">Tên sản phẩm</label>
                                    <input type="text" class="form-control" name="product[name]" id="productName"
                                           placeholder="Nhập tên sản phẩm..."
                                           value="{{ old('product.name', $product->name) }}">
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
                                                    value="{{ $cate->id }}" {{ $product->category_id == $cate->id ? 'selected' : '' }}>{{ $cate->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                @error('product.category_id')
                                <span class="text-danger mt-3">{{ $message }}</span>
                                @enderror
                                <div class="col-md-12 mt-3">
                                    <label for="productDescribe" class="form-label">Mô tả sản phẩm</label>
                                    <textarea name="product[description]" id="productDescribe"
                                              placeholder="Nhập mô tả sản phẩm..." class="form-control ckeditor"
                                              cols="30"
                                              rows="10">{{ old('product.description', $product->description) }}</textarea>
                                </div>
                                <div class="col-12 mt-3 mb-3">
                                    <label for="productSize" class="form-label">Nhập thông tin biến thể</label>
                                    <div
                                        style="border: 1px solid #494949; padding: 20px 0 0 18px; border-radius: 5px; background-color: #27272714;">
                                        <div id="variantContainer">
                                            @if($product->variants->isNotEmpty())
                                                @php
                                                    // Lấy tất cả weight_id từ variants
                                                    $variantWeightIds = $product->variants->pluck('weight_id')->toArray();
                                                @endphp
                                                @foreach ($product->variants as $key => $variant)
                                                    <div class="variantColor d-flex align-items-center">
                                                        @if (!empty($variant->id))
                                                            <input type="hidden" name="productVariant[id][]" value="{{ $variant->id }}">
                                                        @endif
                                                        <div class="mb-3 rightInputColor"
                                                             style="margin-right: 18px;width: 190px !important;">
                                                            <select name="productVariant[weight_id][]"
                                                                    class="form-select weight_id">
                                                                <option value="">--Chọn trọng lượng (carat)--</option>
                                                                @foreach ($weights as $weight)
                                                                    <option
                                                                        @selected($variant->weight_id == $weight->id)
                                                                        @if(in_array($weight->id, $variantWeightIds))
                                                                            style="display: none;"
                                                                        @endif
                                                                        value="{{ $weight->id }}">{{ number_format($weight->weight, 2) }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('productVariant.weight_id.' . $key))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('productVariant.weight_id.' . $key) }}</span>
                                                            @endif
                                                        </div>

                                                        <div class="mb-3 rightInputColor"
                                                             style="margin-right: 18px; width: 200px;">
                                                            <input type="number" name="productVariant[import_price][]"
                                                                   class="form-control" placeholder="Nhập giá nhập..."
                                                                   value="{{ old('productVariant.import_price.' . $key, $variant->import_price) }}">
                                                            @if ($errors->has('productVariant.import_price.' . $key))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('productVariant.import_price.' . $key) }}</span>
                                                            @endif
                                                        </div>

                                                        <div class="mb-3 rightInputColor"
                                                             style="margin-right: 18px; width: 200px">
                                                            <input type="number" name="productVariant[selling_price][]"
                                                                   class="form-control" placeholder="Nhập giá bán..."
                                                                   value="{{ old('productVariant.selling_price.' . $key, $variant->selling_price) }}">
                                                            @if ($errors->has('productVariant.selling_price.' . $key))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('productVariant.selling_price.' . $key) }}</span>
                                                            @endif
                                                        </div>

                                                        <div class="mb-3 rightInputColor"
                                                             style="margin-right: 18px; width: 200px;">
                                                            <input type="number" name="productVariant[listed_price][]"
                                                                   class="form-control"
                                                                   placeholder="Nhập giá niêm yết..."
                                                                   value="{{ old('productVariant.listed_price.' . $key, $variant->listed_price) }}">
                                                            @if ($errors->has('productVariant.listed_price.' . $key))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('productVariant.listed_price.' . $key) }}</span>
                                                            @endif
                                                        </div>

                                                        <div class="mb-3 rightInputColor"
                                                             style="margin-right: 18px;width: 200px !important;">
                                                            <input type="number" name="productVariant[quantity][]"
                                                                   class="form-control" placeholder="Nhập số lượng..."
                                                                   value="{{ old('productVariant.quantity.' . $key, $variant->quantity) }}">
                                                            @if ($errors->has('productVariant.quantity.' . $key))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('productVariant.quantity.' . $key) }}</span>
                                                            @endif
                                                        </div>

                                                        <input type="button"
                                                               data-id="{{ $variant->id }}"
                                                               data-url="{{ route('products.removeVariant') }}"
                                                               class="mb-3 btn btn-outline-danger removeVariant"
                                                               value="Xóa" style="height: 37px;">
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="variantColor d-flex align-items-center">
                                                    <div class="mb-3 rightInputColor"
                                                         style="margin-right: 18px;width: 190px !important;">
                                                            <select name="productVariant[weight_id][]" class="form-select weight_id">
                                                            <option value="">--Chọn trọng lượng (carat)--</option>
                                                            @foreach ($weights as $weight)
                                                                <option
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
                                                        <input type="number" name="productVariant[import_price][]"
                                                               class="form-control" value="{{ old('productVariant.import_price.0') }}" placeholder="Nhập giá nhập...">
                                                        @if ($errors->has('productVariant.import_price.0'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('productVariant.import_price.0') }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="mb-3 rightInputColor"
                                                         style="margin-right: 18px; width: 200px">
                                                        <input type="number" name="productVariant[selling_price][]"
                                                               class="form-control" value="{{ old('productVariant.selling_price.0') }}" placeholder="Nhập giá bán...">
                                                        @if ($errors->has('productVariant.selling_price.0'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('productVariant.selling_price.0') }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="mb-3 rightInputColor"
                                                         style="margin-right: 18px; width: 200px;">
                                                        <input type="number" name="productVariant[listed_price][]"
                                                               class="form-control" value="{{ old('productVariant.listed_price.0') }}" placeholder="Nhập giá niêm yết...">
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
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="text-start">
                                        <a href="{{ route('products.index') }}" class="btn btn-outline-danger">Quay
                                            lại</a>
                                        <button type="button" id="addBienTheMauSac" class="btn btn-outline-info">Thêm
                                            biến thể
                                        </button>
                                        <button type="submit" id="btnAddProduct" class="btn btn-primary">Cập nhật sản
                                            phẩm
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="d-none">
                        <div class="variantColor d-flex align-items-center deleteNoData">
                            <div class="mb-3 rightInputColor"
                                 style="margin-right: 18px;width: 190px !important;">
                                <select name="productVariant[weight_id][]" class="form-select weight_id">
                                    <option value="">--Chọn trọng lượng (carat)--</option>
                                    @foreach ($weights as $weight)
                                        <option
                                            @selected(old('productVariant.weight_id.0') == $weight->id)
                                            value="{{ $weight->id }}">{{ number_format($weight->weight, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('productVariant.weight_id.0'))
                                    <span
                                        class="text-danger">{{ $errors->first('productVariant.weight.0') }}</span>
                                @endif
                            </div>

                            <div class="mb-3 rightInputColor"
                                 style="margin-right: 18px; width: 200px">
                                <input type="number" name="productVariant[listed_price][]"
                                       class="form-control" placeholder="Nhập giá niêm yết...">
                                @if ($errors->has('productVariant.listed_price.0'))
                                    <span
                                        class="text-danger">{{ $errors->first('productVariant.listed_price.0') }}</span>
                                @endif
                            </div>

                            <div class="mb-3 rightInputColor"
                                 style="margin-right: 18px; width: 200px;">
                                <input type="number" name="productVariant[import_price][]"
                                       class="form-control" placeholder="Nhập giá nhập...">
                                @if ($errors->has('productVariant.import_price.0'))
                                    <span
                                        class="text-danger">{{ $errors->first('productVariant.import_price.0') }}</span>
                                @endif
                            </div>

                            <div class="mb-3 rightInputColor"
                                 style="margin-right: 18px; width: 200px">
                                <input type="number" name="productVariant[selling_price][]"
                                       class="form-control" placeholder="Nhập giá bán...">
                                @if ($errors->has('productVariant.selling_price.0'))
                                    <span
                                        class="text-danger">{{ $errors->first('productVariant.selling_price.0') }}</span>
                                @endif
                            </div>

                            <div class="mb-3 rightInputColor"
                                 style="margin-right: 18px;width: 200px !important;">
                                <input type="number" name="productVariant[quantity][]"
                                       class="form-control" placeholder="Nhập số lượng...">
                                @if ($errors->has('productVariant.quantity.0'))
                                    <span
                                        class="text-danger">{{ $errors->first('productVariant.quantity.0') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $(document).on('click', '#btnDeleteImage', function () {
                const button = $(this);
                Swal.fire({
                    title: 'Bạn có muôn xóa ảnh sản phẩm không?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Huỷ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var id = button.data('id');
                        var image = button.data('image');
                        var url = button.data('url');
                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: {
                                id: id,
                                image: image, // Thêm tham số image nếu cần thiết cho logic xóa
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.successDeleteImage) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Xóa ảnh thành công.',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(function () {
                                        button.closest('.box_img').remove(); // Xóa phần tử cha của nút đã nhấn
                                    });
                                }
                            },
                            error: function (error) {
                                console.log(error);
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.removeVariant', function () {
                const button = $(this);
                Swal.fire({
                    title: 'Bạn có muốn xóa biến thể này không?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var id = button.data('id');
                        var url = button.data('url');
                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: {
                                id: id,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.successDeleteVariant) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Xóa biến thể thành công.',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(function () {
                                        // Tìm và xóa phần tử cha của nút đã nhấn
                                        button.closest('.variantColor').remove();

                                        // Lấy tất cả các lựa chọn đã chọn
                                        var selectedValues = Array.from(document.querySelectorAll('.weight_id')).map(function (select) {
                                            return select.value;
                                        });

                                        // Lấy tất cả các select có class weight_id
                                        var selects = document.querySelectorAll('.weight_id');

                                        selects.forEach(function (select) {
                                            var options = select.querySelectorAll('option');

                                            options.forEach(function (option) {
                                                // Nếu giá trị option đã được chọn, ẩn nó
                                                if (selectedValues.includes(option.value) && option.value !== "") {
                                                    option.style.display = 'none';  // Ẩn các option đã được chọn
                                                } else {
                                                    option.style.display = 'block'; // Hiện các option chưa được chọn
                                                }
                                            });
                                        });


                                        // Kiểm tra lại các option đã được chọn
                                        disableSelectedOptions();

                                        // Kiểm tra nếu không còn biến thể nào, thêm deleteNoData vào DOM
                                        const variantCount = $('.variantColor').length;
                                        if (variantCount === 0) {
                                            const deleteNoDataBox = document.querySelector('.deleteNoData');
                                            if (deleteNoDataBox) {
                                                const newDeleteNoDataBox = deleteNoDataBox.cloneNode(true);
                                                newDeleteNoDataBox.classList.remove('deleteNoData');
                                                newDeleteNoDataBox.style.display = 'block'; // Đảm bảo box hiện ra
                                                $('#variantContainer').append(newDeleteNoDataBox);
                                            }
                                        }
                                    });
                                }
                            },
                            error: function (error) {
                                console.log(error);
                            }
                        });
                    }
                });
            });

            function updateWeightOptions() {
                // Gọi API để lấy danh sách trọng lượng
                $.ajax({
                    url: '{{ route('weights.weights') }}',
                    method: 'GET',
                    success: function (weights) {
                        // Tạo một mảng để chứa tất cả các giá trị đã chọn
                        const existingWeights = [];

                        // Lấy tất cả các weight_id từ các ô select còn lại
                        $('.weight_id').each(function () {
                            const selectedValue = $(this).val();
                            if (selectedValue) {
                                existingWeights.push(selectedValue);
                            }
                        });

                        // Cập nhật lại các tùy chọn trong mỗi ô select
                        $('.weight_id').each(function () {
                            const select = $(this);
                            const currentValue = select.val(); // Lưu giá trị hiện tại
                            select.empty(); // Xóa tất cả các tùy chọn hiện tại
                            select.append('<option value="">--Chọn trọng lượng (carat)--</option>'); // Thêm tùy chọn mặc định

                            // Thêm lại tất cả các trọng lượng từ API
                            weights.forEach(function(weight) {
                                // Chuyển đổi và định dạng trọng lượng
                                const formattedWeight = parseFloat(weight.weight).toString().replace(/\.00$/, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                                // Thêm tùy chọn
                                const option = `<option value="${weight.id}" class="weight-option">${formattedWeight}</option>`;
                                select.append(option);
                            });

                            // Ẩn các tùy chọn đã chọn trong các ô khác
                            existingWeights.forEach(function(weightId) {
                                // Ẩn tùy chọn trong các ô khác
                                select.find(`option[value="${weightId}"]`).css('display', 'none'); // Ẩn tùy chọn đã chọn trong ô hiện tại
                            });

                            // Ẩn tùy chọn hiện tại
                            if (currentValue) {
                                select.find(`option[value="${currentValue}"]`).css('display', 'none'); // Ẩn chính tùy chọn hiện tại
                            }

                            // Nếu giá trị hiện tại không còn tồn tại, bỏ chọn
                            if (!existingWeights.includes(currentValue)) {
                                select.val(''); // Nếu không còn tồn tại, bỏ chọn
                            } else {
                                select.val(currentValue); // Đặt lại giá trị hiện tại cho ô select
                            }
                        });
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }
        });
    </script>
@endsection
