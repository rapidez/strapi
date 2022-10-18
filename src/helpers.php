<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

if (! function_exists('strapi')) {
    function strapi($endpoint, $params = 'default', $abortWhenNotFound = true, $requestedSingle = false)
    {
        if (is_string($params)) {
            $params = collect(config('strapi.paramgroup.'.$params))
                ->map(fn ($value) => config($value))
                ->toArray();
        }

        $cacheKey = 'strapi.'.$endpoint.'.'.json_encode($params);

        if (str_contains($endpoint, '?')) {
            if(preg_match_all('/(\?|\&)(?<key>[^=]+)\=(?<value>[^&]+)/', $endpoint, $matches)) {
                $params = array_merge(array_combine($matches['key'], $matches['value']), $params);
            }
        }

        $data = Cache::remember($cacheKey, config('strapi.cache'), function () use ($endpoint, $params, $abortWhenNotFound) {
            $baseUrl = config('strapi.url') . (config('strapi.version') == '3' ?: '/api');
            $response = Http::get($baseUrl.'/'.$endpoint, $params);
            abort_if($response->failed() && $abortWhenNotFound, 404);
            return json_decode($response->body());
        });

        abort_if((config('strapi.version') == '3' && !$data) || (!$data->data && config('strapi.version') == '4') && $abortWhenNotFound, 404);

        return config('strapi.version') == "3" ? $data : ($requestedSingle ? collect($data->data)->first()->attributes : collect($data->data));
    }
}

if (! function_exists('strapi_image')) {
    function strapi_image($image, $size = null)
    {
        if (!$image && config('strapi.version') == '3' || (!$image->data && config('strapi.version') == '4')) {
            return null;
        }

        if (!$size) {
            return config('strapi.url').(config('strapi.version') == "3" ? $image->url : $image->data->url);
        }

        return config('strapi.url').($image->formats->{$size}->url ?? $image->url);
    }
}
