<?php
return [
    'movie' => [
        'importer' => [
            'dmm' => [
                'htmlPath' => __DIR__ . '/',
            ]
        ],
        'crawl' => [
            'dmm' => [
                'logPath' => __DIR__ . '/../tmp',
                'crawlPath' => __DIR__ . '/../tmp',
                'apiId' => '',
                'affiliateId' => '',
            ],
            'douban' => [
                'logPath' => __DIR__ . '/../tmp',
                'crawlPath' => __DIR__ . '/../tmp',
                'appId' => '',
                'appSecret' => ''
            ]
        ],
    ]
];
