<?php

return [
    'api' => [
        /*
        |--------------------------------------------------------------------------
        | Edit to set the api's title
        |--------------------------------------------------------------------------
        */

        'title' => 'Test Task Api',
    ],

    'routes' => [
        /*
        |--------------------------------------------------------------------------
        | Route for accessing api documentation interface
        |--------------------------------------------------------------------------
        */

        'api' => 'api/doc',

        /*
        |--------------------------------------------------------------------------
        | Route for accessing parsed swagger annotations.
        |--------------------------------------------------------------------------
        */

        'docs' => 'api/build',

        /*
        |--------------------------------------------------------------------------
        | Route for Oauth2 authentication callback.
        |--------------------------------------------------------------------------
        */

        'oauth2_callback' => 'api/oauth2-callback',

        /*
        |--------------------------------------------------------------------------
        | Middleware allows to prevent unexpected access to API documentation
        |--------------------------------------------------------------------------
         */
        'middleware'      => [
            'api'             => [],
            'asset'           => [],
            'docs'            => [],
            'oauth2_callback' => [],
        ],
    ],

    'paths'    => [
        /*
        |--------------------------------------------------------------------------
        | Absolute path to location where parsed swagger annotations will be stored
        |--------------------------------------------------------------------------
        */

        'docs' => storage_path('api-docs'),

        /*
        |--------------------------------------------------------------------------
        | File name of the generated json documentation file
        |--------------------------------------------------------------------------
        */

        'docs_json' => 'api-docs.json',

        /*
        |--------------------------------------------------------------------------
        | Absolute path to directory containing the swagger annotations are stored.
        |--------------------------------------------------------------------------
        */

        'annotations' => base_path('app/Api/*/Http/Controllers'),

        /*
        |--------------------------------------------------------------------------
        | Absolute path to directory where to export views
        |--------------------------------------------------------------------------
        */

        'views' => base_path('resources/views/vendor/l5-swagger'),

        /*
        |--------------------------------------------------------------------------
        | Edit to set the api's base path
        |--------------------------------------------------------------------------
        */

        'base' => env('L5_SWAGGER_BASE_PATH', null),

        /*
        |--------------------------------------------------------------------------
        | Absolute path to directories that you would like to exclude from swagger generation
        |--------------------------------------------------------------------------
        */

        'excludes' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | API security definitions. Will be generated into documentation file.
    |--------------------------------------------------------------------------
    */
    'security' => [
        /*
        |--------------------------------------------------------------------------
        | Examples of Security definitions
        |--------------------------------------------------------------------------
        */

        'auth' => [ // Unique name of security
                    'in'          => 'header', // The location of the API key. Valid values are "query" or "header".
                    'type'        => 'apiKey', // The type of the security scheme. Valid values are "basic", "apiKey" or "oauth2".
                    'name'        => 'Authorization', // The name of the header or query parameter to be used.
                    'description' => 'A value of the authorization header has to starts with \'Bearer \' and then your access_token.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Turn this off to remove swagger generation on production
    |--------------------------------------------------------------------------
    */

    'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

    /*
    |--------------------------------------------------------------------------
    | Edit to set the swagger version number
    |--------------------------------------------------------------------------
    */

    'swagger_version' => env('SWAGGER_VERSION', '3.0'),

    /*
    |--------------------------------------------------------------------------
    | Edit to trust the proxy's ip address - needed for AWS Load Balancer
    |--------------------------------------------------------------------------
    */

    'proxy' => false,

    /*
    |--------------------------------------------------------------------------
    | Configs plugin allows to fetch external configs instead of passing them to SwaggerUIBundle.
    | See more at: https://github.com/swagger-api/swagger-ui#configs-plugin
    |--------------------------------------------------------------------------
    */

    'additional_config_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Apply a sort to the operation list of each API. It can be 'alpha' (sort by paths alphanumerically),
    | 'method' (sort by HTTP method).
    | Default is the order returned by the server unchanged.
    |--------------------------------------------------------------------------
    */

    'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

    /*
    |--------------------------------------------------------------------------
    | Uncomment to pass the validatorUrl parameter to SwaggerUi init on the JS
    | side.  A null value here disables validation.
    |--------------------------------------------------------------------------
    */

    'validator_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Uncomment to add constants which can be used in anotations
    |--------------------------------------------------------------------------
     */
    'constants'     => [
    ],
];
