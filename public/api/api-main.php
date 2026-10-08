<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use OpenApi\Attributes as OAT; 

#[OAT\Info(
    title: 'üK295 LB1 API',
    version: '1.0.0'
)]

class ApiMain {

    /**
     *  getProducts-function
     */

    #[OAT\Get(
        path: '/product',
        summary: 'Alle Produkte auflisten',
        tags: ['Product'],
        responses: [
            new OAT\Response(response: 200, description: 'Liste aller Produkte erfolgreich geliefert')
        ]
    )]
    public static function getProducts(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");


        // Prepare and execute the SQL query to fetch all products
        $statement = $database->prepare("SELECT product_id, sku, active, id_category, name, image, description, price, stock FROM product");
        $result = $statement->execute();
        $result = $statement->get_result();

        $products = $result->fetch_all(MYSQLI_ASSOC);

        // Return the products as a JSON response
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200);   

    }

    /**
     *  getProductById-function
     */

    #[OAT\Get(
        path: '/product/{id}',
        summary: 'Produkt nach ID abrufen',
        tags: ['Product'],
        responses: [
            new OAT\Response(response: 200, description: 'Produkt erfolgreich geliefert'),
            new OAT\Response(response: 404, description: 'Produkt nicht gefunden')
            
        ]
    )]

    public static function getProductById(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");
        // Get the product ID from the route parameters
        $id = $args["id"];
        // Prepare and execute the SQL query to fetch the product by ID
        $statement = $database->prepare("SELECT product_id, sku, active, id_category, name, image, description, price, stock FROM product WHERE product_id = ?");
        $statement->bind_param("i", $id);
        $result = $statement->execute();
        $result = $statement->get_result();

        $product = $result->fetch_assoc();
        // Check if the product exists
        if (!$product) {
            $response->getBody()->write(json_encode(["error" => "Product not found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Return the product as a JSON response
        $response->getBody()->write(json_encode($product));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200); 

    }

    /**
     *  deleteProduct-function
     */

    #[OAT\Delete(
        path: '/product/{id}',
        summary: 'Produkt löschen',
        tags: ['Product'],
        responses: [
            new OAT\Response(response: 200, description: 'Produkt erfolgreich gelöscht'),
            new OAT\Response(response: 404, description: 'Produkt nicht gefunden')
        ]
    )]
    public static function deleteProduct(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");
        // Get the product ID from the route parameters
        $id = $args["id"];

        // Prepare and execute the SQL query to delete the product by ID
        $statement = $database->prepare("DELETE FROM product WHERE product_id = ?");
        $statement->bind_param("i", $id);


        $result = $statement->execute();
        // Prepare the response data
        $deletedProduct = [
            "product_id"=> $id
        ];

        // Check if the product was found and deleted
        if ($statement->affected_rows === 0) {
            $response->getBody()->write(json_encode(["error" => "Product not found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Return the deleted product information as a JSON response
        $response->getBody()->write(json_encode($deletedProduct));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200);
    }


    /**
     *  createProduct-function
     */
    #[OAT\Post(
        path: '/product',
        summary: 'Neues Produkt erstellen',
        tags: ['Product'],
        responses: [
            new OAT\Response(response: 201, description: 'Produkt erfolgreich erstellt'),
            new OAT\Response(response: 400, description: 'Ungültige Anfrage')
        ]
    )]
    public static function createProduct(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        // Get the request body data
        $body = $request->getParsedBody();
        $sku = $body["sku"];
        $active = $body["active"];
        $id_category = $body["id_category"];
        $name = $body["name"];
        $image = $body["image"];
        $description = $body["description"];
        $price = $body["price"];
        $stock = $body["stock"];

        // Validate required fields
        if ($sku == '' || $name == '' || $active == '' || $price == '' || $stock == '') {
            return $response->withStatus(400, "Bad Request: Missing required fields");

        }
        
        // Prepare and execute the SQL query to insert a new product
        $statement = $database->prepare("INSERT INTO product (sku, active, id_category, name, image, description, price, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $statement->bind_param("iiisssii", $sku,$active, $id_category, $name, $image, $description, $price, $stock);

        $result = $statement->execute();

        // Prepare the response data for the created product.
        $createdProduct = [
            "sku"=> $sku,
            "active"=> $active,
            "id_category"=> $id_category,
            "name"=> $name,
            "image"=> $image,
            "description"=> $description,
            "price"=> $price,
            "stock"=> $stock

        ];

    
        // Return the created product information as a JSON response.
        $response->getBody()->write(json_encode($createdProduct));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(201);



        
    }


    /**
     *  getCategories-function
     */

    #[OAT\Get(
        path: '/category',
        summary: 'Alle Kategorien auflisten',
        tags: ['Category'],
        responses: [
            new OAT\Response(response: 200, description: 'Liste aller Kategorien erfolgreich geliefert')
        ]
    )]
    public static function getCategories(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        // Prepare and execute the SQL query to fetch all categories
        $statement = $database->prepare("SELECT category_id, active, name FROM category");
        $result = $statement->execute();
        $result = $statement->get_result();

        $categories = $result->fetch_all(MYSQLI_ASSOC);

        // Return the categories as a JSON response
        $response->getBody()->write(json_encode($categories));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200);   

    }

    /**
     *  getCategoryById-function
     */
    #[OAT\Get(
        path: '/category/{id}',
        summary: 'Kategorie nach ID abrufen',
        tags: ['Category'],
        responses: [
            new OAT\Response(response: 200, description: 'Kategorie erfolgreich geliefert'),
            new OAT\Response(response: 404, description: 'Kategorie nicht gefunden')
        ]
    )]
    public static function getCategoryById(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        // Get the category ID from the route parameters
        $id = $args["id"];
        // Prepare and execute the SQL query to fetch the category by ID
        $statement = $database->prepare("SELECT category_id, active, name FROM category WHERE category_id = ?");
        $statement->bind_param("i", $id);
        $result = $statement->execute();
        $result = $statement->get_result();

        $category = $result->fetch_assoc();

        // Check if the category exists
        if (!$category) {
            $response->getBody()->write(json_encode(["error" => "Category not found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Return the category as a JSON response
        $response->getBody()->write(json_encode($category));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200); 

    }

    /**
     *  createCategory-function
     */

    #[OAT\Post(
        path: '/category',
        summary: 'Neue Kategorie erstellen',
        tags: ['Category'],
        responses: [
            new OAT\Response(response: 201, description: 'Kategorie erfolgreich erstellt'),
            new OAT\Response(response: 400, description: 'Ungültige Anfrage')
        ]
    )]
    public static function createCategory(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");
        // Get the request body data
        $body = $request->getParsedBody();
        $name = $body["name"];
        $active = $body["active"];
        
        // Validate required fields
        if ($name == '' || $active == '') {
            return $response->withStatus(400, "Bad Request: Missing required fields");

        }
        
        // Prepare and execute the SQL query to insert a new category
        $statement = $database->prepare("INSERT INTO category (active, name) VALUES (?, ?)");
        $statement->bind_param("is", $active, $name);

        $result = $statement->execute();

        // Prepare the response data for the created category
        $createdCategory = [
            "active"=> $active,
            "name"=> $name
        ];

    
        // Return the created category information as a JSON response
        $response->getBody()->write(json_encode($createdCategory));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(201);

    }

    /**
     *  updateCategory-function
     */
    #[OAT\Put(
        path: '/category/{id}',
        summary: 'Kategorie aktualisieren',
        tags: ['Category'],
        responses: [
            new OAT\Response(response: 200, description: 'Kategorie erfolgreich aktualisiert'),
            new OAT\Response(response: 404, description: 'Kategorie nicht gefunden')
        ]
    )]
    public static function updateCategory(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");

        // Get the request body data
        $body = $request->getParsedBody();
        $name = $body["name"];
        $active = $body["active"];

        // Validate required fields
        
        if ($name == '' || $active == '') {
            return $response->withStatus(400, "Bad Request: Missing required fields");

        }
        
        // Prepare and execute the SQL query to update the category by ID
        $statement = $database->prepare("UPDATE category SET active = ?, name = ? WHERE category_id = ?");
        $statement->bind_param("isi", $active, $name, $args["id"]);

        $result = $statement->execute();

        // Prepare the response data for the updated category
        $createdCategory = [
            "active"=> $active,
            "name"=> $name
        ];

    
        // Return the updated category information as a JSON response
        $response->getBody()->write(json_encode($createdCategory));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(201);


    }

    /**
     *  deleteCategory-function
     */

    #[OAT\Delete(
        path: '/category/{id}',
        summary: 'Kategorie löschen',
        tags: ['Category'],
        responses: [
            new OAT\Response(response: 200, description: 'Kategorie erfolgreich gelöscht'),
            new OAT\Response(response: 404, description: 'Kategorie nicht gefunden')
        ]
    )]
    public static function deleteCategory(Request $request, Response $response, $args) {
        $database = new mysqli("localhost:3307", "root", "", "uek295_lb1");
        // Get the category ID from the route parameters
        $id = $args["id"];
        // Prepare and execute the SQL query to delete the category by ID
        $statement = $database->prepare("DELETE FROM category WHERE category_id = ?");
        $statement->bind_param("i", $id);


        $result = $statement->execute();
        // Prepare the response data for the deleted category
        $deletedCategory = [
            "category_id"=> $id
        ];

        // Check if the category was found and deleted
        if ($statement->affected_rows === 0) {
            $response->getBody()->write(json_encode(["error" => "Category not found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Return the deleted category information as a JSON response
        $response->getBody()->write(json_encode($deletedCategory));
        return $response->withHeader('Content-Type', 'application/json') ->withStatus(200);

    }

    public static function index(Request $request, Response $response, $args) {

    }
}