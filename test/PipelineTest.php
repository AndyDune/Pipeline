<?php
/**
 * ----------------------------------------------
 * | Автор: Андрей Рыжов (Dune) <info@rznw.ru>   |
 * | Сайт: www.rznw.ru                           |
 * | Телефон: +7 (4912) 51-10-23                 |
 * | Дата: 19.09.2017                            |
 * -----------------------------------------------
 *
 */


namespace AndyDuneTest;
use AndyDune\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;

class PipelineTest extends TestCase
{
    /**
     * @return void
     */
    public function testEmptyPipeline()
    {
        $pipeline = new Pipeline();
        $result = $pipeline->then(function () {
            return 12;
        });
        $this->assertEquals(12, $result);
    }

}