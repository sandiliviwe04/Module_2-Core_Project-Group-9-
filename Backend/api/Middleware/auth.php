<?php
require_once '../Config/cors.php';
require_once '../Config/database.php';
require_once '../Config/jwt.php';

header("Content-Type: application/json");

// Verify JWT token from Authorization header
function verifyToken()
{
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "No authorization token provided"
        ]);
        exit();
    }

    $auth_header = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $auth_header);

    $decoded = JWT::decode($token);

    if (!$decoded) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Invalid or expired token"
        ]);
        exit();
    }

    // Check if token is expired
    if (isset($decoded['exp']) && $decoded['exp'] < time()) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Token has expired"
        ]);
        exit();
    }

    return $decoded;
}
?>