<?php
chdir(dirname(__DIR__));

return [
  'settings' => [
    'displayErrorDetails' => true, // Set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header
    'address' => 'Moses Oisakede Secretariat, Abubakar Tafawa Balewa University, Bauchi - Nigeria',
    'president' => '<i>Comr. Amb. Abubakar Sadiq Hassan (SMNCS, SMACM)</i>',

    // Renderer settings
    'renderer' => [
      'template_path' => 'templates/'
    ],

    // Monolog settings
    'logger' => [
      'name' => 'slim-app',
      'path' => isset($_ENV['docker']) ? 'php://stdout' : 'logs/app.log',
      'level' => \Monolog\Logger::DEBUG
    ],

    // Illuminate/database configuration
    'db' => [
      'driver' => getenv('DB_DRIVER'),
      'host'=> getenv('DB_HOST'),
      'db_port' => getenv('DB_PORT'),
      'database' => getenv('DB_NAME'),
      'username' => getenv('DB_USER'),
      'password' => getenv('DB_PASS'),
      'charset' => getenv('DB_CHARSET'),
      'collation' => getenv('DB_COLLATION'),
      'prefix' => getenv('DB_PREFIX')
    ],

    // PHPMailer settings
    'PHPMailer' => [
      'SMTPDebug' => getenv('MAIL_SMTPDebug'),
      'isSMTP' => getenv('MAIL_isSMTP'),
      'host' => getenv('MAIL_HOST'),
      'SMTPAuth' => getenv('MAIL_SMTPAuth'),
      'username' => getenv('MAIL_USERNAME'),
      'password' => getenv('MAIL_PASSWORD'),
      'SMTPSecure' => getenv('MAIL_SMTPSecure'),
      'port' => getenv('MAIL_PORT')
    ]
  ]
];
