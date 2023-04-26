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