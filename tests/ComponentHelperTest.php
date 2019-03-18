<?php

namespace Alex19pov31\Tests\BitrixHelper;

use Alex19pov31\BitrixHelper\ComponentHelper;
use PHPUnit\Framework\TestCase;

class ComponentHelperTest extends TestCase
{
    private static $app;

    public function testIncludeComponent()
    {
        $component = (new ComponentHelper('bitrix:catalog', '', [], null, [], static::$app))
            ->setTemplate('.default')
            ->setParams([
                'CACHE' => 'N',
            ]);
        $component->show();
        $content = $component->getReturnedData();

        $this->assertEquals($content, 'Component content');
        $this->assertCount(1, ComponentHelper::getStack());
        $component->show();
        $this->assertCount(2, ComponentHelper::getStack());
    }

    public function testGetByNameInStack()
    {
        $component = (new ComponentHelper('bitrix:catalog', '', [], null, [], static::$app))
            ->setTemplate('.default')
            ->setNameInStack('test_component')
            ->setParams([
                'CACHE' => 'N',
            ]);

        $returnedComponent = ComponentHelper::getByNameInStack('test_component');
        $this->assertCount(3, ComponentHelper::getStack());

        $this->assertEquals($returnedComponent, $component);
        $this->assertEquals($returnedComponent->getTemplate(), '.default');
        $this->assertEquals($returnedComponent->getParams(), [
            'CACHE' => 'N',
        ]);

        $component->show();
        $content = $component->getReturnedData();

        $this->assertEquals($content, 'Component content');
        $this->assertCount(4, ComponentHelper::getStack());
    }

    public static function setUpBeforeClass()
    {
        static::$app = \Mockery::spy('CMain');
        static::$app->shouldReceive('IncludeComponent')->andReturn('Component content');
    }

    public static function tearDownAfterClass()
    {
        \Mockery::close();
    }
}
