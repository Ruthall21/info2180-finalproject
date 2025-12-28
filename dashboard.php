?php
require_once 'config.php';

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Handle requests from JavaScript to load contacts
if (isset($_GET['action']) && $_GET['action'] === 'get_contacts') {
    header('Content-Type: application/json');

    $filter_type = $_GET['filter'] ?? 'all';
    $current_user_id = $_SESSION['user_id'];

    $sql = "SELECT c.*, CONCAT(u.firstname, ' ', u.lastname) AS assigned_name
            FROM contacts c
            LEFT JOIN users u ON c.assigned_to = u.id";

    $params = [];

    if ($filter_type === 'Sales Lead' || $filter_type === 'Support') {
        $sql .= " WHERE c.type = ?";
        $params[] = $filter_type;
    } elseif ($filter_type === 'assigned') {
        $sql .= " WHERE c.assigned_to = ?";
        $params[] = $current_user_id;
    }

    $sql .= " ORDER BY c.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($contacts);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="includes/stylesheets/dashboard_style.css">
    <script src="includes/javascript/jscript.js"></script>
</head>
<body>
    <header>
        <p>Dolphin CRM</p>
        <img src="includes/images/dolphin-7159274_1920.png" alt="Dolphin Logo" />
    </header>
    <div class="container">
        <div class="main">
            <h1>
                Dashboard
                <a href="new_contact.php" id="addContactBtn">+ Add Contact</a>
            </h1>
            <div class="table-container">
                <p>
                    <img src="includes/images/filter-4881943_1920.png" alt="Filter Icon" class="filter-icon">
                    Filter by:
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="Sales Lead">Sales Lead</button>
                    <button class="filter-btn" data-filter="Support">Support</button>
                    <button class="filter-btn" data-filter="assigned">Assigned to me</button>
                </p>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="contacts-body">
                        <!-- Contacts will be loaded here by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="aside">
            <nav>
                <ul>
                    <li><a href="dashboard.php"><img src="includes/images/home.jpg" alt="Home" class="nav-icon">Home</a></li>
                    <li><a href="new_contact.php"><img src="includes/images/user.jpg" alt="New Contact" class="nav-icon">New Contact</a></li>
                    <li><a href="users.php"><img src="includes/images/users.jpg" alt="Users" class="nav-icon">Users</a></li>
                </ul>
            </nav>
            <div class="logout">
                <a href="logout.php"><img src="includes/images/logout.jpg" alt="Logout" class="nav-icon">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
