<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitea45c4a58e6bcd2d117689eec8569b35
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Karencilla\\TpFinalPreguntas\\' => 28,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Karencilla\\TpFinalPreguntas\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'M' => 
        array (
            'Mustache' => 
            array (
                0 => __DIR__ . '/..' . '/mustache/mustache/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitea45c4a58e6bcd2d117689eec8569b35::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitea45c4a58e6bcd2d117689eec8569b35::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitea45c4a58e6bcd2d117689eec8569b35::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitea45c4a58e6bcd2d117689eec8569b35::$classMap;

        }, null, ClassLoader::class);
    }
}
