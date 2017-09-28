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
use AndyDune\Pipeline\Stage\ExceptionCatch;
use PHPUnit\Framework\TestCase;
use AndyDune\Pipeline\Example\Trim;
use Exception;
use ArrayObject;

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
            function ($contest, $next) {
                $contest += 100;
                return $next($contest);
            },
            function ($contest, $next) {
                $contest += 10;
                return $next($contest);
            }
        ];
        $result = $pipeline->through($stages)->send(1)
            ->then(function ($context) {
                $context += 1000;
                return $context;
            });
        $this->assertEquals(1111, $result);
    }

    /**
     * @return void
     */
    public function testPipeIsClassName()
    {
        $pipeline = new Pipeline();
        $pipeline->send(' 123 ');
        $pipeline->pipe(Trim::class);
        $result = $pipeline->then(function ($context) {
            return $context;
        });
        $this->assertEquals(3, strlen($result));

        $pipeline = new Pipeline();
        $pipeline->send(' 123 ');
        $pipeline->pipe(Trim::class , 'handle', '1 ');
        $result = $pipeline->then(function ($context) {
            return $context;
        });
        $this->assertEquals('23', $result);

    }

    /**
     * @return void
     */
    public function testExceptionCatch()
    {
        $pipeline = new Pipeline();
        $pipeline->send(['zub' => 'kovoy']);
        $pipeline->pipe(function ($context, $next) {
            try {
                return $next($context);
            } catch (Exception $e) {
                $context['exception'] = 'caught';
            }
            return $context;
        });

        $pipeline->pipe(function ($context, $next) {
            $context['action'] = 'before_exception';
            throw new Exception();
            return $next($context);
        });

        $pipeline->pipe(function ($context, $next) {
            $context['exception'] = 'ignored';
            return $next($context);
        });

        $result = $pipeline->execute();
        $this->assertArrayHasKey('exception', $result);
        $this->assertEquals('caught', $result['exception']);
        $this->assertArrayHasKey('zub', $result);
        $this->assertArrayNotHasKey('action', $result);


        $pipeline = new Pipeline();
        $pipeline->send(new ArrayObject(['zub' => 'kovoy']));
        $pipeline->pipe(function ($context, $next) {
            try {
                return $next($context);
            } catch (Exception $e) {
                $context['exception'] = 'caught';
            }
            return $context;
        });

        $pipeline->pipe(function ($context, $next) {
            // $context is object
            $context['action'] = 'before_exception';
            throw new Exception();
            return $next($context);
        });

        $result = $pipeline->execute();
        $this->assertArrayHasKey('exception', $result);
        $this->assertEquals('caught', $result['exception']);
        $this->assertArrayHasKey('zub', $result);
        $this->assertArrayHasKey('action', $result);
    }

    /**
     * Test exception catch default stage.
     */
    public function testDefaultExceptionStage()
    {
        $pipeline = new Pipeline();
        $pipeline->send(['zub' => 'kovoy']);
        $pipeline->pipe(ExceptionCatch::class);
        $pipeline->pipe(function ($context, $next) {
            $context['action'] = 'before_exception';
            throw new Exception('jump');
            return $next($context);
        });
        $result = $pipeline->execute();
        $this->assertInstanceOf(Exception::class, $result);
        $this->assertEquals('jump', $result->getMessage());

    }

}