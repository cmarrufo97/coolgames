<?php

use kartik\datecontrol\Module;

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'smtpUsername' => 'coolgamesmailer@gmail.com',
    'amazon' => [
        'region' => 'eu-west-3',
        'credentials' => [
            'key' => getenv('S3_KEY_'),
            'secret' => getenv('S3_SECRET'),
        ],
    ],
    'bsVersion' => '4.x',
    'payPalClientId' => getenv('PAYPAL_ID'),
    'payPalClientSecret' => getenv('PAYPAL_SECRET'),
    'dateControlDisplay' => [
        Module::FORMAT_DATE => 'php:d-m-Y',
        Module::FORMAT_TIME => 'php:H:i:s',
        Module::FORMAT_DATETIME => 'php:d-m-Y H:i:s',
    ],

    // format settings for saving each date attribute (PHP format example)
    'dateControlSave' => [
        Module::FORMAT_DATE => 'php:Y-m-d', // saves as unix timestamp
        Module::FORMAT_TIME => 'php:H:i:s',
        Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
    ],
];
