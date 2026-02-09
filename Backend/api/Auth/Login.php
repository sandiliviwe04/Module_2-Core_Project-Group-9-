<?php
require_once '../Config/cors.php';
require_once '../Config/database.php';
require_once '../Config/jwt.php';

header("Content-Type: application/json");

$database = new Database();
$db = $database->getConnection();

// Get posted data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Username and password required"
    ]);
    exit();
}

try {
    // Query user from database
    $query = "SELECT user_id, username, password_hash, email, role FROM users WHERE username = :username LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $data['username']);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if (password_verify($data['password'], $user['password_hash'])) {
            // Generate JWT token
            $token_payload = [
                "user_id" => $user['user_id'],
                "username" => $user['username'],
                "role" => $user['role'],
                "exp" => time() + (60 * 60 * 24) // 24 hours
            ];

            $jwt = JWT::encode($token_payload);

            // Update last login
            $update_query = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(":user_id", $user['user_id']);
            $update_stmt->execute();

            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Login successful",
                "token" => $jwt,
                "user" => [
                    "user_id" => $user['user_id'],
                    "username" => $user['username'],
                    "email" => $user['email'],
                    "role" => $user['role']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "username" => $data['username'],
                "password" => $data['password'],
                "success" => false,
                "error" => "Invalid credentials"
            ]);
        }
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Invalid credentials"
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Login failed",
        "message" => $e->getMessage()
    ]);
}
?>