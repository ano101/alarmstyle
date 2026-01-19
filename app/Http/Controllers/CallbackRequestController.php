<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCallbackRequest;
use App\Models\CallbackRequest;

class CallbackRequestController extends Controller
{
    public function store(StoreCallbackRequest $request)
    {
        // Honeypot защита от ботов
        if ($request->filled('website')) {
            return response()->noContent();
        }

        // Собираем UTM метки из запроса
        $utm = $this->collectUtmParams($request);

        CallbackRequest::create([
            'name'     => $request->input('name'),
            'phone'    => $request->input('phone'),
            'comment'  => $request->input('comment'),
            'page_url' => $request->input('page_url') ?: $request->header('referer'),
            'utm'      => !empty($utm) ? $utm : null,
            'status'   => 'new',
        ]);

        // Здесь можно добавить отправку уведомлений (email, Telegram и т.д.)
        // $this->sendNotification($callback);

        return back()->with('success', 'Заявка отправлена! Мы скоро перезвоним.');
    }

    /**
     * Собираем UTM метки из запроса
     */
    protected function collectUtmParams(StoreCallbackRequest $request): array
    {
        $utmParams = [];

        // Если UTM пришли из формы
        if ($request->has('utm') && is_array($request->input('utm'))) {
            $utmParams = $request->input('utm');
        }

        // Дополнительно проверяем query параметры
        $utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];
        foreach ($utmKeys as $key) {
            if ($request->has($key)) {
                $utmParams[$key] = $request->input($key);
            }
        }

        return array_filter($utmParams);
    }
}
