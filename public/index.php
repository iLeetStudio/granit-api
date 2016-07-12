<?php

require 'vendor/autoload.php';

$app = new Slim\App();

$app->get('/v1/patient.get', function ($request, $response, $args) {
    $response->write("Hello, " . $args['name']);
    return $response;
});

$app->run();