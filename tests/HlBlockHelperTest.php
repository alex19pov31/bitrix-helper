<?php

namespace Alex19pov31\Tests\BitrixHelper;

use Alex19pov31\BitrixHelper\HlBlockHelper;
use PHPUnit\Framework\TestCase;

class HlBlockHelperTest extends TestCase
{
    private static $dataSet;
    private static $hlBlockTableClass;

    public function testFindByID()
    {
        $this->assertEquals(HlBlockHelper::getHlblockByID(1), static::$dataSet[0]);
        $this->assertEquals(HlBlockHelper::getHlblockByID(35), static::$dataSet[1]);
        $this->assertEquals(HlBlockHelper::getHlblockByID(21), static::$dataSet[2]);
        static::$hlBlockTableClass->shouldHaveReceived()->getList([
            'cache' => [
                'ttl' => 180 * 60,
            ],
        ])->once();
        static::$hlBlockTableClass->shouldHaveReceived('fetch')->times(4);
    }

    public function testFindByName()
    {
        $this->assertEquals(HlBlockHelper::getHlblockByName('test1'), static::$dataSet[0]);
        $this->assertEquals(HlBlockHelper::getHlblockByName('test2'), static::$dataSet[1]);
        $this->assertEquals(HlBlockHelper::getHlblockByName('test3'), static::$dataSet[2]);
        static::$hlBlockTableClass->shouldHaveReceived()->getList([
            'cache' => [
                'ttl' => 180 * 60,
            ],
        ])->once();
        static::$hlBlockTableClass->shouldHaveReceived('fetch')->times(4);
    }

    public function testFindByTableName()
    {
        $this->assertEquals(HlBlockHelper::getHlblockByTableName('table_test1'), static::$dataSet[0]);
        $this->assertEquals(HlBlockHelper::getHlblockByTableName('table_test2'), static::$dataSet[1]);
        $this->assertEquals(HlBlockHelper::getHlblockByTableName('table_test3'), static::$dataSet[2]);
        static::$hlBlockTableClass->shouldHaveReceived()->getList([
            'cache' => [
                'ttl' => 180 * 60,
            ],
        ])->once();
        static::$hlBlockTableClass->shouldHaveReceived('fetch')->times(4);
    }

    public function testFind()
    {
        $this->assertEquals(HlBlockHelper::getHlblock('1'), static::$dataSet[0]);
        $this->assertEquals(HlBlockHelper::getHlblockByName('test1'), static::$dataSet[0]);
        $this->assertEquals(HlBlockHelper::getHlblockByTableName('table_test1'), static::$dataSet[0]);

        $this->assertEquals(HlBlockHelper::getHlblock('35'), static::$dataSet[1]);
        $this->assertEquals(HlBlockHelper::getHlblockByName('test2'), static::$dataSet[1]);
        $this->assertEquals(HlBlockHelper::getHlblockByTableName('table_test2'), static::$dataSet[1]);

        $this->assertEquals(HlBlockHelper::getHlblock('21'), static::$dataSet[2]);
        $this->assertEquals(HlBlockHelper::getHlblockByName('test3'), static::$dataSet[2]);
        $this->assertEquals(HlBlockHelper::getHlblockByTableName('table_test3'), static::$dataSet[2]);
        static::$hlBlockTableClass->shouldHaveReceived()->getList([
            'cache' => [
                'ttl' => 180 * 60,
            ],
        ])->once();
        static::$hlBlockTableClass->shouldHaveReceived('fetch')->times(4);
    }

    public function testFindClass()
    {
        $dataManager = \Mockery::mock('Bitrix\Main\ORM\Data\DataManager');
        static::$hlBlockTableClass->shouldReceive('compileEntity')->andReturnSelf();
        static::$hlBlockTableClass->shouldReceive('getDataClass')->andReturn($dataManager);

        $this->assertEquals(HlBlockHelper::getHlblockClass('21'), $dataManager);

        static::$hlBlockTableClass->shouldHaveReceived()->compileEntity(static::$dataSet[2])->once();
        static::$hlBlockTableClass->shouldHaveReceived('getDataClass')->once();
    }

    public static function setUpBeforeClass()
    {
        static::$dataSet = [
            [
                'ID' => 1,
                'NAME' => 'test1',
                'TABLE_NAME' => 'table_test1',
                'FIELDS_COUNT' => '4',
            ],
            [
                'ID' => 35,
                'NAME' => 'test2',
                'TABLE_NAME' => 'table_test2',
                'FIELDS_COUNT' => '4',
            ],
            [
                'ID' => 21,
                'NAME' => 'test3',
                'TABLE_NAME' => 'table_test3',
                'FIELDS_COUNT' => '4',
            ],
        ];

        static::$hlBlockTableClass = \Mockery::spy('Bitrix\Highloadblock\HighloadBlockTable');
        static::$hlBlockTableClass->shouldReceive('getList')->andReturnSelf();
        static::$hlBlockTableClass->shouldReceive('fetch')
            ->andReturn(
                static::$dataSet[0],
                static::$dataSet[1],
                static::$dataSet[2],
                null,
            );

        HlBlockHelper::setHlBlockTableCalss(static::$hlBlockTableClass);
        HlBlockHelper::setCacheTime(180);
    }

    public static function tearDownAfterClass()
    {
        \Mockery::close();
    }
}
