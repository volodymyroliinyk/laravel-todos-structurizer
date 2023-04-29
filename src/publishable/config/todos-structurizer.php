<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Directories for scanning.
    |--------------------------------------------------------------------------
    |
    | List of directories for scanning.
    |
    */

    'directories' => [
        '/',
        'app',
        'config',
        'database',
        'lang',
        'public/js',
        'resources/views',
        'routes',
        'tests',

        /* TODO:[todos-structurizer]:
           - bug: folder inside ignored folder ignored too, need provide exclusion rules.
         :ENDTODO */

        // For testing or demonstration.
        'vendor/volodymyroliinyk/laravel-todos-structurizer',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored directories for scanning.
    |--------------------------------------------------------------------------
    |
    | List of ignored directories for scanning.
    |
    */

    'directories-ignored' => [
        'bootstrap/cache',
        'database/backup/local',
        'hooks',
        'storage',
        'vendor',
        '.git',
        '.idea',
    ],

];
