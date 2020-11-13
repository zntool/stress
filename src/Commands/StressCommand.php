<?php

namespace ZnTool\Stress\Commands;

use Illuminate\Support\Collection;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Console\Symfony4\Question\ChoiceQuestion;
use ZnTool\Stress\Domain\Entities\ProfileEntity;
use ZnTool\Stress\Domain\Entities\TestEntity;
use ZnTool\Stress\Domain\Repositories\Conf\ProfileRepository;
use ZnTool\Stress\Domain\Services\StressService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        /*$profileArray = include __DIR__ . '/../Domain/example/scenario.php';

        $profileCollection = EntityHelper::createEntityCollection(ProfileEntity::class, $profileArray);*/
        $profileCollection = $this->profileRepository->all();
        $profiles = EntityHelper::getColumn($profileCollection, 'name');

        if ($profileCollection->count() > 1) {
            $selectedProfiles = $this->selectProfiles($input, $output, $profiles);
        } else {
            $selectedProfiles = $profiles;
        }

        foreach ($selectedProfiles as $selectedProfile) {
            $profileEntity = $this->profileRepository->oneByName($selectedProfile);
            /** @var TestEntity[] $queryCollection */
            $queryCollection = new Collection;
            for ($i = 0; $i < $profileEntity->getSynchQueryCount(); $i++) {
                $index = $i % $profileEntity->getQueryCollectionCount();
                $query = $profileEntity->getQueryByIndex($index);
                $testEntity = new TestEntity;
                $testEntity->url = $query['url'];
                $testEntity->options = $query['options'] ?: [];
                $testEntity->method = $query['method'] ?: 'get';
                $queryCollection->add($testEntity);
            }
            $resultEntity = $this->stressService->test($queryCollection, $profileEntity->getAgeCount());
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

    private function selectProfiles(InputInterface $input, OutputInterface $output, array $profiles): array
    {
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
