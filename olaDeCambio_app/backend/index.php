<?php
require __DIR__ . '/../../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

// Ruta base
$app->get('/api', function ($request, $response, $args) {
    $response->getBody()->write("Bienvenido a la API ODS 14");
    return $response;
});

// Aquí agregarás tus rutas para CRUD (en CREATE, READ, etc.)

$app->run();
