<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Illuminate\Support\Str;

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

if (! function_exists('strapi_image')) {
    function strapi_image($url, $size = "900x350")
    {
        $crop = Str::contains('?crop', $url) ? '?crop' : '';
        $imageUrl = config('strapi.url'). $url;
        $resizedPath = '/resizes/'.$size. str_replace('?crop', '', $url) . '.webp' . $crop;
        $file = file_get_contents($imageUrl);

        if (!is_dir(storage_path('app/public/'.pathinfo($resizedPath, PATHINFO_DIRNAME)))) {
            mkdir(storage_path('app/public/'.pathinfo($resizedPath, PATHINFO_DIRNAME)), 0755, true);
        }

        file_put_contents(storage_path('app/public/'.$resizedPath), $file);
        $stream = @fopen(storage_path('app/public/'.$resizedPath), 'r');
        $temporaryFile = tempnam(sys_get_temp_dir(), 'rapidez');
        file_put_contents($temporaryFile, $stream);
        $image = Image::load($temporaryFile)->optimize();
        @list($width, $height) = explode('x', $size);

        if ($height) {
            $image->fit(Str::contains('?crop', $resizedPath) ? MANIPULATIONS::FIT_CROP : MANIPULATIONS::FIT_CONTAIN, $width, $height);
        } else {
            $image->width($width);
        }

        $image->save(storage_path('app/public/'.$resizedPath));
        return $resizedPath;
    }
}
