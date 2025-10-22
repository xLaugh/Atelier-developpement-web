<?php

return [
    'db' => [
        'host' => $_ENV['MYSQL_HOST'] ?? 'db',
        'dbname' => $_ENV['MYSQL_DATABASE'] ?? 'charlymatloc',
        'username' => $_ENV['MYSQL_USER'] ?? 'root',
        'password' => $_ENV['MYSQL_PASSWORD'] ?? ''
    ],
    'jwt' => null
];
