<?php
require_once 'config.php';

function handleEmployeeRequest($method, $id) {
    $db = getDbConnection();

    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $db->prepare('SELECT * FROM employees WHERE id = ?');
                $stmt->execute([$id]);
                $employee = $stmt->fetch();
                if ($employee) {
                    echo json_encode($employee);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Employee not found']);
                }
            } else {
                $stmt = $db->query('SELECT * FROM employees');
                $employees = $stmt->fetchAll();
                echo json_encode($employees);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input']);
                return;
            }
            $stmt = $db->prepare('INSERT INTO employees (first_name, last_name, email, phone, position, department, hire_date) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $result = $stmt->execute([
                $data['first_name'] ?? null,
                $data['last_name'] ?? null,
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['position'] ?? null,
                $data['department'] ?? null,
                $data['hire_date'] ?? null,
            ]);
            if ($result) {
                http_response_code(201);
                echo json_encode(['message' => 'Employee created', 'id' => $db->lastInsertId()]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create employee']);
            }
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Employee ID required']);
                return;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input']);
                return;
            }
            $stmt = $db->prepare('UPDATE employees SET first_name = ?, last_name = ?, email = ?, phone = ?, position = ?, department = ?, hire_date = ? WHERE id = ?');
            $result = $stmt->execute([
                $data['first_name'] ?? null,
                $data['last_name'] ?? null,
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['position'] ?? null,
                $data['department'] ?? null,
                $data['hire_date'] ?? null,
                $id
            ]);
            if ($result) {
                echo json_encode(['message' => 'Employee updated']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update employee']);
            }
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Employee ID required']);
                return;
            }
            $stmt = $db->prepare('DELETE FROM employees WHERE id = ?');
            $result = $stmt->execute([$id]);
            if ($result) {
                echo json_encode(['message' => 'Employee deleted']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete employee']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}
