<?php

namespace ZnTool\Stress\Domain\Libs;

class Runtime
{

    private $startTime;
    private $stopTime;

    public function start() {
        $this->startTime = microtime(true);
    }

    public function stop() {
        $this->stopTime = microtime(true);
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getStopTime()
    {
        return $this->stopTime;
    }

    public function getResult(int $percision = null) {
        $result = $this->stopTime - $this->startTime;
        if($percision !== null) {
            $result = round($result, $percision);
        }
        return $result;
    }
}
