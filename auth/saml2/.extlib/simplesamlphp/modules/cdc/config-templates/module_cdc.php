<?php

$config = [
    'example.org' => [
        /*
         * The shared key for this CDC server.
         */
        'key' => 'ExampleSharedKey',

        /*
         * The URL to the server script.
         */
        'server' => 'https://my-cdc.example.org/simplesaml/module.php/cdc/server.php',

        /*
         * The lifetime of our cookie, in seconds.
         *
         * If this is 0, the cookie will expire when the browser is closed.
         */
        'cookie.lifetime' => 0,
    ],
];
