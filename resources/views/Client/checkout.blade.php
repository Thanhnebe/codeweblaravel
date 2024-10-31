<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán đơn hàng</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h2>Thông tin thanh toán</h2>

    <form id="paymentForm">
        <!-- Thông tin khách hàng -->
        <input type="hidden" name="user_id" value="1">
        <input type="hidden" name="total_price" value="1000000">
        <input type="hidden" name="code" value="10">
        <div>
            <label for="name">Email:</label>
            <input type="text" name="email" value="k40modgame@gmail.com" required>
        </div>
        <div>
            <label for="name">Tên khách hàng:</label>
            <input type="text" name="name" value="Nguyễn Văn A" required>
        </div>
    
        <div>
            <label for="address">Địa chỉ:</label>
            <input type="text" name="address" value="123 Đường ABC, Quận 1" required>
        </div>
    
        <div>
            <label for="phone">Số điện thoại:</label>
            <input type="text" name="phone" value="0901234567" required>
        </div>
    
        <!-- Thông tin sản phẩm -->
        <div>
            <h3>Sản phẩm</h3>
            <div class="product">
                <label for="product_1">Sản phẩm 1:</label>
                <input type="hidden" name="products[0][product_id]" value="1">
                <input type="hidden" name="products[0][variant_id]" value="1">
                <input type="hidden" name="products[0][price]" value="500000">
                <input type="hidden" name="products[0][quantity]" value="2">
            </div>
    
            <div class="product">
                <label for="product_2">Sản phẩm 2:</label>
                <input type="hidden" name="products[1][product_id]" value="2">
                <input type="hidden" name="products[1][variant_id]" value="2">
                <input type="hidden" name="products[1][price]" value="500000">
                <input type="hidden" name="products[1][quantity]" value="1">
            </div>
        </div>
    
        <!-- Chọn phương thức thanh toán -->
        <div>
            <label for="payment_method">Phương thức thanh toán:</label><br>
            <input type="radio" name="payment_method" value="cod" checked> Thanh toán khi nhận hàng (COD)<br>
            <input type="radio" name="payment_method" value="vnpay"> Thanh toán qua VNPay<br>
        </div>
    
        <!-- Nút chọn thanh toán -->
        <button type="button"data-url="{{ route('orders.storeOrder') }}" id="paymentButton">Thanh toán</button>
    </form>
    

    <script>
        $(document).ready(function() {
            // Set up AJAX to include CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    
            $('#paymentButton').click(function(e) {
                e.preventDefault();
    
                var url = $(this).data('url');
    
                // Serialize form data
                var formData = $('#paymentForm').serialize();
    
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            if (response.payment_method === 'vnpay') {
                                window.location.href = response.vnpay_url;
                            } else {
                                alert(response.message);
                                window.location.href = "/success"; // Redirect after successful COD payment
                            }
                        } else {
                            alert('Payment failed: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            });
        });
    </script>
    

</body>
</html>
