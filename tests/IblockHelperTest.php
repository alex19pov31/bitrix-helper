<?php

namespace Alex19pov31\Tests\BitrixHelper;

use Alex19pov31\BitrixHelper\IblockHelper;
use PHPUnit\Framework\TestCase;

class IblockHelperTest extends TestCase
{
    private static $dataSet;
    private static $iblockClass;

    public function testGetIblockID()
    {
        $this->assertEquals(IblockHelper::getIblockID('test1'), 1);
        $this->assertEquals(IblockHelper::getIblockID('test2'), 35);
        $this->assertEquals(IblockHelper::getIblockID('test3'), 21);
        static::$iblockClass->shouldHaveReceived()->getList([
            'select' => [
                'ID',
                'IBLOCK_TYPE_ID',
                'CODE',
                'NAME',
            ],
            'cache' => [
                'ttl' => 177 * 60,
            ],
        ])->once();
        static::$iblockClass->shouldHaveReceived('fetch')->times(4);
    }

    public function testGetIblock()
    {
        $this->assertEquals(IblockHelper::getIblock('test1'), static::$dataSet[0]);
        $this->assertEquals(IblockHelper::getIblock('test2'), static::$dataSet[1]);
        $this->assertEquals(IblockHelper::getIblock('test3'), static::$dataSet[2]);
        static::$iblockClass->shouldHaveReceived()->getList([
            'select' => [
                'ID',
                'IBLOCK_TYPE_ID',
                'CODE',
                'NAME',
            ],
            'cache' => [
                'ttl' => 177 * 60,
            ],
        ])->once();
        static::$iblockClass->shouldHaveReceived('fetch')->times(4);
    }

    public static function setUpBeforeClass()
    {
        static::$dataSet = [
            [
                'ID' => 1,
                'NAME' => 'Тестовый инфоблок #1',
                'IBLOCK_TYPE_ID' => 'testing',
                'CODE' => 'test1',
            ],
            [
                'ID' => 35,
                'NAME' => 'Тестовый инфоблок #2',
                'IBLOCK_TYPE_ID' => 'testing2',
                'CODE' => 'test2',
            ],
            [
                'ID' => 21,
                'NAME' => 'Тестовый инфоблок #3',
                'IBLOCK_TYPE_ID' => 'testing',
                'CODE' => 'test3',
            ],
        ];

        $iblockClass = \Mockery::spy('Bitrix\Iblock\IblockTable');
        $iblockClass->shouldReceive('getList')->andReturnSelf();
        $iblockClass->shouldReceive('fetch')->andReturn(
            static::$dataSet[0],
            static::$dataSet[1],
            static::$dataSet[2],
            null
        );

        static::$iblockClass = $iblockClass;
        IblockHelper::setIBlockTableCalss(static::$iblockClass);
        IblockHelper::setCacheTime(177);
    }

    public static function tearDownAfterClass()
    {
        \Mockery::close();
    }
}
