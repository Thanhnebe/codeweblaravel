<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Status Update')
            ->line('Đơn hàng của bạn đã được cập nhật.')
            ->line('Mã đơn hàng: ' . $this->order->code)
            ->line('Trạng thái đơn hàng mới: ' . $this->getOrderStatusMessage($this->order->status))
            ->action('Xem đơn hàng', url('/orders/' . $this->order->id))
            ->line('Cảm ơn bạn đã sử dụng website chúng tôi!');
    }

    protected function getOrderStatusMessage($status)
    {
        switch ($status) {
            case 'pending':
                return 'Đang chờ xác nhận';
            case 'completed':
                return 'Hoàn thành';
            case 'confirmed':
                return 'Đã xác nhận';
            case 'cancelled':
                return 'Đã hủy';
            case 'shipping':
                return 'Đang giao';
            default:
                return 'Không xác định';
        }
    }
}
