<?php

return [
    'route' => [
        'uri' => '/graphql',
        'middleware' => [
            'api',
            'auth:api',
        ],
    ],
];
