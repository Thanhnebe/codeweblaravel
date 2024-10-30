@extends('Admin.layouts.master')

@section('title')
    Danh sách Slider
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Danh sách Slider</h4>
                        </div><!-- end card header -->

                        <div class="card-body">
                            <div class="live-preview">
                                <div class="table-responsive table-card">
                                    <a href="{{ route('sliders.create') }}" class="btn btn-primary m-3">Thêm mới Slider</a>
                                    <table class="table align-middle table-nowrap table-striped-columns mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 46px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                           id="cardtableCheck">
                                                    <label class="form-check-label" for="cardtableCheck"></label>
                                                </div>
                                            </th>
                                            <th scope="col">STT</th>
                                            <th scope="col">Tiêu đề</th>
                                            <th scope="col">Hình ảnh</th>
                                            <th scope="col">Liên kết</th>
                                            <th scope="col" style="width: 150px;">Thao tác</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(!empty($data['sliders']))
                                            @foreach ($data['sliders'] as $key => $slider)
                                                <tr data-id-tr="{{ $slider->id }}">
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                   id="cardtableCheck{{ $slider->id }}">
                                                            <label class="form-check-label" for="cardtableCheck{{ $slider->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $slider->title }}</td>
                                                    <td>
                                                        <img src="{{ Storage::url($slider->image) }}" alt="{{ $slider->title }}" style="width: 100px; height: auto;">
                                                    </td>
                                                    <td>{{ $slider->link }}</td>
                                                    <td>
                                                        <a style="margin: 0 5px; cursor: pointer;"
                                                           href="{{ route('sliders.edit', $slider->id) }}"
                                                           class="link-primary"><i class="ri-settings-4-line"
                                                                                   style="font-size:18px;"></i></a>
                                                        <a style="margin: 0 5px; cursor: pointer;" class="link-danger"><i
                                                                class="ri-delete-bin-5-line" style="font-size:18px;"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#topmodal{{ $slider->id }}"></i></a>
                                                    </td>
                                                </tr>
                                                <div id="topmodal{{ $slider->id }}" class="modal fade" tabindex="-1"
                                                     aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-body text-center p-5">
                                                                <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json"
                                                                           trigger="loop"
                                                                           colors="primary:#f7b84b,secondary:#405189"
                                                                           style="width:130px;height:130px"></lord-icon>
                                                                <div class="mt-4">
                                                                    <h4 class="mb-3">Bạn muốn xóa Slider này?</h4>
                                                                    <p class="text-muted mb-4"> Bạn không thể hoàn tác dữ liệu này!</p>
                                                                    <div class="hstack gap-2 justify-content-center">
                                                                        <a href="javascript:void(0);"
                                                                           class="btn btn-link link-success fw-medium btnClose{{ $slider->id }}"
                                                                           data-bs-dismiss="modal"><i
                                                                                class="ri-close-line me-1 align-middle"></i>
                                                                            Hủy</a>
                                                                        <form action="{{ route('sliders.delete', $slider->id) }}" method="post">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                    class="btn btn-success btnDel">Xóa</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                    <div class="d-flex justify-content-center align-items-center">
                        <div>{{ $data['sliders']->links('pagination::bootstrap-4') }}</div>
                    </div>
                </div><!-- end col -->
            </div><!-- end row -->
        </div>
    </div> <!-- end col -->
@endsection

@section('script')
@endsection
