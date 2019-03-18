<?php

namespace Alex19pov31\BitrixHelper;

class ComponentHelper
{
    private static $componentStack;

    /**
     * Название компонента
     *
     * @var string
     */
    private $name;

    /**
     * Шаблон компонента
     *
     * @var string
     */
    private $template;

    /**
     * Параметры компонента
     *
     * @var array
     */
    private $params;

    /**
     * Параметры родительского компонента
     *
     * @var array|null
     */
    private $parentComponent;

    /**
     * Undocumented variable
     *
     * @var array
     */
    private $functionParams;

    /**
     * Данные возвращаемые компонентом
     *
     * @var mixed
     */
    private $returnedData;

    /**
     * Данные для вывода компонента
     *
     * @var string
     */
    private $output;

    /**
     * Время кеширования
     *
     * @var int
     */
    private $ttl = 0;

    private $app;

    /**
     * Ключ кеширования
     *
     * @var string|null
     */
    private $cachekey = null;

    /**
     * Список вызванных компонентов
     *
     * @return array|null
     */
    public static function getStack()
    {
        return static::$componentStack;
    }

    /**
     * Возвращает компонент из стека по ключу
     *
     * @param string $name
     * @return ComponentHelper|null
     */
    public static function getByNameInStack(string $name)
    {
        $component = static::$componentStack[$name];
        return !empty($component) ? $component : null;
    }

    public function __construct(string $name, string $template = '', array $params = [], $parentComponent = null, $functionParams = [], $app = null)
    {
        $this->name = $name;
        $this->app = !is_null($app) ? $app : bxApp();

        $this->setTemplate($template);
        $this->setParams($params);
        $this->setParentComponent($parentComponent);
        $this->setFunctionParams($functionParams);
    }

    public function getReturnedData()
    {
        return $this->returnedData;
    }

    /**
     * Название компонента
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->name;
    }

    /**
     * Установить шаблон компонента
     *
     * @param string $template
     * @return ComponentHelper
     */
    public function setTemplate(string $template = ''): ComponentHelper
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Шаблон компонента
     *
     * @return string
     */
    public function getTemplate(): string
    {
        return (string) $this->template;
    }

    /**
     * Указать ключ компонента в стеке
     *
     * @param string $name
     * @return ComponentHelper
     */
    public function setNameInStack(string $name): ComponentHelper
    {
        static::$componentStack[$name] = &$this;
        return $this;
    }

    /**
     * Установить параметры родительского компонента
     *
     * @param array|null $parentComponent
     * @return ComponentHelper
     */
    public function setParentComponent($parentComponent): ComponentHelper
    {
        $this->parentComponent = $parentComponent;
        return $this;
    }

    /**
     * Параметры родительского компонента
     *
     * @return array|null
     */
    public function getParentComponent()
    {
        return $this->parentComponent;
    }

    public function setFunctionParams(array $functionParams): ComponentHelper
    {
        $this->functionParams = $functionParams;
        return $this;
    }

    public function getFunctionParams(): array
    {
        return (array) $this->functionParams;
    }

    /**
     * Установить параметры компонента
     *
     * @param array $params
     * @return ComponentHelper
     */
    public function setParams(array $params): ComponentHelper
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Параметры компонета
     *
     * @return array
     */
    public function getParams(): array
    {
        return (array) $this->params;
    }

    /**
     * Установить параметр компонента
     *
     * @param string $name
     * @param mixed $value
     * @return ComponentHelper
     */
    public function setParam(string $name, $value): ComponentHelper
    {
        $this->params[$name] = $value;
        return $this;
    }

    private function executeWithoutCache(): string
    {
        ob_start();
        $this->returnedData = $this->app->IncludeComponent(
            $this->name,
            $this->template,
            $this->params,
            $this->parentComponent,
            $this->functionParams
        );
        return ob_get_clean();
    }

    public function execute(): string
    {
        if ($this->ttl == 0) {
            return $this->executeWithoutCache();
        }

        return $this->output = cache($this->ttl, $this->getCacheKey(), '/components', 'cache', function () {
            return $this->executeWithoutCache();
        });
    }

    /**
     * Вывод компонента
     *
     * @return void
     */
    public function show(bool $cachedOutput = false)
    {
        if ($cachedOutput && !is_null($this->output)) {
            echo $this->output;
        }

        echo $this->execute();
        static::$componentStack[] = $this;
    }

    public function getCacheKey(): string
    {
        if (!is_null($this->cachekey)) {
            return (string) $this->cachekey;
        }

        $dataKey = [
            $this->name,
            $this->template,
            $this->params,
            $this->parentComponent,
            $this->functionParams,
        ];

        return md5(json_encode($dataKey));
    }

    public function cache(int $minutes, $key = null)
    {
        $this->ttl = $minutes * 60;
        $this->cacheKey = null;
        return $this;
    }
}
