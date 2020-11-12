<?php

namespace ZnTool\Stress\Domain\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnTool\Dev\Runtime\Domain\Helpers\Benchmark;
use ZnTool\Stress\Domain\Entities\ResultEntity;
use function GuzzleHttp\Promise\settle;

class StressService
{

    public function test(Collection $queryCollection, int $ageCount): ResultEntity
    {
        $totalQueryCount = 0;
        $commonRuntime = 0;
        $resultEntity = new ResultEntity;
        
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
        $commonRuntime = 0;
        $promises = [];
        foreach ($queryCollection as $i => $testEntity) {
            $promises['query_' . $i] = $client->getAsync($testEntity->url, $options);
        }
        Benchmark::begin('stress_test');
        echo "send {$queryCollection->count()} queries ... ";
        //$results = unwrap($promises); // Дождаться завершения всех запросов. Выдает исключение ConnectException если какой-либо из запросов не выполнен
        $results = settle($promises)->wait(); // Дождемся завершения запросов, даже если некоторые из них завершатся неудачно
        echo "OK\n";
        Benchmark::end('stress_test');
        $runtime = Benchmark::allFlat()['stress_test'];
        $commonRuntime = $commonRuntime + $runtime;
        $this->checkErrors($results);
        return $commonRuntime;
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
