<?php

use \Slim\App;
//use \Dotenv\Dotenv;

date_default_timezone_set('Africa/Lagos');

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

// All file paths relative to root
chdir(dirname(__DIR__));

require 'vendor/autoload.php';

session_start();

//$dotenv = new Dotenv(__DIR__);
//$dotenv->load();

// Instantiate the app
$settings = require 'src/settings.php';
$app = new App($settings);

// Set up dependencies
require 'src/dependencies.php';

// Register middleware
require 'src/middleware.php';

// Register routes
require 'src/routes.php';

// Register the database connection with Eloquent
$capsule = $app->getContainer()->get('capsule');
$capsule->bootEloquent();

// Run app
$app->run();
