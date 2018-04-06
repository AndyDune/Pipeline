<?php
/**
 * This package provides a pipeline pattern implementation. It base on middleware approach.
 *
 * PHP version => 5.6
 *
 * @package andydune/pipeline
 * @link  https://github.com/AndyDune/Pipeline for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
 */



namespace AndyDuneTest;
use AndyDune\Pipeline\Pipeline;
use AndyDune\Pipeline\Stage\ExceptionCatch;
use AndyDune\Pipeline\Stage\ArrayKeysToLower;
use PHPUnit\Framework\TestCase;


class StagesTest extends TestCase
{
    /**
     * @return void
     */
    public function testArrayKeysToLower()
    {
        $array = [
            'id' => 12,
            'ID' => 13,
            'aRRay' => [
                'RU' => 'RU',
                'EN' => 'EN',
            ]
        ];

        $result = (new Pipeline())
                  ->send($array)
            ->pipe(ArrayKeysToLower::class)->execute();

        $this->assertCount(2, $result);
        $this->arrayHasKey('array', $result);
        $this->assertArrayNotHasKey('ID', $result);
        $this->assertEquals(13, $result['id']);
        $this->assertArrayNotHasKey('RU', $result['array']);
        $this->assertArrayNotHasKey('EN', $result['array']);


        $result = (new Pipeline())
            ->send($array)
            ->pipe(ArrayKeysToLower::class, null, false)->execute();

        $this->assertCount(2, $result);
        $this->arrayHasKey('array', $result);
        $this->arrayHasKey('id', $result);
        $this->assertEquals(13, $result['id']);
        $this->assertArrayNotHasKey('ru', $result['array']);
        $this->assertArrayNotHasKey('en', $result['array']);

        $result = (new Pipeline())
            ->send($array)
            ->pipe(ArrayKeysToLower::class, null, true, ['aRRay'])->execute();

        $this->assertCount(3, $result);
        $this->assertArrayHasKey('aRRay', $result);
        $this->assertArrayHasKey('ID', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('ru', $result['aRRay']);
        $this->assertArrayHasKey('en', $result['aRRay']);

    }

}