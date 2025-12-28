<?php
require 'sess.php';
require 'db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    exit('Forbidden');
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}


$required = ['firstname', 'lastname', 'email', 'password', 'role'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        exit('Missing required field: ' . $field);
    }
}


$firstname = trim($_POST['firstname']);
$lastname  = trim($_POST['lastname']);
$email     = trim($_POST['email']);
$password  = $_POST['password'];
$role      = $_POST['role'];


if (!in_array($role, ['Admin', 'Member'])) {
    exit('Invalid role');
}


$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


$check = $conn->prepare("SELECT id FROM users WHERE email = :email");
$check->execute([':email' => $email]);

if ($check->rowCount() > 0) {
    exit('Email already exists');
}


$sql = "
    INSERT INTO users (firstname, lastname, email, password, role, created_at)
    VALUES (:firstname, :lastname, :email, :password, :role, NOW())
";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':firstname' => $firstname,
    ':lastname'  => $lastname,
    ':email'     => $email,
    ':password'  => $hashedPassword,
    ':role'      => $role
]);


header('Location: dashboard.php');
exit;

