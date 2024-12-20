<?php
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$theme = $input['theme'] ?? 'light';

$_SESSION['theme'] = $theme;

echo json_encode(['success' => true]);