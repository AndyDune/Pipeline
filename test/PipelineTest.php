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
    public function testSimplePipeline()
    {
        $pipeline = new Pipeline();
        $pipeline->send(1);
        $pipeline->pipe(function ($context, $next) {
            $context += 100;
            return $next($context);
        });
        $result = $pipeline->then(function ($context) {
            $context += 50;
            return $context;
        });
        $this->assertEquals(151, $result);
    }


    /**
     * @return void
     */
    public function testPipeIsClassName()
    {
        $pipeline = new Pipeline();
        $pipeline->send(' 123 ');
        $pipeline->pipe('AndyDune\Pipeline\Example\Trim');
        $result = $pipeline->then(function ($context) {
            return $context;
        });
        $this->assertEquals(3, strlen($result));

        $pipeline = new Pipeline();
        $pipeline->send(' 123 ');
        $pipeline->pipe('AndyDune\Pipeline\Example\Trim:handle:1 ');
        $result = $pipeline->then(function ($context) {
            return $context;
        });
        $this->assertEquals('23', $result);

    }

}