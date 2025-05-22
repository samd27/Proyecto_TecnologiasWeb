<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app = AppFactory::create();
$app->setBasePath('/Proyecto_TecnologiasWeb/olaDeCambio_app/backend');
$app->addErrorMiddleware(true, true, true);

$app->post('/api/reportes', function (Request $request, Response $response) {
    $contentType = $request->getHeaderLine('Content-Type');
    $rawBody = $request->getBody()->getContents();
    $data = [];

    if (strstr($contentType, 'application/json')) {
        $data = json_decode($rawBody, true);
    }

    require_once __DIR__ . '/myapi/CREATE/Create.php';
    $creador = new Create();
    $resultado = $creador->crearReporte($data);

    $payload = json_encode([
        'status' => $resultado ? 'ok' : 'error'
    ]);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
