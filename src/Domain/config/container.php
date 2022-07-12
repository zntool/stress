<?php

use Symfony\Component\Console\Application;
use ZnCore\DotEnv\Domain\Libs\DotEnv;
use ZnCore\Container\Libs\Container;
use ZnDatabase\Eloquent\Domain\Factories\ManagerFactory;
use ZnDatabase\Eloquent\Domain\Capsule\Manager;
use ZnTool\Stress\Domain\Repositories\Conf\ProfileRepository;

return [
    'definitions' => [],
    'singletons' => [
        Application::class => Application::class,
        /*Manager::class => function () {
            return ManagerFactory::createManagerFromEnv();
        },*/
        ProfileRepository::class => function () {
            $config = [];
            if(!isset($_ENV['STRESS_PROFILE_CONFIG'])) {
                throw new \ZnCore\Contract\Common\Exceptions\InvalidConfigException('Empty ENV "STRESS_PROFILE_CONFIG"!');
            }
            if(!empty($_ENV['STRESS_PROFILE_CONFIG'])) {
                $configFileName = $_ENV['STRESS_PROFILE_CONFIG'];
                $config = include ($configFileName);
            }
            return new ProfileRepository($config);
        },
    ],
];
