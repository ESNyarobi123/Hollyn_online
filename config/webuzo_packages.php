<?php
// config/webuzo_packages.php

return [

    /*
    |--------------------------------------------------------------------------
    | Mapping ya Laravel Plans -> Webuzo Package + Limits
    |--------------------------------------------------------------------------
    | Tumia keys zifuatazo kulingana na unavyopata plan:
    |   - 'name:Hollyn Lite'
    |   - 'slug:hollyn-lite'
    |   - 'id:3'
    |
    | 'package' ni JINA la package lililopo Webuzo (Admin -> Plans/Packages).
    | 'limits' zimewekwa kwa MB (Webuzo hupokea MB).
    */

    // ----------------------
    // Hollyn Lite
    // ----------------------
    'name:Hollyn Lite' => [
        'package' => 'hollyn-lite',   // badili kama package yako Webuzo ina jina jingine
        'limits'  => [
            'disk_mb'      => 5 * 1024,      // 5GB
            'bandwidth_mb' => 50 * 1024,     // 50GB
            'addons'       => 0,
            'emails'       => 20,
            'dbs'          => 5,
            'ftp'          => 5,
        ],
    ],
    'slug:hollyn-lite' => [
        'package' => 'hollyn-lite',
        'limits'  => [
            'disk_mb'      => 5 * 1024,
            'bandwidth_mb' => 50 * 1024,
            'addons'       => 0,
            'emails'       => 20,
            'dbs'          => 5,
            'ftp'          => 5,
        ],
    ],

    // ----------------------
    // Hollyn Grow
    // ----------------------
    'name:Hollyn Grow' => [
        'package' => 'hollyn-grow',
        'limits'  => [
            'disk_mb'      => 20 * 1024,     // 20GB
            'bandwidth_mb' => 200 * 1024,    // 200GB
            'addons'       => 5,
            'emails'       => 200,
            'dbs'          => 20,
            'ftp'          => 20,
        ],
    ],
    'slug:hollyn-grow' => [
        'package' => 'hollyn-grow',
        'limits'  => [
            'disk_mb'      => 20 * 1024,
            'bandwidth_mb' => 200 * 1024,
            'addons'       => 5,
            'emails'       => 200,
            'dbs'          => 20,
            'ftp'          => 20,
        ],
    ],

    // ----------------------
    // Hollyn Boost
    // ----------------------
    'name:Hollyn Boost' => [
        'package' => 'hollyn-boost',
        'limits'  => [
            'disk_mb'      => 50 * 1024,     // 50GB
            'bandwidth_mb' => 500 * 1024,    // 500GB
            'addons'       => 10,
            'emails'       => 500,
            'dbs'          => 50,
            'ftp'          => 50,
        ],
    ],
    'slug:hollyn-boost' => [
        'package' => 'hollyn-boost',
        'limits'  => [
            'disk_mb'      => 50 * 1024,
            'bandwidth_mb' => 500 * 1024,
            'addons'       => 10,
            'emails'       => 500,
            'dbs'          => 50,
            'ftp'          => 50,
        ],
    ],

    // ----------------------
    // Hollyn Max
    // ----------------------
    'name:Hollyn Max' => [
        'package' => 'hollyn-max',
        'limits'  => [
            'disk_mb'      => 100 * 1024,    // 100GB
            'bandwidth_mb' => 1000 * 1024,   // 1TB
            'addons'       => 50,
            'emails'       => 1000,
            'dbs'          => 100,
            'ftp'          => 100,
        ],
    ],
    'slug:hollyn-max' => [
        'package' => 'hollyn-max',
        'limits'  => [
            'disk_mb'      => 100 * 1024,
            'bandwidth_mb' => 1000 * 1024,
            'addons'       => 50,
            'emails'       => 1000,
            'dbs'          => 100,
            'ftp'          => 100,
        ],
    ],

    // ----------------------
    // Fallback (ikikosa match juu)
    // ----------------------
    '_default' => [
        // tumia default package kutoka env yako (imependekezwa iwe plan ya chini)
        'package' => env('SERVICES_WEBUZO_DEFAULT_PACKAGE', env('WEBUZO_DEFAULT_PACKAGE', 'hollyn-lite')),
        'limits'  => [
            'disk_mb'      => 5 * 1024,      // 5GB
            'bandwidth_mb' => 50 * 1024,     // 50GB
            'addons'       => 0,
            'emails'       => 20,
            'dbs'          => 5,
            'ftp'          => 5,
        ],
    ],
];
