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
            if (isset($_GET['employee_id'])) {
                // Get payroll for specific employee
                $query = "SELECT p.*, e.name, e.salary 
                          FROM payroll p 
                          JOIN employees e ON p.employee_id = e.employee_id 
                          WHERE p.employee_id = :employee_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':employee_id', $_GET['employee_id']);
                $stmt->execute();

                $payroll = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "data" => $payroll,
                    "count" => count($payroll)
                ]);
            } else {
                // Get all payroll records
                $query = "SELECT p.*, e.name, e.salary 
                          FROM payroll p 
                          JOIN employees e ON p.employee_id = e.employee_id 
                          ORDER BY p.id DESC";
                $stmt = $db->prepare($query);
                $stmt->execute();

                $payroll = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "data" => $payroll,
                    "count" => count($payroll)
                ]);
            }
            break;

        case 'POST':
            // Create payroll record
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['employee_id']) || !isset($data['hours_worked'])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Employee ID and hours worked are required"
                ]);
                break;
            }

            $query = "INSERT INTO payroll (employee_id, hours_worked, leave_deductions, final_salary) 
                      VALUES (:employee_id, :hours_worked, :leave_deductions, :final_salary)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':employee_id', $data['employee_id']);
            $stmt->bindParam(':hours_worked', $data['hours_worked']);
            $stmt->bindParam(':leave_deductions', $data['leave_deductions']);
            $stmt->bindParam(':final_salary', $data['final_salary']);

            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode([
                    "success" => true,
                    "message" => "Payroll record created successfully",
                    "id" => $db->lastInsertId()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "error" => "Failed to create payroll record"
                ]);
            }
            break;

        case 'PUT':
            // Update payroll record
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['id'])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Payroll ID is required"
                ]);
                break;
            }

            $query = "UPDATE payroll SET 
                      hours_worked = :hours_worked,
                      leave_deductions = :leave_deductions,
                      final_salary = :final_salary
                      WHERE id = :id";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':hours_worked', $data['hours_worked']);
            $stmt->bindParam(':leave_deductions', $data['leave_deductions']);
            $stmt->bindParam(':final_salary', $data['final_salary']);

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "Payroll record updated successfully"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "error" => "Failed to update payroll record"
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