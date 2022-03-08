<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

if (! function_exists('strapi')) {
    function strapi($endpoint, $singleType = false, $abortWhenNotFound = true)
    {
        $params = [];

        if (str_contains($endpoint, '?')) {
            $filters = substr($endpoint, strpos($endpoint, '?') + 1);
            parse_str($filters, $params);
        }

        if (config('strapi.multisite') && !$singleType) {
            $params['_where[store][store_code]'] = config('rapidez.store_code');
        }

        $data = Cache::remember('strapi.'.$endpoint, config('strapi.cache'), function () use ($endpoint, $params) {
            $response = Http::get(config('strapi.url').'/'.$endpoint, $params);
            abort_if($response->failed(), 404);
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
