<?php

namespace Alex19pov31\BitrixHelper;

use Bitrix\Iblock\IblockTable;

class IblockHelper
{
    /**
     * Список инфоблоков
     *
     * @var [type]
     */
    private static $iblockList;

    /**
     * Время кеширования
     *
     * @var integer
     */
    private static $ttl = 0;

    private static $iblockClass;

    public static function setIBlockTableCalss(IblockTable $object)
    {
        static::$iblockClass = $object;
    }

    private static function getIBlockTableClass()
    {
        if (!is_null(static::$iblockClass)) {
            return static::$iblockClass;
        }

        loadModule('iblock');
        return IblockTable::class;
    }

    /**
     * Список инфоблоков
     *
     * @return array
     */
    public static function getListIblock(): array
    {
        if (!is_null(static::$iblockList)) {
            return (array)static::$iblockList;
        }

        $iblockTableClass = static::getIBlockTableClass();
        $res = $iblockTableClass::getList([
            'select' => [
                'ID',
                'IBLOCK_TYPE_ID',
                'CODE',
                'NAME',
            ],
            'cache' => [
                'ttl' => static::$ttl,
            ],
        ]);

        while ($iblock = $res->fetch()) {
            static::$iblockList[$iblock['CODE']] = $iblock;
            static::$iblockList[$iblock['CODE'] . '_' . $iblock['IBLOCK_TYPE_ID']] = $iblock;
        }

        return (array)static::$iblockList;
    }

    /**
     * Установить вермя кеширования
     *
     * @param integer $minutes
     * @return void
     */
    public static function setCacheTime(int $minutes)
    {
        static::$ttl = $minutes * 60;
    }

    /**
     * Возвращает идентификатор инфоблока по коду
     *
     * @param string $code
     * @return integer|null
     */
    public static function getIblockID(string $code, $iblockType = null)
    {
        $iblock = static::getIblock($code, $iblockType);
        if (empty($iblock)) {
            return null;
        }

        return (int)$iblock['ID'];
    }

    public static function getIblock(string $code, $iblockType = null)
    {
        $list = static::getListIblock();
        $key = is_null($iblockType) ? $code : $code . '_' . $iblockType;
        $iblock = $list[$key];
        if (empty($iblock)) {
            return null;
        }

        return (array)$iblock;
    }
}
