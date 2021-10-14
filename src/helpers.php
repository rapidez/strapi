<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

if (! function_exists('strapi')) {
    function strapi($endpoint)
    {
        $data = Cache::remember('strapi.'.$endpoint, config('strapi.cache'), function () use ($endpoint) {
            return json_decode(Http::get(config('strapi.url').'/'.$endpoint)->body());
        });

        abort_if(!$data, 404);

        return $data;
    }
}
