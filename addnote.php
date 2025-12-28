<?php
require '../includes/db.php';
session_start();

$stmt = $conn->prepare("
  INSERT INTO Notes (contact_id, comment, created_by)
  VALUES (?, ?, ?)
");
$stmt->execute([
  $_POST['contact_id'],
  $_POST['comment'],
  $_SESSION['user_id']
]);

