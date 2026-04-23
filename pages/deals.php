<?php
include '../includes/session.php';
include '../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch deals with contact name
pg_prepare($conn, "get_deals", "SELECT deals.*, contacts.name as contact_name 
    FROM deals 
    LEFT JOIN contacts ON deals.contact_id = contacts.id 
    WHERE deals.user_id = $1 
    ORDER BY deals.created_at DESC");
$deals_result = pg_execute($conn, "get_deals", array($user_id));

// Fetch contacts for dropdown
pg_prepare($conn, "get_contacts_dropdown", "SELECT id, name FROM contacts WHERE user_id = $1 ORDER BY name ASC");
$contacts_result = pg_execute($conn, "get_contacts_dropdown", array($user_id));
$contacts = [];
while ($row = pg_fetch_assoc($contacts_result)) {
    $contacts[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Sekuya&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sekuya&family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>Deals | SyncBase</title>
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
            <h2>Deals</h2>

            <!-- Add Deal Form -->
            <section class="form-section">
                <h3>Add New Deal</h3>
                <form action="../actions/add_deal.php" method="POST">
                    <label>Title</label>
                    <input type="text" name="title" placeholder="Deal title" required>

                    <label>Value (₵)</label>
                    <input type="number" name="value" placeholder="0.00" step="0.01" min="0">

                    <label>Contact</label>
                    <select name="contact_id">
                        <option value="">-- No Contact --</option>
                        <?php foreach ($contacts as $contact): ?>
                        <option value="<?php echo $contact['id']; ?>">
                            <?php echo htmlspecialchars($contact['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Stage</label>
                    <select name="stage">
                        <option value="lead">Lead</option>
                        <option value="negotiation">Negotiation</option>
                        <option value="closed">Closed</option>
                    </select>

                    <button type="submit" class="btn">Add Deal</button>
                </form>
            </section>

            <!-- Deals Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Contact</th>
                        <th>Value</th>
                        <th>Stage</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($deal = pg_fetch_assoc($deals_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($deal['title']); ?></td>
                        <td><?php echo $deal['contact_name'] ? htmlspecialchars($deal['contact_name']) : '—'; ?></td>
                        <td>₵<?php echo number_format($deal['value'], 2); ?></td>
                        <td><?php echo ucfirst($deal['stage']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($deal['created_at'])); ?></td>
                        <td>
                            <a href="../actions/edit_deal.php?id=<?php echo $deal['id']; ?>">Edit</a>
                            <a href="../actions/delete_deal.php?id=<?php echo $deal['id']; ?>" onclick="return confirm('Delete this deal?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
