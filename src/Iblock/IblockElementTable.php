<?php

namespace Alex19pov31\BitrixHelper\Iblock;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\Relations\Reference;

abstract class IblockElementTable extends ElementTable
{
    const TTL = 180;
    protected static $iblockCode;
    protected static $iblockId;
    
    private function __construct(string $iblockCode) {
        $this->iblockCode = $iblockCode;
        $this->iblockId = getIblockId($iblockCode, null, static::TTL);
    }

    public static function init(string $iblockCode) {
        return new static($iblockCode);
    }

    public static function getList(array $parameters = [])
    {
        initTagCache([
            'iblock_code_'.static::$iblockCode,
            'iblock_id_'.static::$iblockId,
        ]);
        $parameters['filter']['IBLOCK_ID'] = static::$iblockId;
    }

    public static function getMap()
    {
        $map = parent::getMap();
        $map['PROPERTIES'] = new Reference(
            'PROPERTIES',
            static::getPropertiesClass(),
            ['=this.ID' => 'ref.IBLOCK_ELEMENT_ID'],
            ['join_type' => 'LEFT']
        );

        return $map;
    }

    private static function getPropertiesClass(): IblockPropertyTable
    {
        $code = static::IBLOCK_CODE;
        return new class(static::IBLOCK_CODE) extends IblockPropertyTable {
        };
    }
}
