<?php
require_once '../Config/cors.php';
require_once '../Config/database.php';
require_once '../Middleware/auth.php';

header("Content-Type: application/json");

$database = new Database();
$db = $database->getConnection();

// Verify authentication
$user = verifyToken();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get single employee
                $query = "SELECT * FROM employees WHERE employee_id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_GET['id']);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $employee = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo json_encode([
                        "success" => true,
                        "data" => $employee
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        "success" => false,
                        "error" => "Employee not found"
                    ]);
                }
            } else {
                // Get all employees
                $query = "SELECT * FROM employees ORDER BY name ASC";
                $stmt = $db->prepare($query);
                $stmt->execute();

                $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "data" => $employees,
                    "count" => count($employees)
                ]);
            }
            break;

        case 'POST':
            // Create new employee
            $data = json_decode(file_get_contents("php://input"), true);

            // Validate required fields
            if (!isset($data['name']) || !isset($data['position']) || !isset($data['department'])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Name, position, and department are required"
                ]);
                break;
            }

            $query = "INSERT INTO employees (name, position, department, salary, employment_history, contact) 
                      VALUES (:name, :position, :department, :salary, :employment_history, :contact)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':position', $data['position']);
            $stmt->bindParam(':department', $data['department']);
            $stmt->bindParam(':salary', $data['salary']);
            $stmt->bindParam(':employment_history', $data['employment_history']);
            $stmt->bindParam(':contact', $data['contact']);

            if ($stmt->execute()) {
                $employee_id = $db->lastInsertId();
                http_response_code(201);
                echo json_encode([
                    "success" => true,
                    "message" => "Employee created successfully",
                    "employee_id" => $employee_id
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "error" => "Failed to create employee"
                ]);
            }
            break;

        case 'PUT':
            // Update employee
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['employee_id'])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Employee ID is required"
                ]);
                break;
            }

            $query = "UPDATE employees SET 
                      name = :name,
                      position = :position,
                      department = :department,
                      salary = :salary,
                      employment_history = :employment_history,
                      contact = :contact
                      WHERE employee_id = :employee_id";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':employee_id', $data['employee_id']);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':position', $data['position']);
            $stmt->bindParam(':department', $data['department']);
            $stmt->bindParam(':salary', $data['salary']);
            $stmt->bindParam(':employment_history', $data['employment_history']);
            $stmt->bindParam(':contact', $data['contact']);

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "Employee updated successfully"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "error" => "Failed to update employee"
                ]);
            }
            break;

        case 'DELETE':
            // Delete employee
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Employee ID is required"
                ]);
                break;
            }

            $query = "DELETE FROM employees WHERE employee_id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_GET['id']);

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "Employee deleted successfully"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "error" => "Failed to delete employee"
                ]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode([
                "success" => false,
                "error" => "Method not allowed"
            ]);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database error",
        "message" => $e->getMessage()
    ]);
}
?>