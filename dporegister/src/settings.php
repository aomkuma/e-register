<?php
include 'settings_var.php';
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::INFO,
            'maxFiles' => 90
        ],
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'e-register',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'prefix'    => 'tbl_',
            'port' => '3306'
        ],
        'sms' => [
            'from' => $SMS_FROM,
            
            'url' => $SMS_ENDPOINT,
            'username' => $SMS_USERNAME,
            'password' => $SMS_PASSWORD,
        ],
        
        'ldap' => [
            'host' => $AD_HOST,
            'port' => $AD_PORT,
            'active' => 'N', // Y,N
            'principal' => $AD_PRINCIPAL
        ]
    ],
];
