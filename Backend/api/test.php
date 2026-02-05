<?php
require_once 'Config/cors.php';
require_once 'Config/database.php';

// Set JSON content type
header("Content-Type: application/json");

// Simple test endpoint to verify JSON responses and database connectivity
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Test database connection
    $stmt = $db->query("SELECT COUNT(*) as count FROM employees");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "message" => "Backend API is working!",
        "database_connected" => true,
        "employee_count" => $result['count'],
        "timestamp" => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed",
        "message" => $e->getMessage()
    ]);
}
?>
