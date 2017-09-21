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
    public function testNoFinalCallback()
    {

        $pipeline = new Pipeline();
        $pipeline->send(1);
        $pipeline->pipe(function ($context, $next) {
            $context += 100;
            return $next($context);
        });
        $result = $pipeline->execute();
        $this->assertEquals(101, $result);

    }


    public function testMethodThrough()
    {
        $pipeline = new Pipeline();

        $stages = [
            function($contest, $next) {
                $contest += 100;
                return $next($contest);
            },
            function($contest, $next) {
                $contest += 10;
                return $next($contest);
            }
        ];
        $result = $pipeline->through($stages)->send(1)
            ->then(function ($context) {
                $context += 1000;
                return $context;});
        $this->assertEquals(1111, $result);
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