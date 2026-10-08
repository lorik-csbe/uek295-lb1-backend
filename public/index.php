<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use OpenApi\Attributes as OAT; 
use ReallySimpleJWT\Token;

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/api/api-main.php"; 

$app = AppFactory::create();

$config = json_decode(file_get_contents(__DIR__ . "/../config.json"), true);


$app->setBasePath("/api/v1");

$app->addBodyParsingMiddleware();

$app->get("/", [ApiMain::class, "index"]);
$app->get("/categories", [ApiMain::class, "getCategories"]);
$app->get("/category/{id}", [ApiMain::class, "getCategoryById"]);
$app->patch("/category/{id}", [ApiMain::class, "updateCategory"]);
$app->delete("/category/{id}", [ApiMain::class,"deleteCategory"]);
$app->get("/products", [ApiMain::class, "getProducts"]);
$app->get("/product/{id}", [ApiMain::class, "getProductById"]);
$app->delete("/product/{id}", [ApiMain::class,"deleteProduct"]);
$app->post("/category", [ApiMain::class, "createCategory"]);
$app->put("/product", [ApiMain::class, "createProduct"]);



$app->post("/authenticate", function (Request $request, Response $response) {
    global $config;
    $requestBody = $request->getParsedBody();

    if ($requestBody["username"] != $config["username"] || $requestBody["password"] != $config["password"]) {
        return $response->withStatus(401, "Invalid credentials");    
    }

    $token = Token::create($config["username"], $config["password"], time() + 3600, "localhost");

    setcookie("token", $token, time() + 3600);
    return $response->withStatus("204");

});


$app->run();