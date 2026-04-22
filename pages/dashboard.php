<?php
include '../includes/session.php';
include '../includes/auth.php';


if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Deallocate all existing prepared statements to avoid "already exists" error
pg_query($conn, "DEALLOCATE ALL");

pg_prepare($conn, "get_stats", "SELECT 
    (SELECT COUNT(*) FROM contacts WHERE user_id = $1) as total_contacts,
    (SELECT COUNT(*) FROM deals WHERE user_id = $1) as total_deals,
    (SELECT COALESCE(SUM(value), 0) FROM deals WHERE user_id = $1) as total_deal_value,
    (SELECT COUNT(*) FROM invoices WHERE user_id = $1 AND status = 'unpaid') as unpaid_count,
    (SELECT COALESCE(SUM(amount), 0) FROM invoices WHERE user_id = $1 AND status = 'unpaid') as total_unpaid
");
$stats_result = pg_execute($conn, "get_stats", array($user_id));
$stats = pg_fetch_assoc($stats_result);

pg_prepare($conn, "recent_contacts", "SELECT name, email, company, created_at FROM contacts WHERE user_id = $1 ORDER BY created_at DESC LIMIT 5");
$recent_result = pg_execute($conn, "recent_contacts", array($user_id));
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
    <title>Dashboard | SyncBase</title>
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
            <h2>Dashboard</h2>

            <div class="stat-cards">
                <div class="stat-card">
                    <p class="stat-label">Total Contacts</p>
                    <p class="stat-value"><?= $stats['total_contacts'] ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Total Deals</p>
                    <p class="stat-value"><?= $stats['total_deals'] ?></p>
                    <p class="stat-sub">$<?= number_format($stats['total_deal_value'], 2) ?> total value</p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Unpaid Invoices</p>
                    <p class="stat-value"><?= $stats['unpaid_count'] ?></p>
                    <p class="stat-sub">$<?= number_format($stats['total_unpaid'], 2) ?> owed</p>
                </div>
            </div>

            <h3>Recent Contacts</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($contact = pg_fetch_assoc($recent_result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($contact['name']) ?></td>
                        <td><?= htmlspecialchars($contact['email']) ?></td>
                        <td><?= htmlspecialchars($contact['company']) ?></td>
                        <td><?= date('M d, Y', strtotime($contact['created_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
