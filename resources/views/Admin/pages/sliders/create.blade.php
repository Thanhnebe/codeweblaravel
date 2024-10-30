@extends('Admin.layouts.master')

@section('title')
    Thêm mới Slider
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Thêm mới Slider</h4>
                    </div>
                    <!-- end card header -->

                    <div class="card-body">
                        <form action="{{ route('sliders.store') }}" enctype="multipart/form-data" method="post">
                            @csrf
                            @method('POST')
                            <div class="live-preview">
                                <div class="col-md-12 mb-3">
                                    <label for="title" class="form-label">Tiêu đề</label>
                                    <input type="text" value="{{ old('title') }}" class="form-control" name="title"
                                           id="title" placeholder="Nhập tiêu đề cho slider...">
                                    @error('title')
                                    <span class="text-danger mt-3">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="image" class="form-label">Hình ảnh</label>
                                    <input type="file" class="form-control" name="image" id="image" accept="image/*">
                                    @error('image')
                                    <span class="text-danger mt-3">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="link" class="form-label">Liên kết</label>
                                    <input type="url" value="{{ old('link') }}" class="form-control" name="link"
                                           id="link" placeholder="Nhập liên kết cho slider (nếu có)">
                                    @error('link')
                                    <span class="text-danger mt-3">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="text-start">
                                        <button type="submit" class="btn btn-primary">Thêm Slider</button>
                                        <a href="{{ route('sliders.index') }}" class="btn btn-outline-warning">Quay lại</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
@endsection

@section('script')
@endsection
