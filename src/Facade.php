<?php
/**
 * Facades for Yii 2
 *
 * @see       https://github.com/sergeymakinen/yii2-facades
 * @copyright Copyright (c) 2016-2017 Sergey Makinen (https://makinen.ru)
 * @license   https://github.com/sergeymakinen/yii2-facades/blob/master/LICENSE The MIT License
 */

namespace yii\support\facades;

use yii\base\Application;
use yii\base\InvalidConfigException;

/**
 * Base facade class.
 */
abstract class Facade
{
    /**
     * Facaded component property accessors.
     *
     * @var array
     */
    private static $accessors = [];

    /**
     * The facaded application.
     *
     * @var Application
     */
    private static $app;

    /**
     * Facaded components.
     *
     * @var object[]
     */
    private static $components = [];

    /**
     * Prevents the class to be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Prevents the class to be serialized.
     */
    private function __wakeup()
    {
    }

    /**
     * Prevents the class to be cloned.
     */
    private function __clone()
    {
    }

    /**
     * Redirects static calls to component instance calls.
     *
     * @inheritDoc
     */
    public static function __callStatic($name, $arguments)
    {
        $id = static::getFacadeComponentId();
        if (!isset(self::$accessors[$id])) {
            self::$accessors[$id] = [];
            foreach ((new \ReflectionClass(static::getFacadeComponent()))->getProperties(
                \ReflectionProperty::IS_PUBLIC & ~\ReflectionProperty::IS_STATIC
            ) as $property) {
                $accessor = ucfirst($property->getName());
                self::$accessors[$id]['get' . $accessor] = $property->getName();
                self::$accessors[$id]['set' . $accessor] = $property->getName();
            }
        }
        if (isset(self::$accessors[$id][$name])) {
            if ($name[0] === 'g') {
                return static::getFacadeComponent()->{self::$accessors[$id][$name]};
            } else {
                static::getFacadeComponent()->{self::$accessors[$id][$name]} = reset($arguments);
                return null;
            }
        } else {
            return call_user_func_array([
                static::getFacadeComponent(),
                $name,
            ], $arguments);
        }
    }

    /**
     * Clears a resolved facade component.
     *
     * @param string $id
     */
    public static function clearResolvedFacadeComponent($id)
    {
        unset(self::$accessors[$id], self::$components[$id]);
    }

    /**
     * Clears all resolved facade components.
     */
    public static function clearResolvedFacadeComponents()
    {
        self::$accessors = [];
        self::$components = [];
    }

    /**
     * Returns a component ID being facaded.
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function getFacadeComponentId()
    {
        throw new InvalidConfigException('Facade must implement getFacadeComponentId method.');
    }

    /**
     * Returns a component being facaded.
     *
     * @return object
     */
    public static function getFacadeComponent()
    {
        $id = static::getFacadeComponentId();
        if (!isset(self::$components[$id])) {
            self::$components[$id] = static::getFacadeApplication()->get($id);
        }
        return self::$components[$id];
    }

    /**
     * Returns the facaded application.
     *
     * @return Application
     */
    public static function getFacadeApplication()
    {
        if (!isset(self::$app)) {
            self::$app = \Yii::$app;
        }
        return self::$app;
    }

    /**
     * Sets the facaded application.
     *
     * @param Application $value
     */
    public static function setFacadeApplication(Application $value)
    {
        self::$app = $value;
        self::clearResolvedFacadeComponents();
    }
}
