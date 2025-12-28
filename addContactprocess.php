
<?php
require 'sess.php';
require 'db.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}


$required = ['title', 'firstname', 'lastname', 'email'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        exit('Missing required field: ' . $field);
    }
}


$title     = trim($_POST['title']);
$firstname = trim($_POST['firstname']);
$lastname  = trim($_POST['lastname']);
$email     = trim($_POST['email']);
$telephone = trim($_POST['telephone'] ?? '');
$company   = trim($_POST['company'] ?? '');
$type      = trim($_POST['type'] ?? '');


$sql = "
    INSERT INTO contacts
    (title, firstname, lastname, email, telephone, company, type, created_by)
    VALUES
    (:title, :firstname, :lastname, :email, :telephone, :company, :type, 
:created_by)
";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ':title'      => $title,
    ':firstname'  => $firstname,
    ':lastname'   => $lastname,
    ':email'      => $email,
    ':telephone'  => $telephone,
    ':company'    => $company,
    ':type'       => $type,
    ':created_by' => $_SESSION['user_id']
]);


header('Location: dashboard.php');
exit;

