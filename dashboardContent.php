<?php
require 'db.php';

$contacts = $conn->query("
  SELECT id, firstname, lastname, email, company, type
  FROM Contacts
  ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="top-bar">
  <h2>Dashboard</h2>
  <button class="btn ajax-link" data-page="addContact">+ Add Contact</button>
</div>

<div class="filters">
  <span>Filter By:</span>
  <button class="filter active">All</button>
  <button class="filter">Sales Leads</button>
  <button class="filter">Support</button>
  <button class="filter">Assigned to me</button>
</div>

<table class="contacts-table">
  <thead>
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Company</th>
      <th>Type</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($contacts as $c): ?>
    <tr>
      <td><?= htmlspecialchars($c['firstname'].' '.$c['lastname']) ?></td>
      <td><?= htmlspecialchars($c['email']) ?></td>
      <td><?= htmlspecialchars($c['company']) ?></td>
      <td><span class="badge <?= strtolower($c['type']) ?>"><?= $c['type'] ?></span></td>
      <td><a href="view_contact.php?id=<?= $c['id'] ?>" class="view">View</a></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>



