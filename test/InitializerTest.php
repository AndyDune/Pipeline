<?php
/**
 * This package provides a pipeline pattern implementation. It base on middleware approach.
 *
 * PHP version >= 7.1
 *
 * @package andydune/pipeline
 * @link  https://github.com/AndyDune/Pipeline for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
 */



namespace AndyDuneTest;
use AndyDune\Pipeline\Example\InitializeSetAnyValueWithAnyMethod;
use AndyDune\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;
use AndyDune\Pipeline\Example\Methods;


class InitializerTest extends TestCase
{
    public function test()
    {
        $instance = new Methods();

        $pipeline = new Pipeline();
        $pipeline->addInitializer((new InitializeSetAnyValueWithAnyMethod())->setMethod('setBraceLeft')->setValue('['));
        $pipeline->addInitializer((new InitializeSetAnyValueWithAnyMethod())->setMethod('setBraceRight')->setValue(']'));
        $pipeline->send('puh');
        $pipeline->pipeForContainer($instance, 'addBraceLeft');
        $pipeline->pipe(Methods::class, 'addBraceRight');
        $result = $pipeline->execute();
        $this->assertEquals('[puh]', $result);

    }
}