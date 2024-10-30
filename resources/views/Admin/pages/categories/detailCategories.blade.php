@extends('Admin.layouts.master')

@section('title')
    Chi tiết danh mục
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Chi tiết của danh mục: {{ $categoryById->category_name }}</h4>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <td scope="col">{{ $categoryById->id }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="col">Tên danh mục</th>
                                            <td scope="col">{{ $categoryById->name }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="col">Ngày đăng</th>
                                            <td scope="col">{{ date('d-m-Y | H:i:s', strtotime($categoryById->created_at)) }}</td>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="col-12 mt-3">
                                    <div class="text-start">
                                        <a href="{{ route('categories.listCategories') }}" class="btn btn-outline-danger">Quay lại</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end card-body -->
                </div>
                <!-- end card -->
            </div>
            <!-- end col -->
        </div>
    </div>
@endsection

@section('script')

@endsection
