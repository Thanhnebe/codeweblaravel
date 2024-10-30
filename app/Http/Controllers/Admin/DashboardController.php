<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalPendingOrders = Order::countPendingOrders();
        $totalCompletedOrders = Order::countActiveOrders();
        $danggiao = Order::danggiao();
        $giaohuy = Order::giaohuy();
        $giaothatbai = Order::giaothatbai();
        $giaothanhcong = Order::giaothanhcong();
        return view('Admin.pages.dashboard', compact('totalOrders', 'totalUsers', 'totalPendingOrders', 'totalCompletedOrders', 'danggiao', 'giaothanhcong', 'giaothatbai', 'giaohuy'));
    }
}
