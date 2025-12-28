<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCallbackRequest;
use App\Models\CallbackRequest;

class CallbackRequestController extends Controller
{
    public function store(StoreCallbackRequest $request)
    {
        if ($request->filled('website')) {
            return response()->noContent();
        }
        CallbackRequest::create([
            'name'     => $request->input('name'),
            'phone'    => $request->input('phone'),
            'comment'  => $request->input('comment'),
            'page_url' => $request->input('page_url'),
            'utm'      => $request->input('utm'),
            'status'   => 'new',
        ]);

        return back()->with('success', 'Заявка отправлена! Мы скоро перезвоним.');
    }
}
