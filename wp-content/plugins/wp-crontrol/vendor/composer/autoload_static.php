<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfaa4112f61e504ad0807660e78dee2ec
{
    public static $classMap = array (
        'Crontrol\\Event\\Table' => __DIR__ . '/../..' . '/src/event-list-table.php',
        'Crontrol\\Request' => __DIR__ . '/../..' . '/src/request.php',
        'Crontrol\\Schedule_List_Table' => __DIR__ . '/../..' . '/src/schedule-list-table.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitfaa4112f61e504ad0807660e78dee2ec::$classMap;

        }, null, ClassLoader::class);
    }
}
