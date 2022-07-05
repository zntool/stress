<?php

namespace ZnTool\Stress\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZnCore\Domain\Collection\Interfaces\Enumerable;
use ZnCore\Domain\Collection\Libs\Collection;
use ZnCore\Domain\Entity\Helpers\CollectionHelper;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnLib\Console\Symfony4\Question\ChoiceQuestion;
use ZnTool\Stress\Domain\Entities\ProfileEntity;
use ZnTool\Stress\Domain\Entities\ResultEntity;
use ZnTool\Stress\Domain\Entities\TestEntity;
use ZnTool\Stress\Domain\Helpers\RuntimeHelper;
use ZnTool\Stress\Domain\Repositories\Conf\ProfileRepository;
use ZnTool\Stress\Domain\Services\StressService;

class StressCommand extends Command
{

    protected static $defaultName = 'dev:stress:test';
    private $stressService;
    private $profileRepository;

    public function __construct(?string $name = null, StressService $stressService, ProfileRepository $profileRepository)
    {
        parent::__construct($name);
        $this->stressService = $stressService;
        $this->profileRepository = $profileRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['<fg=white># Stress test</>']);

        $profileCollection = $this->profileRepository->findAll();
        $profiles = CollectionHelper::getColumn($profileCollection, 'name');

        if (empty($profiles)) {
            $output->writeln('<fg=yellow>Empty profiles</>');
        }

        $selectedProfiles = $this->selectProfiles($input, $output, $profiles);

        foreach ($selectedProfiles as $profileName) {
            $profileEntity = $this->profileRepository->findOneByName($profileName);
            /** @var TestEntity[] $queryCollection */
            $queryCollection = $this->forgeQueryCollection($profileEntity);

            $this->showQueryList($input, $output, $queryCollection);

            $all = new Collection;
            for ($i = 0; $i < $profileEntity->getAgeCount(); $i++) {
                $output->write(".");
                $runtimeCollection = $this->stressService->testAge($queryCollection, $profileEntity);
                $all->add($runtimeCollection);
            }

            $resultEntity = new ResultEntity();
            $resultEntity->runtime = RuntimeHelper::sunOfTree($all);
            $resultEntity->queryCount = $profileEntity->getSynchQueryCount() * $profileEntity->getAgeCount();

            $queryRuntime = $resultEntity->runtime / $resultEntity->queryCount;

            $output->writeln([
                '',
                '<fg=green>Stress success!</>',
                '<fg=green>Total runtime: ' . round($resultEntity->runtime, 4) . '</>',
                '<fg=green>Query count: ' . $resultEntity->queryCount . '</>',
                '<fg=green>Query runtime: ' . round($queryRuntime, 4) . '</>',
                '<fg=green>Performance: ' . round(1 / $queryRuntime) . ' queries per second</>',
                '',
            ]);
        }
        return Command::SUCCESS;
    }

    private function forgeQueryCollection(ProfileEntity $profileEntity): Enumerable
    {
        $queryCollection = new Collection;
        for ($i = 0; $i < $profileEntity->getSynchQueryCount(); $i++) {
            $index = $i % $profileEntity->getQueryCollectionCount();
            $query = $profileEntity->getQueryByIndex($index);
            $testEntity = EntityHelper::createEntity(TestEntity::class, $query);
            $queryCollection->add($testEntity);
        }
        return $queryCollection;
    }

    private function showQueryList(InputInterface $input, OutputInterface $output, $queryCollection)
    {
        $output->writeln("All queries");
        foreach ($queryCollection as $i => $testEntity) {
            $method = mb_strtoupper($testEntity->getMethod());
            $output->writeln("   {$method} {$testEntity->getUrl()}");
        }
        $output->writeln("");
    }

    private function selectProfiles(InputInterface $input, OutputInterface $output, array $profiles): array
    {
        if (count($profiles) < 2) {
            return $profiles;
        }
        $output->writeln('');
        $question = new ChoiceQuestion(
            'Select profiles',
            $profiles,
            'a'
        );
        $question->setMultiselect(true);
        $selectedProfiles = $this->getHelper('question')->ask($input, $output, $question);
        return $selectedProfiles;
    }
}
