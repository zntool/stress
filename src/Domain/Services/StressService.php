<?php

namespace ZnTool\Stress\Domain\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnTool\Dev\Runtime\Domain\Helpers\Benchmark;
use ZnTool\Stress\Domain\Entities\ResultEntity;
use ZnTool\Stress\Domain\Entities\TestEntity;
use ZnTool\Stress\Domain\Libs\Runtime;
use function GuzzleHttp\Promise\settle;

class StressService
{

    public function test(Collection $queryCollection, int $ageCount): ResultEntity
    {
        $totalQueryCount = 0;
        $commonRuntime = 0;
        $resultEntity = new ResultEntity;

        /** @var TestEntity[] $queryCollection */

        echo "All queries\n";
        foreach ($queryCollection as $i => $testEntity) {
            echo "   {$testEntity->getUrl()}\n";
        }
        
        for ($i = 0; $i < $ageCount; $i++) {
            $commonRuntime += $this->testAge($queryCollection);
            $totalQueryCount += count($queryCollection);
        }
        $resultEntity->runtime = $commonRuntime;
        $resultEntity->queryCount = $totalQueryCount;
        return $resultEntity;
    }

    private function testAge(Collection $queryCollection): float
    {
        $client = new Client;
        $options = [
            /*'headers' => [
                'Accept' => 'application/json',
            ],*/
        ];
        $promises = [];
        /**
         * @var  $i
         * @var TestEntity $testEntity
         */
        foreach ($queryCollection as $i => $testEntity) {
            $clientMethodName = $testEntity->getMethod() . 'Async';
            $async = $client->$clientMethodName($testEntity->getUrl(), $options);
            $promises['query_' . $i] = $async;
        }

        /** @var Runtime[] $runtimeCollection */
        $runtimeCollection = new Collection();
        $runtime = new Runtime();
        $runtime->start();
        echo "send {$queryCollection->count()} queries ... ";
        //$results = unwrap($promises); // Дождаться завершения всех запросов. Выдает исключение ConnectException если какой-либо из запросов не выполнен
        $results = settle($promises)->wait(); // Дождемся завершения запросов, даже если некоторые из них завершатся неудачно
        $runtime->stop();
        $runtimeCollection->add($runtime);
        $runTimePerQuery = $runtime->getResult() / $queryCollection->count();
        $runTimePerQuery = round($runTimePerQuery, 4);
        echo "OK ($runTimePerQuery)\n";

        $localRuntime = 0;
        foreach ($runtimeCollection as $rt) {
            $localRuntime = $localRuntime + $rt->getResult();
        }

        $this->checkErrors($results);
        return $localRuntime;
    }

    private function checkErrors(array $results)
    {
        foreach ($results as $result) {
            if ($result['state'] != 'fulfilled' || ArrayHelper::getValue($result, 'reason.code') > 500) {
                dd($result);
                throw new \UnexpectedValueException('Response error!');
            }
        }
    }

}
