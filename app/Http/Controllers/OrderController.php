<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        Log::info('Order data received:', $request->validated());

        Order::query()->create($request->validated());

        return back()->with('success', 'Заказ успешно создан! Мы свяжемся с вами в ближайшее время.');
    }
}
