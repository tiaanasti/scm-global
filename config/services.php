<?php

return [

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Open-Meteo API
    |--------------------------------------------------------------------------
    */

    'open_meteo' => [
        'forecast_url' => env(
            'OPEN_METEO_FORECAST_URL',
            'https://api.open-meteo.com/v1/forecast'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Exchange Rate API
    |--------------------------------------------------------------------------
    */

    'exchange_rate' => [
        'latest_url' => env(
            'EXCHANGE_RATE_LATEST_URL',
            'https://open.er-api.com/v6/latest/USD'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pengaturan API Eksternal
    |--------------------------------------------------------------------------
    */

    'external_api' => [
        'timeout' => (int) env('EXTERNAL_API_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | GNews API
    |--------------------------------------------------------------------------
    */

    'gnews' => [
        'api_key' => env('GNEWS_API_KEY'),

        'base_url' => env(
            'GNEWS_BASE_URL',
            'https://gnews.io/api/v4/search'
        ),

        'search_url' => env(
            'GNEWS_SEARCH_URL',
            'https://gnews.io/api/v4/search'
        ),

        'max_articles' => (int) env('GNEWS_MAX_ARTICLES', 5),

        'lang' => env('GNEWS_LANG', 'en'),
    ],

    /*
    |--------------------------------------------------------------------------
    | World Bank API
    |--------------------------------------------------------------------------
    */

    'world_bank' => [
        'base_url' => env(
            'WORLD_BANK_BASE_URL',
            'https://api.worldbank.org/v2'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | REST Countries API
    |--------------------------------------------------------------------------
    */

    'rest_countries' => [
        'api_key' => env('REST_COUNTRIES_API_KEY'),

        'base_url' => env(
            'REST_COUNTRIES_BASE_URL',
            'https://api.restcountries.com/countries/v5'
        ),

        'limit' => (int) env('REST_COUNTRIES_LIMIT', 100),
    ],
    'world_port_index' => [
    'arcgis_item_id' => env(
        'WORLD_PORT_INDEX_ARCGIS_ITEM_ID',
        '976ae810a25245228747b80191f625d0'
    ),

    'arcgis_items_url' => env(
        'WORLD_PORT_INDEX_ARCGIS_ITEMS_URL',
        'https://www.arcgis.com/sharing/rest/content/items'
    ),
],

];