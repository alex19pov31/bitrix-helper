<?php

namespace Alex19pov31\BitrixHelper;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ORM\Data\DataManager;

class HlBlockHelper
{
    /**
     * Список HL блоков
     *
     * @var array
     */
    private static $hlBlockList = [];
    private static $hlBlockTableCalss;

    /**
     * Время кеширования
     *
     * @var integer
     */
    private static $ttl = 0;

    public static function setHlBlockTableCalss(HighloadBlockTable $object)
    {
        static::$hlBlockTableCalss = $object;
    }

    private static function getHlBlockTableClass()
    {
        if (!is_null(static::$hlBlockTableCalss)) {
            return static::$hlBlockTableCalss;
        }

        loadModule('highloadblock');
        return HighloadBlockTable::class;
    }

    /**
     * Список доступных HL блоков
     *
     * @return array
     */
    private static function getListHlBlock(): array
    {
        if (!empty(static::$hlBlockList)) {
            return static::$hlBlockList;
        }

        $hlTableClass = static::getHlBlockTableClass();
        $res = $hlTableClass::getList([
            'cache' => [
                'ttl' => static::$ttl,
            ],
        ]);

        while ($hlBlock = $res->fetch()) {
            static::$hlBlockList['id_' . $hlBlock['ID']] = $hlBlock;
            static::$hlBlockList['name_' . $hlBlock['NAME']] = $hlBlock;
            static::$hlBlockList['table_' . $hlBlock['TABLE_NAME']] = $hlBlock;
        }

        return (array) static::$hlBlockList;
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
     * Информация о HL блоке по идентификатору
     *
     * @param integer $id
     * @return array|null
     */
    public static function getHlblockByID(int $id): ?array
    {
        $list = static::getListHlBlock();
        $hlBlock = $list['id_' . $id];

        return !empty($hlBlock) ? (array) $hlBlock : null;
    }

    /**
     * Информация о HL блоке по названию
     *
     * @param string $name
     * @return array|null
     */
    public static function getHlblockByName(string $name): ?array
    {
        $list = static::getListHlBlock();
        $hlBlock = $list['name_' . $name];

        return !empty($hlBlock) ? (array) $hlBlock : null;
    }

    /**
     * Информация о HL блоке по названию таблицы
     *
     * @param string $tableName
     * @return array|null
     */
    public static function getHlblockByTableName(string $tableName): ?array
    {
        $list = static::getListHlBlock();
        $hlBlock = $list['table_' . $tableName];

        return !empty($hlBlock) ? (array) $hlBlock : null;
    }

    /**
     * Информация о HL блоке
     *
     * @param string $name
     * @return array|null
     */
    public static function getHlblock(string $name): ?array
    {
        $hlBlock = static::getHlblockByID((int) $name);
        if (!is_null($hlBlock)) {
            return (array) $hlBlock;
        }

        $hlBlock = static::getHlblockByName($name);
        if (!is_null($hlBlock)) {
            return (array) $hlBlock;
        }

        $hlBlock = static::getHlblockByTableName($name);
        if (!is_null($hlBlock)) {
            return (array) $hlBlock;
        }

        return null;
    }

    /**
     * Класс для работы с HL блоком
     *
     * @param string $name
     * @return DataManager|null
     */
    public static function getHlblockClass(string $name): ?DataManager
    {
        $hlBlock = static::getHlblock($name);
        if (is_null($hlBlock)) {
            return null;
        }

        $hlTableClass = static::getHlBlockTableClass();
        return $hlTableClass::compileEntity($hlBlock)->getDataClass();
    }
}
