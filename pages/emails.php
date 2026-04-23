<?php
include '../includes/session.php';
include '../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch emails with contact name
pg_prepare($conn, "get_emails", "SELECT emails.*, contacts.name as contact_name 
    FROM emails 
    LEFT JOIN contacts ON emails.contact_id = contacts.id 
    WHERE emails.user_id = $1 
    ORDER BY emails.sent_at DESC");
$emails_result = pg_execute($conn, "get_emails", array($user_id));

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
    <title>Emails | SyncBase</title>
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
            <h2>Email Log</h2>

            <!-- Log Email Form -->
            <section class="form-section">
                <h3>Log New Email</h3>
                <form action="../actions/add_email.php" method="POST">
                    <label>Contact</label>
                    <select name="contact_id" required>
                        <option value="">-- Select Contact --</option>
                        <?php foreach ($contacts as $contact): ?>
                        <option value="<?php echo $contact['id']; ?>">
                            <?php echo htmlspecialchars($contact['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="Email subject">

                    <label>Body</label>
                    <textarea name="body" rows="5" placeholder="Email content..."></textarea>

                    <button type="submit" class="btn">Log Email</button>
                </form>
            </section>

            <!-- Emails Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Contact</th>
                        <th>Subject</th>
                        <th>Preview</th>
                        <th>Sent At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($email = pg_fetch_assoc($emails_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($email['contact_name']); ?></td>
                        <td><?php echo htmlspecialchars($email['subject']); ?></td>
                        <td><?php echo htmlspecialchars(substr($email['body'], 0, 60)) . '...'; ?></td>
                        <td><?php echo date('M d, Y', strtotime($email['sent_at'])); ?></td>
                        <td>
                            <a href="../actions/delete_email.php?id=<?php echo $email['id']; ?>" onclick="return confirm('Delete this email log?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
