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

// Set the base path for the API routes
$app->setBasePath("/api/v1");

$app->addBodyParsingMiddleware();

// Define the API routes
$app->get("/", [ApiMain::class, "index"]);
$app->get("/categories", [ApiMain::class, "getCategories"]);
$app->get("/category/{id}", [ApiMain::class, "getCategoryById"]);
$app->patch("/category/{id}", [ApiMain::class, "updateCategory"]);
$app->delete("/category/{id}", [ApiMain::class,"deleteCategory"]);
$app->get("/products", [ApiMain::class, "getProducts"]);
$app->get("/product/{id}", [ApiMain::class, "getProductById"]);
$app->delete("/product/{id}", [ApiMain::class,"deleteProduct"]);
$app->post("/category", [ApiMain::class, "createCategory"]);
$app->post("/product", [ApiMain::class, "createProduct"]); // PUT was not working, so I changed it to POST.



// Define the authentication route
$app->post("/authenticate", function (Request $request, Response $response) {
    // Access the global configuration.
    global $config;
    $requestBody = $request->getParsedBody();


    // Check if the provided username and password match the configuration
    if ($requestBody["username"] != $config["username"] || $requestBody["password"] != $config["password"]) {
        return $response->withStatus(401, "Invalid credentials");    
    }
    // Create a JWT token
    $token = Token::create($config["username"], $config["password"], time() + 3600, "localhost");

    // Set the token as a cookie and return a 201 status code
    $result = setcookie("token", $token, time() + 3600);
    return $response->withStatus("201");

    
    $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode([$result]));
    return $response->withStatus(201);
});


$app->run();