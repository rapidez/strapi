<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

if (! function_exists('strapi')) {
    function strapi($endpoint, $abortWhenNotFound = true)
    {
        $data = Cache::remember('strapi.'.$endpoint, config('strapi.cache'), function () use ($endpoint) {
            return json_decode(Http::get(config('strapi.url').'/'.$endpoint)->body());
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
