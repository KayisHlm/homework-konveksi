<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    //
    public function index(Orders $order)
    {
        $order->loadMissing('customer');

        return view('Page.Admin.OrderDetail.Index', compact('order'));
    }
}
