<?php

namespace App\Services;

use App\Models\CallbackRequest;
use Illuminate\Http\Request;

class CallbackRequestService
{
    /**
     * Создать заявку из запроса.
     */
    public function createFromRequest(Request $request): CallbackRequest
    {
        $utm = $this->collectUtmParams($request);

        return CallbackRequest::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'comment' => $request->input('comment'),
            'page_url' => $request->input('page_url') ?: $request->header('referer'),
            'utm' => ! empty($utm) ? $utm : null,
            'status' => 'new',
        ]);
    }

    /**
     * Собрать UTM-метки из запроса.
     *
     * @return array<string, string>
     */
    public function collectUtmParams(Request $request): array
    {
        $utmParams = [];

        if ($request->has('utm') && is_array($request->input('utm'))) {
            $utmParams = $request->input('utm');
        }

        $utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];
        foreach ($utmKeys as $key) {
            if ($request->has($key)) {
                $utmParams[$key] = $request->input($key);
            }
        }

        return array_filter($utmParams);
    }
}
