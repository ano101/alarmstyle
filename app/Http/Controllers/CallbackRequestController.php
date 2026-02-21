<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCallbackRequest;
use App\Services\CallbackRequestService;

class CallbackRequestController extends Controller
{
    public function __construct(
        protected CallbackRequestService $callbackRequestService,
    ) {}

    public function store(StoreCallbackRequest $request)
    {
        // Honeypot защита от ботов
        if ($request->filled('website')) {
            return response()->noContent();
        }

        $this->callbackRequestService->createFromRequest($request);

        return back()->with('success', 'Заявка отправлена! Мы скоро перезвоним.');
    }
}
