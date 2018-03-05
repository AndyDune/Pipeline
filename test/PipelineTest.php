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
use AndyDune\Pipeline\Example\InterfaceMethods;
use AndyDune\Pipeline\Example\Methods;
use AndyDune\Pipeline\Example\PowerOfNumber;
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

        $pipeline = new Pipeline();
        $pipeline->send(' 123 ');
        $pipeline->pipe(new Trim , 'handle', '3 ');
        $result = $pipeline->then(function ($context) {
            return $context;
        });
        $this->assertEquals('12', $result);


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

    public function testParams()
    {
        $pipeline = new Pipeline();
        $pipeline->send(2);
        $pipeline->pipe(PowerOfNumber::class);
        $result = $pipeline->execute();
        $this->assertEquals(4, $result);

        $pipeline = new Pipeline();
        $pipeline->send(2);
        $pipeline->pipe(PowerOfNumber::class, null, 3);
        $result = $pipeline->execute();
        $this->assertEquals(8, $result);


        $pipeline = new Pipeline();
        $pipeline->send(2);
        $pipeline->pipe(PowerOfNumber::class, 'power3');
        $result = $pipeline->execute();
        $this->assertEquals(8, $result);

                $pipeline = new Pipeline();
        $pipeline->send(2);
        $pipeline->pipe(PowerOfNumber::class, null, [2, 3, 4]);
        $result = $pipeline->execute();
        $this->assertCount(3, $result);
        $this->assertEquals(4, $result[0]);
        $this->assertEquals(8, $result[1]);
        $this->assertEquals(16, $result[2]);

    }

    public function testExceptionForMethodNotExists()
    {
        try {
            $pipeline = new Pipeline();
            $pipeline->send(2);
            $pipeline->pipe(PowerOfNumber::class, 'notExistMethod');
            $result = $pipeline->execute();
            $this->assertTrue(false, 'This line must not be achived');
        } catch (\AndyDune\Pipeline\Exception $e) {
            $message = $e->getMessage();
            $this->assertEquals('Method notExistMethod does not exist in stage with class ' . PowerOfNumber::class, $message);
        }
    }

    public function testStageWithInterface()
    {
        $pipeline = new Pipeline();
        $pipeline->send(2);
        $result = $pipeline->pipe(InterfaceMethods::class)->execute();
        $this->assertEquals(12, $result);

        $pipeline = new Pipeline();
        $pipeline->send(2);
        $result = $pipeline->pipe(InterfaceMethods::class, 'handle')->execute();
        $this->assertEquals(102, $result);


        $pipeline = new Pipeline();
        $pipeline->send(2);
        $pipeline->pipe(InterfaceMethods::class);
        $result = $pipeline->pipe(InterfaceMethods::class, 'handle')->execute();
        $this->assertEquals(112, $result);

    }

    public function test()
    {
        $instance = new Methods();

        $pipeline = new Pipeline();
        $pipeline->send('puh');
        $pipeline->pipeForContainer($instance, 'addBraceLeft');
        $pipeline->pipe(Methods::class, 'addBraceRight');
        $result = $pipeline->execute();
        $this->assertEquals('(puh)', $result);

    }
}