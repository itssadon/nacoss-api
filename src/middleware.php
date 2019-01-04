<?php
// Application middleware
use RKA\Middleware\IpAddress;

// e.g: $app->add(new \Slim\Csrf\Guard);

// Middleware to add Access-Control-Allow-Origin and Access-Control-Allow-Methods to response readers
$app->add(new \Eko3alpha\Slim\Middleware\CorsMiddleware([
  '*' => ['GET', 'POST', 'PUT']
]));

// Middleware to Retrieving IP address
$checkProxyHeaders = true;
$trustedProxies = ['10.0.0.1', '10.0.0.2'];
$app->add(new IpAddress($checkProxyHeaders, $trustedProxies));
