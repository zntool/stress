<?php

namespace ZnTool\Stress\Commands;

use Illuminate\Support\Collection;
use ZnTool\Stress\Domain\Entities\ProfileEntity;
use ZnTool\Stress\Domain\Entities\TestEntity;
use ZnTool\Stress\Domain\Services\StressService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StressCommand extends Command
{

    protected static $defaultName = 'dev:stress:test';
    private $stressService;

    public function __construct(?string $name = null, StressService $stressService)
    {
        parent::__construct($name);
        $this->stressService = $stressService;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['<fg=white># Stress test</>']);

        $profileEntity = new ProfileEntity(20, 5);
//        $profileEntity->synchQueryCount = 20;
//        $profileEntity->ageCount = 5;
        $baseUrl = 'http://elumiti.cd/api.php/v1/';
        $profileEntity->setQueryCollection([
            $baseUrl . 'language',
            $baseUrl . 'geo-locality',
            $baseUrl . 'geo-region',
            $baseUrl . 'geo-province',
            $baseUrl . 'feedback-category',
            $baseUrl . 'tag',
            $baseUrl . 'news',
            $baseUrl . 'news-feed',
            $baseUrl . 'news-category',
            $baseUrl . 'community-category',
        ]);

        /** @var TestEntity[] $queryCollection */
        $queryCollection = new Collection;

        for ($i = 0; $i < $profileEntity->getSynchQueryCount(); $i++) {
            $testEntity = new TestEntity;
            $id = $i + 1;
            $index = $i % $profileEntity->getQueryCollectionCount();
            $url = $profileEntity->getQueryByIndex($index);
            $testEntity->url = $url /*. '/' . $id*/;
            $queryCollection->add($testEntity);
        }
        
//        dd($queryCollection);

        $resultEntity = $this->stressService->test($queryCollection, $profileEntity->getAgeCount());
        $queryRuntime = $resultEntity->runtime / $resultEntity->queryCount;

        $output->writeln([
            '',
            '<fg=green>Stress success!</>',
            '<fg=green>Total runtime: ' . $resultEntity->runtime . '</>',
            '<fg=green>Query count: ' . $resultEntity->queryCount . '</>',
            '<fg=green>Query runtime: ' . $queryRuntime . '</>',
            '<fg=green>Performance: ' . round(1 / $queryRuntime) . ' queries per second</>',
            '',
        ]);

        return Command::SUCCESS;
    }

}
