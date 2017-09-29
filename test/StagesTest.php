<?php
/**
 * ----------------------------------------------
 * | Автор: Андрей Рыжов (Dune) <info@rznw.ru>   |
 * | Сайт: www.rznw.ru                           |
 * | Телефон: +7 (4912) 51-10-23                 |
 * | Дата: 29.09.2017                            |
 * -----------------------------------------------
 *
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
    }

}