<?php
require_once 'config.php';

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Check if current user is an admin (only admins can add users)
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';

$showForm = isset($_GET['addUser']);

// Create new user when form is submitted (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    // Get and sanitize form data
    $new_firstname = trim($_POST['firstname'] ?? '');
    $new_lastname = trim($_POST['lastname'] ?? '');
    $new_email_raw = trim($_POST['email'] ?? '');
    $new_email = filter_var($new_email_raw, FILTER_SANITIZE_EMAIL);
    $new_password_raw = $_POST['password'] ?? '';
    $new_role = $_POST['role'] ?? 'Member';

    // Basic validation
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } elseif (empty($new_password_raw)) {
        $error = "Password cannot be empty.";
    } else {
        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM Users WHERE email = ?");
        $checkStmt->execute([$new_email]);
        $emailExists = (int)$checkStmt->fetchColumn() > 0;

        if ($emailExists) {
            $error = "A user with that email already exists.";
        } else {
            // Hash password and insert safely inside try/catch
            $new_password = password_hash($new_password_raw, PASSWORD_DEFAULT);
            try {
                $insert = $conn->prepare("INSERT INTO Users (firstname, lastname, password, email, role) VALUES (?, ?, ?, ?, ?)");
                $insert->execute([$new_firstname, $new_lastname, $new_password, $new_email, $new_role]);
                $success = "User added successfully!";
                // Optionally clear form variables after success
                $new_firstname = $new_lastname = $new_email = '';
            } catch (PDOException $e) {
                $error = "An error occurred while adding the user. Please try again.";
            }
        }
    }
}

// Load all users to display in the table
$query = $conn->query("SELECT id, firstname, lastname, email, role, created_at FROM Users ORDER BY created_at DESC");
$all_users = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dolphin CRM - Users</title>

    <link rel="stylesheet" href="includes/stylesheets/users_style.css">
    <link rel="stylesheet" href="includes/stylesheets/new_user_style.css">
</head>
<body>

<header>
    <p>Dolphin CRM</p>
    <img src="includes/images/dolphin-7159274_1920.png" alt="Dolphin Logo">
</header>

<div class="container">

    <div class="main">
        <h1>
            Users
            <?php if ($is_admin): ?>
                <button id="showAddUserForm" class="newUserBtn">+ Add User</button>
            <?php endif; ?>
        </h1>

        <?php if (isset($success)): ?>
            <p class="success-message">
                <?= htmlspecialchars($success) ?>
            </p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p class="error-message">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <?php if ($is_admin): ?>
        <!-- Hidden Add User form (styled by new_user_style.css) -->
        <div id="add-user-form" class="form-container" style="display:none;">
            <h2>New User</h2>
            <form method="POST">
                <div class="form-field" id="firstName">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" required value="<?= isset($new_firstname) ? htmlspecialchars($new_firstname) : '' ?>">
                </div>

                <div class="form-field" id="lastName">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" required value="<?= isset($new_lastname) ? htmlspecialchars($new_lastname) : '' ?>">
                </div>

                <div class="form-field" id="email">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?= isset($new_email_raw) ? htmlspecialchars($new_email_raw) : '' ?>">
                </div>

                <div class="form-field" id="password">
                    <label for="password">Password</label>

                    <div class="password-wrapper">
                        <input type="password" id="newUserPassword" name="password" required>
                        <i id="toggleNewUserPassword" class="eye">👁</i>
                    </div>
                </div>

                <div class="form-field" id="role">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="Member" <?= (isset($new_role) && $new_role === 'Member') ? 'selected' : '' ?>>Member</option>
                        <option value="Admin" <?= (isset($new_role) && $new_role === 'Admin') ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="form-field" id="button">
                    <button type="submit">Save</button>
                    <button type="button" id="cancelAddUser">Cancel</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- USERS TABLE -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <?= date('Y/m/d H:i', strtotime($user['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <div class="aside">
        <nav>
            <ul>
                <li>
                    <a href="dashboard.php">
                        <img src="includes/images/home.jpg" alt="Home" class="nav-icon">
                        Home
                    </a>
                </li>
                <li>
                    <a href="new_contact.php">
                        <img src="includes/images/user.jpg" alt="New Contact" class="nav-icon">
                        New Contact
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <img src="includes/images/users.jpg" alt="Users" class="nav-icon">
                        Users
                    </a>
                </li>
            </ul>
        </nav>
        <div class="logout">
            <a href="logout.php">
                <img src="includes/images/logout.jpg" alt="Logout" class="nav-icon">
                Logout
            </a>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addForm     = document.getElementById('add-user-form');
    const showBtn     = document.getElementById('showAddUserForm');
    const cancelBtn   = document.getElementById('cancelAddUser');
    const pwdField    = document.getElementById('newUserPassword');
    const togglePwd   = document.getElementById('toggleNewUserPassword');

    if (showBtn && addForm) {
        showBtn.addEventListener('click', () => {
            addForm.style.display = 'block';
        });
    }

    if (cancelBtn && addForm) {
        cancelBtn.addEventListener('click', () => {
            addForm.style.display = 'none';
        });
    }

    if (pwdField && togglePwd) {
        togglePwd.addEventListener('click', () => {
            const type = pwdField.getAttribute('type') === 'password' ? 'text' : 'password';
            pwdField.setAttribute('type', type);
        });
    }
});
</script>

</body>
</html>