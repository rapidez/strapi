<?php

return [
    'url' => env('STRAPI_URL'),

    'cache' => env('STRAPI_CACHE', 3600), // 1 hour

    // A paramgroup is useful so you do not have to repeat yourself,
    // choose a key which can be passed as a string to the strapi()
    // helper method and specify the parameters you'd like.
    // The value should be a config item as it will be
    // run through the config() helper method.
    'paramgroup' => [
        // 'default' => [
        //     'stores.store_code' => 'rapidez.store_code',
        // ],
    ],
];
