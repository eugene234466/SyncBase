<?php
include '../includes/session.php';
include '../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$invoices_result = pg_query_params($conn, "SELECT invoices.*, contacts.name as contact_name 
    FROM invoices 
    LEFT JOIN contacts ON invoices.contact_id = contacts.id 
    WHERE invoices.user_id = $1 
    ORDER BY invoices.created_at DESC", array($user_id));

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
    <title>Invoices | SyncBase</title>
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
            <a href="invoices.php" class="active">Invoices</a>
        </aside>

        <main class="main-content">
            <h2>Invoices</h2>

            <section class="form-section">
                <h3>Create New Invoice</h3>
                <form action="../actions/add_invoice.php" method="POST">
                    <label>Title</label>
                    <input type="text" name="title" placeholder="Invoice title" required>
                    <label>Amount ($)</label>
                    <input type="number" name="amount" placeholder="0.00" step="0.01" min="0" required>
                    <label>Contact</label>
                    <select name="contact_id">
                        <option value="">-- No Contact --</option>
                        <?php foreach ($contacts as $contact): ?>
                        <option value="<?= $contact['id'] ?>"><?= htmlspecialchars($contact['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Due Date</label>
                    <input type="date" name="due_date">
                    <label>Status</label>
                    <select name="status">
                        <option value="unpaid">Unpaid</option>
                        <option value="paid">Paid</option>
                    </select>
                    <button type="submit" class="btn">Create Invoice</button>
                </form>
            </section>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Contact</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($invoice = pg_fetch_assoc($invoices_result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($invoice['title']) ?></td>
                        <td><?= $invoice['contact_name'] ? htmlspecialchars($invoice['contact_name']) : '—' ?></td>
                        <td>$<?= number_format($invoice['amount'], 2) ?></td>
                        <td><span class="badge badge-<?= $invoice['status'] ?>"><?= ucfirst($invoice['status']) ?></span></td>
                        <td><?= $invoice['due_date'] ? date('M d, Y', strtotime($invoice['due_date'])) : '—' ?></td>
                        <td>
                            <a href="../actions/update_invoice.php?id=<?= $invoice['id'] ?>&status=<?= $invoice['status'] === 'paid' ? 'unpaid' : 'paid' ?>">
                                <?= $invoice['status'] === 'paid' ? 'Mark Unpaid' : 'Mark Paid' ?>
                            </a>
                            <a href="../actions/delete_invoice.php?id=<?= $invoice['id'] ?>" onclick="return confirm('Delete this invoice?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
