@extends('Admin.layouts.master')

@section('title')
    Danh sách sản phẩm
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm</h4>
                        </div><!-- end card header -->

                        <div class="card-body">
                            <div class="live-preview">
                                <div class="table-responsive table-card">
                                    <a href="{{ route('products.create') }}" class="btn btn-primary m-3">Thêm mới sản phẩm</a>
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
                                                <th scope="col">Ảnh</th>
                                                <th scope="col">Tên</th>
                                                <th scope="col">Biến thể</th>
                                                <th scope="col">Ngày đăng</th>
                                                <th scope="col" style="width: 150px;">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($listProduct as $key => $pro)
                                                <tr data-id-tr="{{ $pro->id }}">
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="cardtableCheck03">
                                                            <label class="form-check-label" for="cardtableCheck03"></label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td><img src="{{ explode(',', $pro->image)[0] }}" width="100px"
                                                            alt=""></td>
                                                    <td>{{ $pro->name }}</td>
                                                    <td>
                                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                                                data-bs-target="#variant_{{ $pro->id }}">Xem biến thể</button>
                                                    </td>
                                                    <td>{{ date('d-m-Y | H:i:s', strtotime($pro->created_at)) }}</span></td>
                                                    <td>
                                                        <a style="margin: 0 5px;"
                                                            href="{{ route('products.edit', $pro->id) }}"
                                                            class="link-primary"><i class="ri-settings-4-line"
                                                                style="font-size:18px;"></i></a>
                                                        <a style="margin: 0 5px; cursor: pointer;" class="link-danger"><i
                                                                class="ri-delete-bin-5-line" style="font-size:18px;"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#topmodal{{ $pro->id }}"></i></a>
                                                    </td>
                                                    <td>
                                                        <div id="variant_{{ $pro->id }}" class="modal fade fadeInLeft" tabindex="-1"
                                                             aria-hidden="true" style="display: none;">
                                                            <div class="modal-dialog" style="max-width: 800px !important;">
                                                                <div class="modal-content">
                                                                    <div class="modal-body text-center p-5">
                                                                        <div class="mt-4">
                                                                            <h4 class="mb-3">Thông tin biến thể của sản phẩm</h4>
                                                                            <h5 class="mb-3">'{{ $pro->name }}'</h5>
                                                                            <div class="hstack gap-2 justify-content-center">
                                                                                <table class="table table-bordered">
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <td>#</td>
                                                                                        <td>Khối lượng</td>
                                                                                        <td>Giá nhập</td>
                                                                                        <td>Giá bán</td>
                                                                                        <td>Giá niêm yết</td>
                                                                                        <td>Số lượng</td>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                    @if(!empty($pro->variants))
                                                                                        @foreach($pro->variants as $key => $var)
                                                                                            <tr>
                                                                                                <td>{{ $key + 1 }}</td>
                                                                                                <td>
                                                                                                    @if(!empty($var->weight))
                                                                                                        {{ number_format($var->weight->weight, 2) . ' ' . $var->weight->unit }}
                                                                                                    @endif
                                                                                                </td>
                                                                                                <td>{{ number_format($var->import_price, 0, ',', '.') }} VNĐ</td>
                                                                                                <td>{{ number_format($var->selling_price, 0, ',', '.') }} VNĐ</td>
                                                                                                <td>{{ number_format($var->listed_price, 0, ',', '.') }} VNĐ</td>
                                                                                                <td>{{ $var->quantity }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    @endif
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                                                                    </div>
                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->
                                                    </td>
                                                </tr>


                                                <div id="topmodal{{ $pro->id }}" class="modal fade" tabindex="-1"
                                                     aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-body text-center p-5">
                                                                <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json"
                                                                           trigger="loop"
                                                                           colors="primary:#f7b84b,secondary:#405189"
                                                                           style="width:130px;height:130px"></lord-icon>
                                                                <div class="mt-4">
                                                                    <h4 class="mb-3">Bạn muốn xóa sản phẩm?</h4>
                                                                    <h5 class="mb-3">'{{ $pro->name }}'</h5>
                                                                    <p class="text-muted mb-4"> Nó sẽ bị xóa vĩnh viễn khỏi
                                                                        website của bạn</p>
                                                                    <div class="hstack gap-2 justify-content-center">
                                                                        <a href="javascript:void(0);"
                                                                           class="btn btn-link link-success fw-medium btnClose{{ $pro->id }}"
                                                                           data-bs-dismiss="modal"><i
                                                                                class="ri-close-line me-1 align-middle"></i>
                                                                            Hủy</a>
                                                                        <a data-product-id="{{ $pro->id }}"
                                                                           class="btn btn-success btnDel">Xóa</a>
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
                        <div>{{ $listProduct->links('pagination::bootstrap-4') }}</div>
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
            let formData = new FormData();

            btnDels.forEach(btnDel => {
                btnDel.addEventListener('click', function() {
                    let productId = this.dataset.productId;

                    formData.append('_token', '{{ csrf_token() }}');

                    let urlDelete = `/admin/products/delete/${productId}`;

                    $.ajax({
                        url: urlDelete,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.successDeleteProduct) {
                                let trElement = document.querySelector(
                                    `tr[data-id-tr="${productId}"]`);
                                let btnClose = document.querySelector(
                                    `.btnClose${productId}`);

                                btnClose.click();
                                trElement.remove();

                                Swal.fire({
                                    position: "top-end",
                                    icon: "success",
                                    title: `Xóa sản phẩm thành công`,
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                            } else {
                                if(response.errorProduct) {
                                    let trElement = document.querySelector(
                                        `tr[data-id-tr="${productId}"]`);
                                    let btnClose = document.querySelector(
                                        `.btnClose${productId}`);

                                    btnClose.click();
                                    trElement.remove();

                                    Swal.fire({
                                        position: "top-end",
                                        icon: "error",
                                        title: `Sản phẩm không tồn tại`,
                                        showConfirmButton: false,
                                        timer: 2500
                                    });
                                }
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
