<?php

namespace ZnTool\Stress\Commands;

use Illuminate\Support\Collection;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Console\Symfony4\Question\ChoiceQuestion;
use ZnTool\Stress\Domain\Entities\ProfileEntity;
use ZnTool\Stress\Domain\Entities\TestEntity;
use ZnTool\Stress\Domain\Libs\Runtime;
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

        $profileCollection = $this->profileRepository->all();
        $profiles = EntityHelper::getColumn($profileCollection, 'name');

        $selectedProfiles = $this->selectProfiles($input, $output, $profiles);

        foreach ($selectedProfiles as $selectedProfile) {
            $profileEntity = $this->profileRepository->oneByName($selectedProfile);
            /** @var TestEntity[] $queryCollection */
            $queryCollection = new Collection;
            for ($i = 0; $i < $profileEntity->getSynchQueryCount(); $i++) {
                $index = $i % $profileEntity->getQueryCollectionCount();
                $query = $profileEntity->getQueryByIndex($index);
                $testEntity = EntityHelper::createEntity(TestEntity::class, $query);
                $queryCollection->add($testEntity);
            }
//            $runtimeCollection = $this->stressService->test($queryCollection);
            echo "All queries\n";
            foreach ($queryCollection as $i => $testEntity) {
                echo "   {$testEntity->getUrl()}\n";
            }

            $totalQueryCount = 0;
            $commonRuntime = 0;
            for ($i = 0; $i < $profileEntity->getAgeCount(); $i++) {
                $runtimeCollection = $this->stressService->testAge($queryCollection);

                /**
                 * @var Runtime[] $runtimeCollection
                 */
                $localRuntime = 0;
                foreach ($runtimeCollection as $rt) {
                    $localRuntime = $localRuntime + $rt->getResult();
                }
                $commonRuntime += $localRuntime;
                $totalQueryCount += count($queryCollection);
            }

            $queryRuntime = $commonRuntime / $totalQueryCount;

            $output->writeln([
                '',
                '<fg=green>Stress success!</>',
                '<fg=green>Total runtime: ' . round($commonRuntime, 4) . '</>',
                '<fg=green>Query count: ' . $totalQueryCount . '</>',
                '<fg=green>Query runtime: ' . round($queryRuntime, 4) . '</>',
                '<fg=green>Performance: ' . round(1 / $queryRuntime) . ' queries per second</>',
                '',
            ]);
        }
        return Command::SUCCESS;
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
