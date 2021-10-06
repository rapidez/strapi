<?php

namespace Rapidez\Strapi\ViewDirectives;

use Illuminate\Support\Facades\View;

class DynamiczoneDirective
{
    public function render($components)
    {
        $html = '';

        foreach ($components as $component) {
            $view = 'strapi.'.$component->__component;

            if (View::exists($view)) {
                $html .= view($view, ['data' => $component]);
            } elseif (!app()->environment('production')) {
                $html .= '<hr>'.__('View not found (:view).', compact('view')).'<hr>';
            }
        }

        return $html;
    }
}
