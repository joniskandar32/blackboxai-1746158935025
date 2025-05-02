<?php
require_once 'config.php';

function handleAuthRequest($method) {
    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['username']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password required']);
        return;
    }

    $db = getDbConnection();
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($data['password'], $user['password_hash'])) {
        // For simplicity, return user info without token
        echo json_encode(['message' => 'Login successful', 'user' => ['id' => $user['id'], 'username' => $user['username'], 'employee_id' => $user['employee_id']]]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid username or password']);
    }
}
