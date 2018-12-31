<?php

use Awurth\SlimValidation\Validator;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Capsule\Manager;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Handler\StreamHandler;
use NACOSS\Exceptions\Handler;

// DIC configuration
$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Logger($settings['name']);
    $logger->pushProcessor(new UidProcessor());
    $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Service factory for the ORM
$container['capsule'] = function ($container) {
    $capsule = new Manager;
    $capsule->addConnection($container['settings']['db']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    $capsule->getContainer()->singleton(
        ExceptionHandler::class,
        Handler::class
    );
    return $capsule;
};

// validator
$container['validator'] = function () {
	return new Validator();
};

// copyright year
$container['copyrightYear'] = function () {
    return date('Y');
};