<?php

namespace Alex19pov31\BitrixHelper\Iblock;

use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Data\DeleteResult;

abstract class IblockModel
{
    const TTL = 3600;

    /**
     * Объект для работы с инфоблоком
     *
     * @var IblockElementTable
     */
    private static $entity;

    abstract protected static function getIblockCode(): string;

    /**
     * Возвращает элемент по идентификатору
     *
     * @param integer $id
     * @return array|null
     */
    public static function getById(int $id)
    {
        return static::getList([
            'filter' => [
                'ID' => $id,
            ],
        ])->fetch();
    }

    /**
     * Список элементов
     *
     * @param array $params
     * @return Result
     */
    public static function getList(array $params): Result
    {
        if (empty($params['cache'])) {
            $params['cache']['ttl'] = static::TTL;
        }

        return static::getEntity()->getList($params);
    }

    /**
     * Добавление элемента
     *
     * @param array $data
     * @return AddResult
     */
    public static function add(array $data): AddResult
    {
        return static::getEntity()->add($data);
    }

    /**
     * Обновление элемента
     *
     * @param integer $id
     * @param array $data
     * @return UpdateResult
     */
    public static function update(int $id, array $data): UpdateResult
    {
        return static::getEntity()->update($id, $data);
    }

    /**
     * Удаление элемента по идентификатору
     *
     * @param integer $id
     * @return DeleteResult
     */
    public static function delete(int $id): DeleteResult
    {
        return static::getEntity()->delete($id);
    }

    /**
     * Объект для работы с инфоблоком
     *
     * @return IblockElementTable
     */
    private static function getEntity(): IblockElementTable
    {
        if (!is_null(static::$entity)) {
            return static::$entity;
        }

        return static::$entity = new  class (static::getIblockCode()) extends IblockElementTable
        { };
    }
}
