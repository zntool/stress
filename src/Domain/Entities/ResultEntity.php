<?php

namespace ZnTool\Stress\Domain\Entities;

class ResultEntity
{

    public $queryCount = 0;
    public $runtime = 0;

    public function incrementQueryCount(int $value)
    {
        $this->queryCount = $this->queryCount + $value;
    }

    public function incrementRuntime(int $value)
    {
        $this->runtime = $this->runtime + $value;
    }
}
