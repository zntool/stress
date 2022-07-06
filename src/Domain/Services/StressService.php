<?php

namespace ZnTool\Stress\Domain\Services;

use GuzzleHttp\Client;
use ZnCore\Arr\Helpers\ArrayHelper;
use ZnCore\Collection\Interfaces\Enumerable;
use ZnCore\Collection\Libs\Collection;
use ZnTool\Stress\Domain\Entities\ProfileEntity;
use ZnTool\Stress\Domain\Entities\TestEntity;
use ZnTool\Stress\Domain\Libs\Runtime;
use function GuzzleHttp\Promise\settle;

class StressService
{

    public function testAge(Enumerable $queryCollection, ProfileEntity $profileEntity): Enumerable
    {
        $client = new Client;
        $defaultOptions = [
            /*'headers' => [
                'Accept' => 'application/json',
            ],*/
        ];
        $promises = [];
        /**
         * @var  $i
         * @var TestEntity $testEntity
         */
        $queryCollection = $queryCollection->shuffle();
        foreach ($queryCollection as $i => $testEntity) {
            $options = $defaultOptions;
            if ($testEntity->getOptions()) {
                $options = ArrayHelper::merge($options, $testEntity->getOptions());
            }
            //dd($options);
            $clientMethodName = $testEntity->getMethod() . 'Async';
            $async = $client->$clientMethodName($testEntity->getUrl(), $options);
            $promises['query_' . $i] = $async;
        }

        /** @var Runtime[] $runtimeCollection */
        $runtimeCollection = new Collection();
        $runtime = new Runtime();
        $runtime->start();
//        echo "send {$queryCollection->count()} queries ... ";
        //$results = unwrap($promises); // Дождаться завершения всех запросов. Выдает исключение ConnectException если какой-либо из запросов не выполнен
        $results = settle($promises)->wait(); // Дождемся завершения запросов, даже если некоторые из них завершатся неудачно
        $runtime->stop();
        $runtimeCollection->add($runtime);
//        $runTimePerQuery = $runtime->getResult() / $queryCollection->count();
//        $runTimePerQuery = round($runTimePerQuery, 4);
//        echo "OK ($runTimePerQuery)\n";

        $this->checkErrors($results, $profileEntity->getValidator());
        return $runtimeCollection;
    }

    private function checkErrors(array $results, callable $validator = null)
    {
        foreach ($results as $result) {
            /** @var \GuzzleHttp\Psr7\Response $response */
            if (empty($result['value'])) {
                dd($result);
            }

            $response = $result['value'];
            if ($validator !== null) {
                $isValid = $validator($response);
                if (!$isValid) {
                    dd($response->getBody()->getContents());
                    //dd($result);
                    throw new \UnexpectedValueException('Response error!');
                }
            } else {
                if ($result['state'] != 'fulfilled' || ArrayHelper::getValue($result, 'reason.code') > 500) {
                    dd($response->getBody()->getContents());
                    //dd($result);
                    throw new \UnexpectedValueException('Response error!');
                }
            }
        }
    }
}
