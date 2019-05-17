<?php
/**
 * @author xialeistudio
 * @date 2019-05-16
 */

namespace tests\handler;


use tests\services\SumService\SumServiceIf;

class SumServiceImpl implements SumServiceIf
{
    /**
     * @param int $a
     * @param int $b
     * @return int
     */
    public function sum($a, $b)
    {
        return $a + $b;
    }
}