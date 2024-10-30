@extends('Admin.layouts.master')

@section('title')
    Cập nhật danh mục
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Cập nhật danh mục</h4>
                    </div>
                    <!-- end card header -->

                    <div class="card-body">
                        <div class="live-preview">
                            <div class="col-md-12">
                                <label for="categoryInput" class="form-label">Tên danh mục</label>
                                <input type="text" class="form-control" value="{{ $categoryById->name }}"
                                    name="categoriesName" id="categoryInput" placeholder="Nhập tên danh mục...">
                            </div>
                            <div class="col-12 mt-3">
                                <div class="text-start">
                                    <button type="button" id="btnUpdateCategories" name="insertCategory"
                                        class="btn btn-primary">Cập nhật danh
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
            let btnUpdateCategories = document.querySelector('#btnUpdateCategories');

            btnUpdateCategories.addEventListener('click', function() {
                let categoryName = document.querySelector('#categoryInput');

                let data = {
                    name: categoryName.value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: '{{ route("categories.updateCategories", ["id" => $id]) }}',
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(response) {
                        if (response.messageUpdate) {
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: `Cập nhật danh mục thành công`,
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
                            title: `Lỗi không thể cập nhật`,
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
