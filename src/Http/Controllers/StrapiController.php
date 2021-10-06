<?php

namespace Rapidez\Strapi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class StrapiController extends Controller
{
    public function __invoke($endpoint, $view, $getFirst = false)
    {
        $data = Cache::remember('strapi.'.$endpoint, config('strapi.cache'), function () {
            return json_decode(Http::get(config('strapi.url').'/'.$endpoint)->body());
        });

        abort_if(!$data, 404);

        if ($getFirst) {
            $data = $data[0];
        }

        return view('strapi.'.$view, compact('data'));
    }
}
