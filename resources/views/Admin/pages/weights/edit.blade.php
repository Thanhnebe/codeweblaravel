@extends('Admin.layouts.master')

@section('title')
    Cập nhật trọng lượng
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Cập nhật trọng lượng</h4>
                    </div>
                    <!-- end card header -->

                    <div class="card-body">
                        <form action="{{  route('weights.update', $data['weight']->id)}}" enctype="multipart/form-data" method="post">
                            @csrf
                            @method('POST')
                            <div class="live-preview">
                                <div class="col-md-12 mb-3">
                                    <label for="categoryInput" class="form-label">Trọng lương</label>
                                    <input type="number" value="{{ old('weight', $data['weight']->weight) }}" step="0.01" class="form-control weight" name="weight"
                                           id="weight" placeholder="Nhập trọng lượng...">
                                    @error('weight')
                                    <span class="text-danger mt-3">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="categoryInput" class="form-label">Đơn vị trọng lượng</label>
                                    <div class="mt-2">
                                        <select name="unit" id="unit" class="form-select">
                                            <option value="" selected>--Chọn--</option>
                                            <option value="carat" selected>carat</option>
                                        </select>
                                    </div>
                                    @error('unit')
                                    <span class="text-danger mt-3">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="text-start">
                                        <button type="submit" class="btn btn-primary insertCategory">Cập nhật trọng lượng</button>
                                        <a href="{{ route('weights.index') }}" class="btn btn-outline-warning">Quay lại</a>
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
