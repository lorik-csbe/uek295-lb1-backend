<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
class ApiMain {

    public static function getCategories(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        $statement = $database->prepare("SELECT category_id, active, name FROM category");
        $result = $statement->execute();
        $result = $statement->get_result();

        $categories = $result->fetch_all(MYSQLI_ASSOC);


        $response->getBody()->write(json_encode($categories));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200);   

    }

    public static function getProducts(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        $statement = $database->prepare("SELECT product_id, sku, active, id_category, name, image, description, price, stock FROM product");
        $result = $statement->execute();
        $result = $statement->get_result();

        $products = $result->fetch_all(MYSQLI_ASSOC);


        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200);   

    }

    public static function getProductById(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        $id = $args["id"];

        $statement = $database->prepare("SELECT product_id, sku, active, id_category, name, image, description, price, stock FROM product WHERE product_id = ?");
        $statement->bind_param("i", $id);
        $result = $statement->execute();
        $result = $statement->get_result();

        $product = $result->fetch_assoc();

        if (!$product) {
            $response->getBody()->write(json_encode(["error" => "Product not found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }


        $response->getBody()->write(json_encode($product));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200); 

    }
    public static function getCategoryById(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        $id = $args["id"];

        $statement = $database->prepare("SELECT category_id, active, name FROM category WHERE category_id = ?");
        $statement->bind_param("i", $id);
        $result = $statement->execute();
        $result = $statement->get_result();

        $category = $result->fetch_assoc();

        if (!$category) {
            $response->getBody()->write(json_encode(["error" => "Category not found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }


        $response->getBody()->write(json_encode($category));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200); 

    }



    public static function createCategory(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        $body = $request->getParsedBody();
        $statement = $database->prepare("INSERT INTO category (active, name) VALUES (?, ?)");

        $result = $statement->execute();
        $result = $statement->get_result();
        
        $name = $body["name"];
        $active = $body["active"];



        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(201);

        if ($name == '' || $active == '') {
            return $response->withStatus(400, "Bad Request: Missing required fields");
        }

    }

    public static function index(Request $request, Response $response, $args) {

    }
}