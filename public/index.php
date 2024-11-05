<?php
require_once __DIR__ . '/vendor/autoload.php';

use Src\Controller\UserController;
use Src\Service\UserService;
use Src\Database\Database;
use Src\Exception\DatabaseConnectionException;

try {
    $databaseConnection = new Database();
    $pdo = $databaseConnection->getConnection();
    $userService = new UserService($pdo);
    $userController = new UserController($userService);

    $action = $_GET['action'] ?? '';
    $params = $_GET;
    $data = json_decode(file_get_contents('php://input'), true);

    $response = $userController->handleRequest($action, $params, $data);
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (DatabaseConnectionException $e) {
    http_response_code(500);
    echo json_encode($e->getErrorResponse());
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "result" => ["error" => "An unexpected error occurred: " . $e->getMessage()]
    ]);
}
