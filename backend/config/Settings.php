<?php
return [
    'db' => [
        'host' => 'db',        
        'dbname' => 'charlymatloc',
        'username' => 'root',
        'password' => ''
    ],
    'jwt' => [
        'secret' => $_ENV['JWT_SECRET'] ?? 'charlymatloc_secret_key_2025',
        'algorithm' => 'HS256',
        'expiration' => 3600 // 1 heure
    ]
];
