<?php

namespace ZnTool\Stress\Domain\Helpers;

use Illuminate\Support\Collection;
use ZnTool\Stress\Domain\Entities\ResultEntity;
use ZnTool\Stress\Domain\Libs\Runtime;

class RuntimeHelper
{

    public static function calcRuntime(Collection $all, $queryCollection)
    {
        $localRuntime = 0;
        foreach ($all as $runtimeCollection) {
            /** @var Runtime[] $runtimeCollection */
            foreach ($runtimeCollection as $rt) {
                $localRuntime = $localRuntime + $rt->getResult();
            }
        }
        return $localRuntime;
    }
}
