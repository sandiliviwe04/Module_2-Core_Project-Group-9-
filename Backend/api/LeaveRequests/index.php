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
                // Get leave requests for specific employee
                $query = "SELECT lr.*, e.name 
                          FROM leave_requests lr 
                          JOIN employees e ON lr.employee_id = e.employee_id 
                          WHERE lr.employee_id = :employee_id 
                          ORDER BY lr.date DESC";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':employee_id', $_GET['employee_id']);
                $stmt->execute();

                $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "data" => $requests,
                    "count" => count($requests)
                ]);
            } else {
                // Get all leave requests
                $query = "SELECT lr.*, e.name 
                          FROM leave_requests lr 
                          JOIN employees e ON lr.employee_id = e.employee_id 
                          ORDER BY lr.date DESC";
                $stmt = $db->prepare($query);
                $stmt->execute();

                $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "data" => $requests,
                    "count" => count($requests)
                ]);
            }
            break;

        case 'POST':
            // Submit leave request
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['employee_id']) || !isset($data['date']) || !isset($data['reason'])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Employee ID, date, and reason are required"
                ]);
                break;
            }

            $query = "INSERT INTO leave_requests (employee_id, date, reason, status) 
                      VALUES (:employee_id, :date, :reason, 'Pending')";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':employee_id', $data['employee_id']);
            $stmt->bindParam(':date', $data['date']);
            $stmt->bindParam(':reason', $data['reason']);

            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode([
                    "success" => true,
                    "message" => "Leave request submitted successfully",
                    "id" => $db->lastInsertId()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "error" => "Failed to submit leave request"
                ]);
            }
            break;

        case 'PUT':
            // Update leave request status (approve/deny)
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['id']) || !isset($data['status'])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Request ID and status are required"
                ]);
                break;
            }

            // Validate status
            if (!in_array($data['status'], ['Approved', 'Denied', 'Pending'])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Status must be 'Approved', 'Denied', or 'Pending'"
                ]);
                break;
            }

            $query = "UPDATE leave_requests SET 
                      status = :status
                      WHERE id = :id";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':status', $data['status']);

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "Leave request updated successfully"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "error" => "Failed to update leave request"
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