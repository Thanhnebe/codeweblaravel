@extends('Admin.layouts.master')

@section('title')
    Danh sách danh mục
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Danh sách danh mục</h4>
                        </div><!-- end card header -->

                        <div class="card-body">
                            <div class="live-preview">
                                <div class="table-responsive table-card">
                                    <a href="{{ route('categories.createCategories') }}" class="btn btn-primary m-3">Thêm mới danh mục</a>
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
                                                <th scope="col">Tên danh mục</th>
                                                <th scope="col">Ngày đăng</th>
                                                <th scope="col" style="width: 150px;">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($listCategories as $key => $cate)
                                                <tr data-id-tr="{{ $cate->id }}">
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="cardtableCheck03">
                                                            <label class="form-check-label" for="cardtableCheck03"></label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $cate->name }}</td>
                                                    <td>{{ date('d-m-Y | H:i:s', strtotime($cate->created_at)) }}</td>
                                                    <td>
                                                        <a style="margin: 0 5px; cursor: pointer;"
                                                            href="/admin/categories/edit/{{ $cate->id }}"
                                                            class="link-primary"><i class="ri-settings-4-line"
                                                                style="font-size:18px;"></i></a>
                                                        <a style="margin: 0 5px; cursor: pointer;" class="link-danger"><i
                                                                class="ri-delete-bin-5-line" style="font-size:18px;"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#topmodal{{ $cate->id }}"></i></a>
                                                    </td>
                                                </tr>
                                                <div id="topmodal{{ $cate->id }}" class="modal fade" tabindex="-1"
                                                    aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-body text-center p-5">
                                                                <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json"
                                                                    trigger="loop"
                                                                    colors="primary:#f7b84b,secondary:#405189"
                                                                    style="width:130px;height:130px"></lord-icon>
                                                                <div class="mt-4">
                                                                    <h4 class="mb-3">Bạn muốn xóa danh mục
                                                                        '{{ $cate->category_name }}' ?</h4>
                                                                    <p class="text-muted mb-4"> Nó sẽ bị xóa vĩnh viễn khỏi
                                                                        website của bạn</p>
                                                                    <div class="hstack gap-2 justify-content-center">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-link link-success fw-medium btnClose{{ $cate->id }}"
                                                                            data-bs-dismiss="modal"><i
                                                                                class="ri-close-line me-1 align-middle"></i>
                                                                            Hủy</a>
                                                                        <a data-category-slug="{{ $cate->slug }}" data-category-id="{{ $cate->id }}"
                                                                            class="btn btn-success btnDel">Xóa</a>
                                                                        {{-- href="/admin/categories/delete/{{ $cate->category_id }}" --}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
                                            @endforeach

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                    <div class="d-flex justify-content-center align-items-center">
                        <div>{{ $listCategories->links('pagination::bootstrap-4') }}</div>
                    </div>
                </div><!-- end col -->
            </div><!-- end row -->
        </div>
    </div> <!-- end col -->
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let btnDels = document.querySelectorAll('.btnDel');
            let formData = new FormData(); // Khai báo formData

            btnDels.forEach(btnDel => {
                btnDel.addEventListener('click', function() {
                    let categoryId = this.dataset.categoryId;
                    formData.append('_token', '{{ csrf_token() }}');

                    let urlDelete = `/admin/categories/delete/${categoryId}`;

                    $.ajax({
                        url: urlDelete,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.messageDeleteCategories) {
                                let trElement = document.querySelector(
                                    `tr[data-id-tr="${categoryId}"]`);
                                let btnClose = document.querySelector(
                                    `.btnClose${categoryId}`);

                                btnClose.click();
                                trElement.remove();

                                Swal.fire({
                                    position: "top-end",
                                    icon: "success",
                                    title: `Xóa danh mục thành công`,
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
                                title: `Lỗi không thể xóa`,
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection
