<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit93e5a7db8d96bda11f5f89ffb3ad1b77
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit93e5a7db8d96bda11f5f89ffb3ad1b77', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit93e5a7db8d96bda11f5f89ffb3ad1b77', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit93e5a7db8d96bda11f5f89ffb3ad1b77::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
