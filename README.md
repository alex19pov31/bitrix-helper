[![Latest Stable Version](https://poser.pugx.org/alex19pov31/bitrix-helper/v/stable)](https://packagist.org/packages/alex19pov31/bitrix-helper) [![Build Status](https://travis-ci.org/alex19pov31/bitrix-helper.svg?branch=master)](https://travis-ci.org/alex19pov31/bitrix-helper) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex19pov31/bitrix-helper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex19pov31/bitrix-helper/?branch=master)

# Bitrix helper

Коллекция хелперов для CMS Bitrix.

## Установка

```bash
    composer require alex19pov31/bitrix-helper
```

### Получение идентификатора инфоблока по его коду:

* code - Код инфоблока
* iblockType - Тип инфоблока
* minutes - время кеширования списка инфоблоков в минутах

```php
    function getIblockId(string $code, ?string $iblockType = null, int $minutes = 0): ?int;
```

**Пример:**

```php
    /**
     * shoes - код инфоблока
     * catalog - идентификатор типа инфоблока (не обязательный параметр)
     * 30 - время кеширования в минутах
     */
    getIblockId('shoes', 'catalog', 30); // 2
```

### Получение класса для работы с HL блоком

```php
    /**
     *  1 - идентификатор HL блока
     *  30 - время кеширования в минутах
     */
    getHlBlockClass('1', 30);

    /**
     * table_comments - имя теблицы HL блока
     * 30 - время кеширования в минутах
     */
    getHlBlockClass('table_comments', 30);

    /**
     * TableComments - название HL блока
     * 30 - время кеширования в минутах
     */
    getHlBlockClass('TableComments', 30);
```

### Получение информации о HL блоке

```php
    /**
     *  1 - идентификатор HL блока
     *  30 - время кеширования в минутах
     */
    getHlBlock('1', 30);

    /**
     * table_comments - имя теблицы HL блока
     * 30 - время кеширования в минутах
     */
    getHlBlock('table_comments', 30);

    /**
     * TableComments - название HL блока
     * 30 - время кеширования в минутах
     */
    getHlBlock('TableComments', 30);
```

### Подключение модуля

```php
    loadModule('iblock');
```

```php
    bxApp(); // $APPLICATION
```

```php
    appInstance(); // Application::getInstance()
```

### Выполнить произвольный SQL запрос к базе

```php
    echo sql('show tables')->fetch();
```

### Тегированный кеш

```php
    taggedCache();
```

### Инициализация тегированного кеша

```php

    /**
     * ['catalog', 'cars'] - теги для кеша
     * /catalog - расположение кеша
     */
    initTagCache(['catalog', 'cars'], '/catalog')
```

### Кеширование

```php

    /**
     * 30 - время кеширования в минутах
     * cache_key - ключ кеширования
     */
    return cache(30, 'cache_key', '/', 'cache', function() {
        initTagCache(['simple_cache'], '/simple'); // инициализация тегированного кеша

        return "данные которые надо закешировать";
    });
```

### Подключение компонента

```php
    initComponent('bitrix:catalog', 'test', ['CACHE' => 'Y'])->show();

    // Call in component
    $arResult['bitrix:catalog'] = initComponent('bitrix:catalog', 'test_template', ['CACHE' => 'Y']);
    // Call in component template
    $arResult['bitrix:catalog']->show();


    initComponent('bitrix:catalog')
        ->setTemplate('test_template')
        ->setParams(['CACHE' => 'N'])
        ->cache(120, 'cache_key') // Кеширование вывода компонента на 120 минут по ключу cache_key
        ->show();

    /**
     * Инициируем компонент без вывода, 
     * задаем имя в стеке вызова чтобы вызвать в другом месте
     */
    initComponent('bitrix:catalog')
        ->setTemplate('new_template')
        ->setParams(['PARAM' => 'local acces to value'])
        ->setNameInStack('wery_impotant_component'); // задаем имя в стеке вызова компонентов

    use Alex19pov31\BitrixHelper\ComponentHelper;

    /**
     * Получаем компонент из стека по заданному имени и вызываем его
     */
    ComponentHelper::getByNameInStack('wery_impotant_component')->show();

    ComponentHelper::getStack(); // стек вызова компонентов 
```

### Область редактирования элемента инфоблока

* tpl - Объект шаблона компонента
* elementId - Идентификатор элемента
* iblockId - Идентификатор инфоблока
* iblockType - Тип инфоблока
* description - Надпись в области редактирования

```php
    function initEditIblockElement(CBitrixComponentTemplate $tpl, int $elementId, int $iblockId, string $iblockType, string $description = null): string;
```

**Пример:**

```html
    <div id="<?= initEditHLBlockElement($this, 4, 2, 'catalog'); ?>">
        ...
    </div>
```

### Область редактирования раздела инфоблока

* tpl - Объект шаблона компонента
* elementId - Идентификатор элемента
* iblockId - Идентификатор инфоблока
* iblockType - Тип инфоблока
* description - Надпись в области редактирования

```php
    function initEditIblockSection(CBitrixComponentTemplate $tpl, int $sectionId, int $iblockId, string $iblockType, string $description = null): string;
```

**Пример:**

```html
    <div id="<?= initEditIblockSection($this, 4, 2, 'catalog'); ?>">
        ...
    </div>
```

### Область редактирования элемента HL блока

* tpl - Объект шаблона компонента
* elementId - Идентификатор элемента
* hlBlockName - Имя таблицы/имя HL блока/идентификатор HL блока
* description - Надпись в области редактирования

```php
    function initEditHLBlockElement(CBitrixComponentTemplate $tpl, int $elementId, string $hlBlockName, string $description = null): string;
```

**Пример:**

```html
    <div id="<?= initEditHLBlockElement($this, 4, 'hl_table_name', 'catalog'); ?>">
        ...
    </div>

    <div id="<?= initEditHLBlockElement($this, 4, 'HLName', 'catalog'); ?>">
        ...
    </div>

    <div id="<?= initEditHLBlockElement($this, 4, '1', 'catalog'); ?>">
        ...
    </div>
```