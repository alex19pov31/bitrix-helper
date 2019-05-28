<?php

namespace Alex19pov31\BitrixHelper\Iblock;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Sender\Connector\Filter\NumberField;

abstract class IblockPropertyTable extends DataManager
{
    const TTL = 180;

    private static $map;
    protected static $iblockCode;

    public function __construct(string $iblockCode) {
        $this->iblockCode = $iblockCode;
    }

    public static function getIblockId()
    {
        return getIblockId(static::$iblockCode, 180);
    }

    public static function getTableName()
    {
        return 'b_iblock_element_prop_s' . static::getIblockId();
    }

    /**
     * Карта свойств
     *
     * @return void
     */
    public static function getMap()
    {
        if (!is_null(static::$map['IBLOCK_CODE'])) {
            return static::$map['IBLOCK_CODE'];
        }

        $propertyList = PropertyTable::getList([
            'filter' => [
                'IBLOCK_ID' => static::getIblockId(),
            ],
            'cache' => [
                'ttl' => static::TTL * 60,
            ],
        ])->fetchAll();

        $fields = [
            'IBLOCK_ELEMENT_ID' => new IntegerField(
                'IBLOCK_ELEMENT_ID',
                [
                    'title' => 'Идентификатор элемента инфоблока',
                ]
            ),
        ];
        foreach ($propertyList as $property) {
            $class = StringField::class;
            switch ($property['PROPERTY_TYPE']) {
                case PropertyTable::TYPE_STRING:
                    $class = StringField::class;
                    break;
                case PropertyTable::TYPE_NUMBER:
                    $class = NumberField::class;
                    break;
                case PropertyTable::TYPE_FILE:
                    $class = NumberField::class;
                    break;
                case PropertyTable::TYPE_ELEMENT:
                    $class = StringField::class;
                    break;
                case PropertyTable::TYPE_SECTION:
                    $class = StringField::class;
                    break;
                case PropertyTable::TYPE_LIST:
                    $class = StringField::class;
                    break;
            }

            $fields[$property['CODE']] = new $class(
                'PROPERTY_' . $property['ID'],
                [
                    'title' => $property['NAME'],
                ]
            );
        }

        return static::$map['IBLOCK_CODE'] = $fields;
    }
}
