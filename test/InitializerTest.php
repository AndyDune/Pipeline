<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 11.12.2017                            |
 * -----------------------------------------------
 *
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
        $pipeline->addInitializer((new InitializeSetAnyValueWithAnyMethod())->setMethod('addBraceLeft')->setValue('['));
        $pipeline->addInitializer((new InitializeSetAnyValueWithAnyMethod())->setMethod('addBraceRight')->setValue(']'));
        $pipeline->send('puh');
        $pipeline->pipeForContainer($instance, 'addBraceLeft');
        $pipeline->pipe(Methods::class, 'addBraceRight');
        $result = $pipeline->execute();
        $this->assertEquals('[puh]', $result);

    }
}