<?php
/**
 * ----------------------------------------------------------------------------
 * This code is part of an application or library developed by Datamedrix and
 * is subject to the provisions of your License Agreement with
 * Datamedrix GmbH.
 *
 * @copyright (c) 2019 Datamedrix GmbH
 * ----------------------------------------------------------------------------
 *
 * @author Christian Graf <c.graf@datamedrix.com>
 */

declare(strict_types=1);
 
return [
    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Default settings for all locales where no specific settings are defined.
    | All NULL values will be determined from the operating system locale settings.
    |
    */
    'defaults' => [
        'codeSet' => 'UTF-8',
        'settings' => [
            'decimalPoint' => null,
            'thousandsSeparator' => null,
            'positiveSign' => null,
            'negativeSign' => null,
            'formatting' => [
                'date' => null,
                'datetime' => null,
                'timestamp' => null,
                'time' => null,
                'decimals' => null,
                'number' => null,
                'currency' => null,
            ],
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Available languages
    |--------------------------------------------------------------------------
    |
    | List of available languages and their territories.
    |
    */
    'languages' => [
        'en' => [
            'territories' => [
                'US',
                'GB',
            ],
        ],
        'de' => [
            'territories' => [
                'DE',
                'AT',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale settings
    |--------------------------------------------------------------------------
    |
    | List of locale specific settings.
    |
    */
    'settings' => [
        'en-GB' => [
            'decimalPoint' => null,
            'thousandsSeparator' => null,
            'formatting' => [
                'date' => 'dd.MM.y',
                'datetime' => null,
                'timestamp' => null,
                'time' => null,
                'number' => null,
                'decimal' => 2,
                'money' => null,
            ],
        ],
    ],
];
