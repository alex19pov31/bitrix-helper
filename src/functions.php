<?php

use Alex19pov31\BitrixHelper\ComponentHelper;
use Alex19pov31\BitrixHelper\HlBlockHelper;
use Alex19pov31\BitrixHelper\IblockHelper;
use Bitrix\Main\Application;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Data\TaggedCache;
use Bitrix\Main\DB\Exception;
use Bitrix\Main\DB\Result;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Data\DataManager;

/**
 * Entry point bitrix application
 *
 * @return CMain
 */
function bxApp(): CMain
{
    global $APPLICATION;
    return $APPLICATION;
}

function appInstance(): Application
{
    return Application::getInstance();
}

/**
 * Выполнить sql запрос
 *
 * @param string $sql
 * @return Result
 */
function sql(string $sql): Result
{
    return appInstance()->getConnection()->query($sql);
}

/**
 * Загрузить модуль
 *
 * @param string $moduleName
 * @return boolean
 */
function loadModule(string $moduleName): bool
{
    return (bool) Loader::includeModule($moduleName);
}

/**
 * Идентификатор инфоблока
 *
 * @param string $code
 * @param string|null $iblockType
 * @return integer|null
 */
function getIblockId(string $code, $iblockType = null, int $minutes = 0)
{
    IblockHelper::setCacheTime($minutes);
    return IblockHelper::getIblockID($code, $iblockType);
}

/**
 * Информация о HL блоке
 *
 * @param string $code
 * @param integer $minutes
 * @return array|null
 */
function getHlBlock(string $code, $minutes = 0)
{
    HlBlockHelper::setCacheTime($minutes);
    return HlBlockHelper::getHlblock($code);
}

/**
 * Класс для работы с HL блоком
 *
 * @param string $code
 * @param integer $minutes
 * @return DataManager|null
 */
function getHlBlockClass(string $code, $minutes = 0)
{
    HlBlockHelper::setCacheTime($minutes);
    return HlBlockHelper::getHlblockClass($code);
}

/**
 * Тэггированный кеш
 *
 * @return TaggedCache
 */
function taggedCache(): TaggedCache
{
    return appInstance()->getTaggedCache();
}

/**
 * Инициализация тэггированного кеша
 *
 * @param array $tags
 * @param string $cacheDir
 * @return void
 */
function initTagCache(array $tags, string $cacheDir = '/')
{
    taggedCache()->startTagCache($cacheDir);
    foreach ($tags as $tag) {
        taggedCache()->registerTag($tag);
    }
    taggedCache()->endTagCache();
}

/**
 * Кеширование
 *
 * @param integer $minutes
 * @param string $key
 * @param string $initDir
 * @param string $baseDir
 * @param callable $func
 * @return mixed
 */
function cache(int $minutes, string $key, $initDir = '/', string $baseDir = 'cache', callable $func)
{
    $data = null;
    $ttl = $minutes * 60;
    $cache = new Cache(Cache::createInstance());
    if ($cache->initCache($ttl, $key, $initDir, $baseDir)) {
        $data = $cache->getVars();
    } elseif ($cache->startDataCache($ttl, $key, $initDir, [], $baseDir)) {
        try {
            $data = $func();
            $cache->endDataCache($data);
        } catch (Exception $e) {
            $cache->abortDataCache();
        }
    }

    return $data;
}

/**
 * Область редактирования элемента инфоблока
 *
 * @param CBitrixComponentTemplate $tpl
 * @param integer $elementId
 * @param integer $iblockId
 * @param string $iblockType
 * @param string $description
 * @return string
 */
function initEditIblockElement(CBitrixComponentTemplate $tpl, int $elementId, int $iblockId, string $iblockType, string $description = null): string
{
    $link = '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=' . $iblockId . '&type=' . $iblockType . '&ID=' . $elementId . '&lang=ru&force_catalog=&filter_section=0&bxpublic=Y&from_module=iblock';
    if (is_null($description)) {
        $description = "Редактировать элемент";
    }

    $tpl->AddEditAction($elementId, $link, $description);

    return (string) $tpl->GetEditAreaId($elementId);
}

/**
 * Область редактирования раздела инфоблока
 *
 * @param CBitrixComponentTemplate $tpl
 * @param integer $sectionId
 * @param integer $iblockId
 * @param string $iblockType
 * @param string $description
 * @return string
 */
function initEditIblockSection(CBitrixComponentTemplate $tpl, int $sectionId, int $iblockId, string $iblockType, string $description = null): string
{
    $link = '/bitrix/admin/iblock_section_edit.php?IBLOCK_ID=' . $iblockId . '&type=' . $iblockType . '&ID=' . $sectionId . '&lang=ru&find_section_section=0&bxpublic=Y&from_module=iblock';
    if (is_null($description)) {
        $description = "Редактировать раздел";
    }

    $tpl->AddEditAction($sectionId, $link, $description);

    return (string) $tpl->GetEditAreaId($sectionId);
}

/**
 * Область редактирования элемента HL блока
 *
 * @param CBitrixComponentTemplate $tpl
 * @param integer $elementId
 * @param string $hlBlockName
 * @param string $description
 * @return string
 */
function initEditHLBlockElement(CBitrixComponentTemplate $tpl, int $elementId, string $hlBlockName, string $description = null): string
{
    $hlBlock = getHlBlock($hlBlockName);
    if (empty($hlBlock)) {
        return '';
    }

    $link = '/bitrix/admin/highloadblock_row_edit.php?ENTITY_ID=' . $hlBlock['ID'] . '&ID=' . $elementId . '&bxpublic=Y';
    if (is_null($description)) {
        $description = "Редактировать элемент";
    }

    $tpl->AddEditAction($elementId, $link, $description);

    return (string) $tpl->GetEditAreaId($elementId);
}

/**
 * Инициализация компонента
 *
 * @param string $name
 * @param string $template
 * @param array $params
 * @param mixed $parentComponent
 * @param array $functionParams
 * @return ComponentHelper
 */
function initComponent(string $name, string $template = '', array $params = [], $parentComponent = null, $functionParams = []): ComponentHelper
{
    return new ComponentHelper($name, $template, $params, $parentComponent, $functionParams);
}

/**
 * Включаемая область
 *
 * @param string $path
 * @param string|null $folder
 * @return void
 */
function includeArea(string $path, $basePath = null)
{
    if (is_null($basePath)) {
        $basePath = bxApp()->GetTemplatePath('') . 'include';
    }

    initComponent('bitrix:main.include')
        ->setTemplate('.default')
        ->setParams([
            "AREA_FILE_SHOW" => "file",
            "AREA_FILE_SUFFIX" => "inc",
            "AREA_FILE_RECURSIVE" => "Y",
            "EDIT_TEMPLATE" => "",
            "COMPONENT_TEMPLATE" => ".default",
            "PATH" => "{$basePath}/{$path}",
        ])
        ->show();
}
