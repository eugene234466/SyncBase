<?php
include '../includes/session.php';
include '../includes/auth.php';

// Authentication Check
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch Contacts
$query = "SELECT * FROM contacts WHERE user_id = $1 ORDER BY created_at DESC";
$result = pg_query_params($conn, $query, array($user_id));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900;1,100..900&family=Sekuya&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Manage Contacts</title>
</head>
<body>
    <nav class="navbar">
        <span class="brand">SyncBase</span>
        <div class="nav-links">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>
    <div class="layout">
    <aside class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="contacts.php">Contacts</a>
        <a href="deals.php">Deals</a>
        <a href="emails.php">Emails</a>
        <a href="invoices.php">Invoices</a>
    </aside>
    <main class="main-content">
        <h1>Contacts</h1>
        
        <!-- Add Contact Form -->
        <section id="add-contact">
            <h3>Add New Contact</h3>
            <form action="../actions/add_contact.php" method="POST">
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email">
                <input type="text" name="phone" placeholder="Phone">
                <input type="text" name="company" placeholder="Company">
                <textarea name="notes" placeholder="Notes"></textarea>
                <button type="submit" name="add_contact">Add Contact</button>
            </form>
        </section>

        <!-- Contacts Table -->
        <table border="1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Company</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = pg_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['company']); ?></td>
                    <td><?php echo htmlspecialchars($row['notes']); ?></td>
                    <td>
                        <a href="../actions/edit_contact.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="../actions/delete_contact.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this contact?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
