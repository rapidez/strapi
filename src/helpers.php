<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

if (! function_exists('strapi')) {
    function strapi($endpoint, $params = 'default', $abortWhenNotFound = true)
    {
        if (is_string($params)) {
            $params = collect(config('strapi.paramgroup.'.$params))
                ->map(fn ($value) => config($value))
                ->toArray();
        }

        $cacheKey = 'strapi.'.$endpoint.'.'.json_encode($params);

        if (str_contains($endpoint, '?')) {
            parse_str(parse_url($endpoint, PHP_URL_QUERY), $parsedParams);
            $params = array_merge($parsedParams, $params);
        }

        $data = Cache::remember($cacheKey, config('strapi.cache'), function () use ($endpoint, $params, $abortWhenNotFound) {
            $response = Http::get(config('strapi.url').'/'.$endpoint, $params);
            abort_if($response->failed() && $abortWhenNotFound, 404);
            return json_decode($response->body());
        });

        abort_if(!$data && $abortWhenNotFound, 404);

        return $data;
    }
}

if (! function_exists('strapi_image')) {
    function strapi_image($image, $size = null)
    {
        if (!$image) {
            return null;
        }

        if (!$size) {
            return config('strapi.url').$image->url;
        }

        return config('strapi.url').($image->formats->{$size}->url ?? $image->url);
    }
}
