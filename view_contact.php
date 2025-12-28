<?php
require 'sess.php';
require 'db.php';

$userId = (int)$_SESSION['user_id'];
$contactId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($contactId <= 0) {
  echo "<p>Invalid contact ID.</p>";
  exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'assign') {
    $stmt = $conn->prepare("UPDATE contacts SET assigned_to=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([$userId, $contactId]);
  }

  if ($action === 'switch') {
    $stmt = $conn->prepare("
      UPDATE contacts
      SET type = IF(type='Support','Sales Lead','Support'),
          updated_at = NOW()
      WHERE id = ?
    ");
    $stmt->execute([$contactId]);
  }

  if ($action === 'note') {
    $comment = trim($_POST['comment'] ?? '');
    if ($comment !== '') {
      $stmt = $conn->prepare("
        INSERT INTO notes (contact_id, comment, created_by, created_at)
        VALUES (?, ?, ?, NOW())
      ");
      $stmt->execute([$contactId, $comment, $userId]);

      $stmt2 = $conn->prepare("UPDATE contacts SET updated_at=NOW() WHERE id=?");
      $stmt2->execute([$contactId]);
    }
  }

  // return to the same view after action
  header("Location: viewContact.php?id=".$contactId);
  exit();
}

/* ---------- FETCH CONTACT ---------- */
$stmt = $conn->prepare("
  SELECT c.*,
         CONCAT(u1.firstname,' ',u1.lastname) AS created_by_name,
         CONCAT(u2.firstname,' ',u2.lastname) AS assigned_to_name
  FROM contacts c
  LEFT JOIN users u1 ON c.created_by = u1.id
  LEFT JOIN users u2 ON c.assigned_to = u2.id
  WHERE c.id = ?
");
$stmt->execute([$contactId]);
$contact = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contact) {
  echo "<p>Contact not found.</p>";
  exit();
}

/* ---------- FETCH NOTES ---------- */
$stmt = $conn->prepare("
  SELECT n.comment, n.created_at,
         CONCAT(u.firstname,' ',u.lastname) AS author
  FROM notes n
  JOIN users u ON n.created_by = u.id
  WHERE n.contact_id = ?
  ORDER BY n.created_at DESC
");
$stmt->execute([$contactId]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$name = trim($contact['title'].' '.$contact['firstname'].' '.$contact['lastname']);
?>

<div class="top-bar">
  <h2>Viewing Full Contact Details</h2>

  <div class="actions">
    <form method="POST">
      <input type="hidden" name="action" value="assign">
      <button class="btn">Assign to me</button>
    </form>

    <form method="POST">
      <input type="hidden" name="action" value="switch">
      <button class="btn btn-save">
        Switch to <?= ($contact['type'] === 'Support') ? 'Sales Lead' : 'Support' ?>
      </button>
    </form>
  </div>
</div>

<div class="form-box">
  <h2><?= htmlspecialchars($name) ?></h2>

  <div class="form-row">
    <div class="form-group"><label>Email</label><?= htmlspecialchars($contact['email']) ?></div>
    <div class="form-group"><label>Company</label><?= htmlspecialchars($contact['company']) ?></div>
  </div>

  <div class="form-row">
    <div class="form-group"><label>Telephone</label><?= htmlspecialchars($contact['telephone']) ?></div>
    <div class="form-group">
      <label>Type</label>
      <?= htmlspecialchars($contact['type']) ?>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label>Assigned To</label>
      <?= htmlspecialchars($contact['assigned_to_name'] ?: 'Unassigned') ?>
    </div>
    <div class="form-group">
      <label>Created By</label>
      <?= htmlspecialchars($contact['created_by_name'] ?: 'Unknown') ?>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group"><label>Date Created</label><?= htmlspecialchars($contact['created_at']) ?></div>
    <div class="form-group"><label>Last Updated</label><?= htmlspecialchars($contact['updated_at']) ?></div>
  </div>
</div>

<div class="form-box">
  <h2>Notes</h2>

  <form method="POST">
    <input type="hidden" name="action" value="note">
    <textarea name="comment" rows="3" placeholder="Add a note..." required></textarea>
    <div class="form-actions">
      <button class="btn-save">Add Note</button>
    </div>
  </form>

  <?php if (count($notes) === 0): ?>
    <p style="color:#6b7280;">No notes yet.</p>
  <?php else: ?>
    <?php foreach ($notes as $n): ?>
      <div style="margin-top:15px; padding-top:10px; border-top:1px solid #e5e7eb;">
        <strong><?= htmlspecialchars($n['author']) ?></strong>
        <div style="font-size:13px;color:#6b7280;"><?= htmlspecialchars($n['created_at']) ?></div>
        <p><?= nl2br(htmlspecialchars($n['comment'])) ?></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
