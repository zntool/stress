<?php

use Symfony\Component\Console\Application;
use ZnCore\Base\Libs\DotEnv\DotEnv;
use Illuminate\Container\Container;
use ZnLib\Db\Factories\ManagerFactory;
use ZnLib\Db\Capsule\Manager;
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
                throw new \ZnCore\Base\Exceptions\InvalidConfigException('Empty ENV "STRESS_PROFILE_CONFIG"!');
            }
            if(!empty($_ENV['STRESS_PROFILE_CONFIG'])) {
                $configFileName = __DIR__ . '/../../../../../../' . $_ENV['STRESS_PROFILE_CONFIG'];
                $config = include ($configFileName);
            }
            return new ProfileRepository($config);
        },
    ],
];
