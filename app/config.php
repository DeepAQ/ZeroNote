<?php

return [
    'db' => [
        'dsn' => 'sqlite:zeronote.sqlite3',
        'user' => '',
        'pass' => ''
    ],

    'cache' => [
        'provider' => '\BestLang\ext\cache\WinCache'
    ],

    'token' => [
        'provider' => '\BestLang\ext\token\JWT',
        'options' => [
            'signer' => '\Lcobucci\JWT\Signer\Hmac\Sha256',
            'key' => '2Pr{sr4$Z^UR=_(~laT6u[2:n*EbP)aG'
        ]
    ]
];