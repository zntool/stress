<?php

use ZnTool\Stress\Domain\Entities\ProfileEntity;

$profileEntity = new ProfileEntity();
$profileEntity->synchQueryCount = 20;
$profileEntity->ageCount = 5;
$baseUrl = 'http://elumiti.cd/api.php/v1/';
$profileEntity->setQueryCollection([
    [
        'url' => $baseUrl . 'auth',
        'method' => 'POST',
        'options' => [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => [
                'login' => 'user4',
                'password' => 'user4',
            ],
        ],
    ],
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
]);

return $profileEntity;