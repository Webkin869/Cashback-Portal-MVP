<?php
return [
    'app_name' => 'Cashback Portal MVP',
    'app_url' => 'http://localhost:8080',
    'frontend_url' => 'http://localhost:5173',
    'db' => [
        // sqlite yoki mysql ishlatishingiz mumkin
        // sqlite uchun:
        'driver' => 'sqlite',
        'sqlite_path' => __DIR__ . '/../database/database.sqlite',

        // mysql uchun pastdagilarni moslang va driver ni mysql qiling
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'cashback_portal',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'security' => [
        'jwt_secret' => 'change_this_secret_key_for_production_123456',
        'password_algo' => PASSWORD_BCRYPT,
        'min_payout' => 25,
    ],
];
