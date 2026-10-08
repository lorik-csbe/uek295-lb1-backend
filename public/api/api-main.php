<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
class ApiMain {
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

    public static function deleteProduct(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        $id = $args["id"];

        $statement = $database->prepare("DELETE FROM product WHERE product_id = ?");
        $statement->bind_param("i", $id);


        $result = $statement->execute();

        $deletedProduct = [
            "product_id"=> $id
        ];

        if ($statement->affected_rows === 0) {
            $response->getBody()->write(json_encode(["error" => "Product not found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($deletedProduct));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200);
    }

    public static function updateCreateProduct(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        $body = $request->getParsedBody();
        $active = $body["active"];
        $id_category = $body["id_category"];
        $name = $body["name"];
        $image = $body["image"];
        $description = $body["description"];
        $price = $body["price"];
        $stock = $body["stock"];

        
        if ($name == '' || $active == '' || $price == '' || $stock == '') {
            return $response->withStatus(400, "Bad Request: Missing required fields");

        }
        
        $statement = $database->prepare("INSERT INTO product (active, id_category, name, image, description, price, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $statement->bind_param("iisssii", $active, $id_category, $name, $image, $description, $price, $stock);

        $result = $statement->execute();

        $createdProduct = [
            "active"=> $active,
            "id_category"=> $id_category,
            "name"=> $name,
            "image"=> $image,
            "description"=> $description,
            "price"=> $price,
            "stock"=> $stock

        ];

    

        $response->getBody()->write(json_encode($createdProduct));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(201);



        
    }

    public static function getCategories(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        $statement = $database->prepare("SELECT category_id, active, name FROM category");
        $result = $statement->execute();
        $result = $statement->get_result();

        $categories = $result->fetch_all(MYSQLI_ASSOC);


        $response->getBody()->write(json_encode($categories));
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
        $name = $body["name"];
        $active = $body["active"];
        
        if ($name == '' || $active == '') {
            return $response->withStatus(400, "Bad Request: Missing required fields");

        }
        
        $statement = $database->prepare("INSERT INTO category (active, name) VALUES (?, ?)");
        $statement->bind_param("is", $active, $name);

        $result = $statement->execute();

        $createdCategory = [
            "active"=> $active,
            "name"=> $name
        ];

    

        $response->getBody()->write(json_encode($createdCategory));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(201);

    }

    public static function updateCategory(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        $body = $request->getParsedBody();
        $name = $body["name"];
        $active = $body["active"];
        
        if ($name == '' || $active == '') {
            return $response->withStatus(400, "Bad Request: Missing required fields");

        }
        
        $statement = $database->prepare("UPDATE category SET active = ?, name = ? WHERE category_id = ?");
        $statement->bind_param("isi", $active, $name, $args["id"]);

        $result = $statement->execute();

        $createdCategory = [
            "active"=> $active,
            "name"=> $name
        ];

    

        $response->getBody()->write(json_encode($createdCategory));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(201);


    }

    public static function deleteCategory(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        $id = $args["id"];

        $statement = $database->prepare("DELETE FROM category WHERE category_id = ?");
        $statement->bind_param("i", $id);


        $result = $statement->execute();

        $deletedCategory = [
            "category_id"=> $id
        ];

        if ($statement->affected_rows === 0) {
            $response->getBody()->write(json_encode(["error" => "Category not found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($deletedCategory));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200);

    }

    public static function index(Request $request, Response $response, $args) {

    }
}