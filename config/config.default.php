<?php

return [
    //Phalcon default settings
    'baseUri' => '/',
    'debug' => 1,
    'staticBaseUri' => 'http://wscn.cdn.wallstreetcn.com',
    //'staticBaseUri' => '/static',
    'staticBaseUriVersionFile' => __DIR__ . '/../.git/HEAD',
    'domains' => [

    ],
    'cache' => [
        'enable' => false,
        'fastCache' => [
            'enable' => true,
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 1,
        ],
        'globalCache' => [
            'enable' => true,
            'frontend' => [
                'adapter' => 'Data',
                'options' => [],
            ],
            'backend' => [
                'adapter' => 'File',
                'options' => [
                    'cacheDir' => __DIR__ . '/../cache/global/',
                ],
            ],
        ],
        'viewCache' => [
            'enable' => true,
            'frontend' => [
                'adapter' => 'Output',
                'options' => [],
            ],
            'backend' => [
                'adapter' => 'File',
                'options' => [
                    'cacheDir' => __DIR__ . '/../cache/view/',
                ],
            ],
        ],
        'modelsCache' => [
            'enable' => true,
            'frontend' => [
                'adapter' => 'Data',
                'options' => [],
            ],
            'backend' => [
                'adapter' => 'File',
                'options' => [
                    'cacheDir' => __DIR__ . '/../cache/model/',
                ],
            ],
        ],
        'apiCache' => [
            'enable' => true,
            'frontend' => [
                'adapter' => 'Json',
                'options' => [],
            ],
            'backend' => [
                'adapter' => 'File',
                'options' => [
                    'cacheDir' => __DIR__ . '/../cache/api/',
                ],
            ],
        ],
    ],
    'tokenStorage' => [
        'enable' => true,
        'frontend' => [
            'adapter' => 'Data',
            'options' => [
                'lifetime' => 3600 * 24 * 30    // one month
            ],
        ],
        'backend' => [
            'adapter' => 'File',
            'options' => [
                'cacheDir' => __DIR__ . '/../cache/token/',
            ],
        ],
    ],
    'session' => [
        'session_name' => 'WSCN_SESSID',
        'adapter' => 'files',
        'options' => [
            'lifetime' => 3600 * 3, //3 hours
        ],
        'cookie_params' => [
            'lifetime' => 0,
            'path' => '/',
            'domain' => null,
            'secure' => false,
            'httponly' => false
        ],
    ],
    'auth' => [
        'sso_ticket_name' => '_gaia_ticket',
        'sso_domain' => '.wallstreetcn.com',
        'passport_base' => 'http://passport.wallstreetcn.com',
        'session_key' => '_gaia_session',
    ],
    'translate' => [
        'enable' => true,
        'path' => __DIR__ . '/../languages/',
        'adapter' => 'csv',
        'forceLang' => 'zh_CN',
    ],
    'logger' => [
        'adapter' => 'File',
        'path' => __DIR__ . '/../logs/',
    ],
    'dbAdapter' => [
        'prefix' => 'eva_',
        'master' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'dbname' => 'eva',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => 'eva_',
        ],
        'slave' => [
            'slave1' => [
                'adapter' => 'mysql',
                'host' => 'localhost',
                'dbname' => 'eva',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
                'prefix' => 'eva_',
            ],
        ]
    ]
];
