<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use OpenApi\Attributes as OAT; 

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/api/api-main.php"; 

$app = AppFactory::create();

$app->setBasePath("/api/v1");

$app->addBodyParsingMiddleware();

$app->get("/", [ApiMain::class, "index"]);

$app->run();