<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

$resource = $request[0] ?? null;
$id = $request[1] ?? null;

switch ($resource) {
    case 'employees':
        require_once 'employee.php';
        handleEmployeeRequest($method, $id);
        break;
    case 'auth':
        require_once 'auth.php';
        handleAuthRequest($method);
        break;
    case 'attendance':
        require_once 'attendance.php';
        handleAttendanceRequest($method, $id);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Resource not found']);
        break;
}
