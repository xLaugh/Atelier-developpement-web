<?php

return [
    'db' => [
        'host' => $_ENV['MYSQL_HOST'] ?? 'db',
        'dbname' => $_ENV['MYSQL_DATABASE'] ?? 'charlymatloc',
        'username' => $_ENV['MYSQL_USER'] ?? 'root',
        'password' => $_ENV['MYSQL_PASSWORD'] ?? ''
    ],
    'jwt' => [
        'secret' => $_ENV['JWT_SECRET'] ?? 'charlymatloc_secret_key_2025',
        'algorithm' => 'HS256',
        'expiration' => 3600 // 1 heure
    ]
];
