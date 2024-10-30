<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
</head>
<body>
    <h1>Cảm ơn bạn đã đặt hàng!</h1>
    <p>Thông tin đơn hàng của bạn:</p>
    <ul>
        <li>Mã đơn hàng: {{ $order->code }}</li>
        <li>Tên: {{ $order->name }}</li>
        <li>Địa chỉ: {{ $order->address }}</li>
        <li>Số điện thoại: {{ $order->phone }}</li>
        <li>Tổng giá: {{ number_format($order->total_price, 0, ',', '.') }} VNĐ</li>
        <li>Phương thức thanh toán: {{ $order->payment_method }}</li>
        <li>Trạng thái: {{ $order->status }}</li>
    </ul>

    <p>Cảm ơn bạn đã mua sắm cùng chúng tôi!</p>
</body>
</html>
