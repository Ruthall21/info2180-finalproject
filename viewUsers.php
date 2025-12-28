<?php
require 'db.php';

$users = $conn->query("
  SELECT firstname, lastname, email, role, created_at
  FROM Users
  ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="top-bar">
  <h2>Users</h2>
  <button class="btn ajax-link" data-page="newUser">+ Add User</button>

</div>

<table class="contacts-table">
  <thead>
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Created</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
    <tr>
      <td><?= htmlspecialchars($u['firstname'].' '.$u['lastname']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= htmlspecialchars($u['role']) ?></td>
      <td><?= htmlspecialchars($u['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>



