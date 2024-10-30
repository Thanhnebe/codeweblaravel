<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }
    public static function countPendingOrders()
    {
        return self::where('status', 'pending')->count();
    }
    public static function countActiveOrders()
    {
        return self::where('status', 'completed')->count();
    }
    public static function danggiao()
    {
        return self::where('status', 'shipping')->count();
    }
    public static function giaohuy()
    {
        return self::where('status', 'cancelled')->count();
    }
    public static function giaothatbai()
    {
        return self::where('status', 'failed')->count();
    }
    public static function giaothanhcong()
    {
        return self::where('status', 'completed')->count();
    }
    public function orderStatusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id', 'id');
    }
}
