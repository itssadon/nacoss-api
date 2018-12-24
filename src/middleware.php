<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

// This middleware will add the Access-Control-Allow-Methods header to every request
$app->add(function($request, $response, $next) {
  $route = $request->getAttribute("route");

  $methods = [];

  if (!empty($route)) {
    $pattern = $route->getPattern();

    foreach ($this->router->getRoutes() as $route) {
      if ($pattern === $route->getPattern()) {
        $methods = array_merge_recursive($methods, $route->getMethods());
      }
    }
    // Methods holds all of the HTTP Verbs that a particular route handles.
  } else {
    $methods[] = $request->getMethod();
  }

  $response = $next($request, $response);

  return $response->withHeader("Access-Control-Allow-Methods", implode(",", $methods));
});