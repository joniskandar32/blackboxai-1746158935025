<?php
require_once 'config.php';

function handleAttendanceRequest($method, $id) {
    $db = getDbConnection();

    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $db->prepare('SELECT * FROM attendance WHERE id = ?');
                $stmt->execute([$id]);
                $attendance = $stmt->fetch();
                if ($attendance) {
                    echo json_encode($attendance);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Attendance record not found']);
                }
            } else {
                $stmt = $db->query('SELECT * FROM attendance');
                $attendanceRecords = $stmt->fetchAll();
                echo json_encode($attendanceRecords);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['employee_id']) || !isset($data['date'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Employee ID and date are required']);
                return;
            }
            // Check if record exists for employee and date
            $stmt = $db->prepare('SELECT id FROM attendance WHERE employee_id = ? AND date = ?');
            $stmt->execute([$data['employee_id'], $data['date']]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Update check_in or check_out
                $fields = [];
                $params = [];
                if (isset($data['check_in'])) {
                    $fields[] = 'check_in = ?';
                    $params[] = $data['check_in'];
                }
                if (isset($data['check_out'])) {
                    $fields[] = 'check_out = ?';
                    $params[] = $data['check_out'];
                }
                if (count($fields) === 0) {
                    http_response_code(400);
                    echo json_encode(['error' => 'No check_in or check_out provided']);
                    return;
                }
                $params[] = $existing['id'];
                $sql = 'UPDATE attendance SET ' . implode(', ', $fields) . ' WHERE id = ?';
                $stmt = $db->prepare($sql);
                $result = $stmt->execute($params);
                if ($result) {
                    echo json_encode(['message' => 'Attendance updated']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to update attendance']);
                }
            } else {
                // Insert new record
                $stmt = $db->prepare('INSERT INTO attendance (employee_id, date, check_in, check_out) VALUES (?, ?, ?, ?)');
                $result = $stmt->execute([
                    $data['employee_id'],
                    $data['date'],
                    $data['check_in'] ?? null,
                    $data['check_out'] ?? null,
                ]);
                if ($result) {
                    http_response_code(201);
                    echo json_encode(['message' => 'Attendance recorded', 'id' => $db->lastInsertId()]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to record attendance']);
                }
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}
