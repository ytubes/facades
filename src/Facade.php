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
     * The application instance being facaded.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected static $app;
    /**
     * The resolved object instances.
     *
     * @var array
     */
    protected static $resolvedInstance;


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
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new InvalidConfigException('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * @param  string|object  $name
     * @return mixed
     */
    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }
        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        return static::$resolvedInstance[$name] = static::getFacadeApplication()->get($name);
    }

    /**
     * Clear a resolved facade instance.
     *
     * @param  string  $name
     * @return void
     */
    public static function clearResolvedInstance($name)
    {
        unset(static::$resolvedInstance[$name]);
    }
    /**
     * Clear all of the resolved instances.
     *
     * @return void
     */
    public static function clearResolvedInstances()
    {
        static::$resolvedInstance = [];
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
    }

    /**
     * Redirects static calls to component instance calls.
     *
     * @inheritDoc
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (! $instance) {
            throw new InvalidConfigException('A facade root has not been set.');
        }
        return $instance->{$method}(...$args);
	}
}
