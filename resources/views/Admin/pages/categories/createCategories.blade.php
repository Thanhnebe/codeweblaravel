@extends('Admin.layouts.master')

@section('title')
    Thêm mới danh mục
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Thêm mới danh mục</h4>
                    </div>
                    <!-- end card header -->

                    <div class="card-body">
                        <div class="live-preview">
                            <div class="col-md-12">
                                <label for="categoryInput" class="form-label">Tên danh mục</label>
                                <input type="text" class="form-control categoriesName" name="name"
                                    id="categoryInput" placeholder="Nhập tên danh mục...">
                            </div>
                            <div class="col-12 mt-3">
                                <div class="text-start">
                                    <button type="button" id="btnAddCate" name="insertCategory"
                                        class="btn btn-primary insertCategory">Thêm danh
                                        mục</button>
                                    <a href="/admin/categories/" class="btn btn-outline-warning">Quay lại</a>
                                </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            let btnInsertCategory = document.querySelector('#btnAddCate');

            btnInsertCategory.addEventListener('click', function() {
                let categoryName = document.querySelector('.categoriesName');
                let statusCategories = document.querySelectorAll('.statusCategories');

                let data = {
                    name: categoryName.value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: `{{ route('categories.storeCategories') }}`,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.messageCreate) {
                            categoryName.value = "";
                            statusCategories.forEach(item => {
                                if (item.checked) {
                                    item.checked = false;
                                }
                            });
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: `Thêm mới danh mục thành công`,
                                showConfirmButton: false,
                                timer: 2500
                            });
                        } else {
                            Swal.fire({
                                position: "top-end",
                                icon: "warning",
                                title: `Đã xảy ra lỗi`,
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: `Lỗi không thể thêm`,
                            text: `${xhr.responseJSON.message}`,
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                });
            });
        });
    </script>
@endsection
