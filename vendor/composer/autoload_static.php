<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7273a
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RjoshiWebdev\\GoogleSignIn\\' => 19,
        ),
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RjoshiWebdev\\GoogleSignIn\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Pimple' => 
            array (
                0 => __DIR__ . '/..' . '/pimple/pimple/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7273a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7273a::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit7273a::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit7273a::$classMap;

        }, null, ClassLoader::class);
    }
}