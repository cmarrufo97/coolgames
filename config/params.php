<?php

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
];
