<?php
include '../includes/session.php';
include '../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$deals_result = pg_query_params($conn, "SELECT deals.*, contacts.name as contact_name 
    FROM deals 
    LEFT JOIN contacts ON deals.contact_id = contacts.id 
    WHERE deals.user_id = $1 
    ORDER BY deals.created_at DESC", array($user_id));

$contacts_result = pg_query_params($conn, "SELECT id, name FROM contacts WHERE user_id = $1 ORDER BY name ASC", array($user_id));
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
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sekuya&family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
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
            <a href="deals.php" class="active">Deals</a>
            <a href="emails.php">Emails</a>
            <a href="invoices.php">Invoices</a>
        </aside>

        <main class="main-content">
            <h2>Deals</h2>

            <section class="form-section">
                <h3>Add New Deal</h3>
                <form action="../actions/add_deal.php" method="POST">
                    <label>Title</label>
                    <input type="text" name="title" placeholder="Deal title" required>
                    <label>Value ($)</label>
                    <input type="number" name="value" placeholder="0.00" step="0.01" min="0">
                    <label>Contact</label>
                    <select name="contact_id">
                        <option value="">-- No Contact --</option>
                        <?php foreach ($contacts as $contact): ?>
                        <option value="<?= $contact['id'] ?>"><?= htmlspecialchars($contact['name']) ?></option>
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
                        <td><?= htmlspecialchars($deal['title']) ?></td>
                        <td><?= $deal['contact_name'] ? htmlspecialchars($deal['contact_name']) : '—' ?></td>
                        <td>$<?= number_format($deal['value'], 2) ?></td>
                        <td><span class="badge badge-<?= $deal['stage'] ?>"><?= ucfirst($deal['stage']) ?></span></td>
                        <td><?= date('M d, Y', strtotime($deal['created_at'])) ?></td>
                        <td>
                            <a href="../actions/edit_deal.php?id=<?= $deal['id'] ?>">Edit</a>
                            <a href="../actions/delete_deal.php?id=<?= $deal['id'] ?>" onclick="return confirm('Delete this deal?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
