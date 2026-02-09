<?php
require_once '../Config/cors.php';
require_once '../Config/database.php';

header("Content-Type: application/json");

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['username']) ||
    !isset($data['password']) ||
    !isset($data['email']) ||
    !isset($data['role'])
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "All fields required (username, password, email, role)"
    ]);
    exit();
}

$username = trim($data['username']);
$password = $data['password'];
$email = trim($data['email']);
$role = trim($data['role']);

try {

    // Check if username already exists
    $checkQuery = "SELECT user_id FROM users WHERE username = :username LIMIT 1";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(":username", $username);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "error" => "Username already exists"
        ]);
        exit();
    }

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $query = "INSERT INTO users (username, password_hash, email, role)
              VALUES (:username, :password_hash, :email, :role)";

    $stmt = $db->prepare($query);

    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password_hash", $passwordHash);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":role", $role);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "User created successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to create user"
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Server error",
        "message" => $e->getMessage()
    ]);
}
?>
