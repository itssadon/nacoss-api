<?php
chdir(dirname(__DIR__));

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        "determineRouteBeforeAppMiddleware" => true, // This Slim setting is required for the middleware to work

        // Renderer settings
        'renderer' => [
            'template_path' => 'templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : 'logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Illuminate/database configuration
        'db' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'db_port'   => '3306',
            'database'  => 'nacossor_national',
            'username'  => 'nacossor',
            'password'  => 'Nacoss@123!',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix'    => '',
        ],
    ],
];
