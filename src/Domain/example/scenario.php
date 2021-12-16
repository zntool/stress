<?php

$baseUrl = 'http://example.com/api.php/v1/';

return [
    [
        'title' => 'Read only',
        'name' => 'read_only',
        'synchQueryCount' => 10,
        'ageCount' => 5,
        'queryCollection' => [
            /*[
                'url' => $baseUrl . 'auth',
                'method' => 'POST',
                'options' => [
                    \GuzzleHttp\RequestOptions::FORM_PARAMS => [
                        'login' => 'user4',
                        'password' => 'user4',
                    ],
                ],
            ],*/
            [
                'url' => $baseUrl . 'language',
                'method' => 'GET',
                'options' => [],
            ],
            [
                'url' => $baseUrl . 'geo-locality',
            ],
            [
                'url' => $baseUrl . 'geo-region',
            ],
            [
                'url' => $baseUrl . 'geo-province',
            ],
            [
                'url' => $baseUrl . 'feedback-category',
            ],
            [
                'url' => $baseUrl . 'tag',
            ],
            [
                'url' => $baseUrl . 'news',
            ],
            [
                'url' => $baseUrl . 'news-feed',
            ],
            [
                'url' => $baseUrl . 'news-category',
            ],
            [
                'url' => $baseUrl . 'community-category',
            ],
        ],
    ],
];
