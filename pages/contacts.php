<?php
include '../includes/session.php';
include '../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$result = pg_query_params($conn, "SELECT * FROM contacts WHERE user_id = $1 ORDER BY created_at DESC", array($user_id));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sekuya&family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Contacts | SyncBase</title>
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
            <a href="contacts.php" class="active">Contacts</a>
            <a href="deals.php">Deals</a>
            <a href="emails.php">Emails</a>
            <a href="invoices.php">Invoices</a>
        </aside>

        <main class="main-content">
            <h2>Contacts</h2>

            <section class="form-section">
                <h3>Add New Contact</h3>
                <form action="../actions/add_contact.php" method="POST">
                    <label>Name</label>
                    <input type="text" name="name" placeholder="Full name" required>
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Email address">
                    <label>Phone</label>
                    <input type="text" name="phone" placeholder="Phone number">
                    <label>Company</label>
                    <input type="text" name="company" placeholder="Company name">
                    <label>Notes</label>
                    <textarea name="notes" placeholder="Any notes..."></textarea>
                    <button type="submit" class="btn">Add Contact</button>
                </form>
            </section>

            <table class="data-table">
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
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['company']) ?></td>
                        <td><?= htmlspecialchars($row['notes']) ?></td>
                        <td>
                            <a href="../actions/edit_contact.php?id=<?= $row['id'] ?>">Edit</a>
                            <a href="../actions/delete_contact.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this contact?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
