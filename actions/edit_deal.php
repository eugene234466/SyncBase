<?php
include("../includes/session.php");
include("../includes/auth.php");

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header("Location: ../pages/deals.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $value = !empty($_POST["value"]) ? (float)$_POST["value"] : 0;
    $contact_id = !empty($_POST["contact_id"]) ? (int)$_POST["contact_id"] : null;
    $stage = trim($_POST["stage"]);

    if (empty($title)) {
        header("Location: ../pages/deals.php?error=Title is required");
        exit();
    }

    $valid_stages = ["lead", "negotiation", "closed"];
    if (!in_array($stage, $valid_stages)) $stage = "lead";

    pg_query_params($conn, "UPDATE deals SET title=$1, value=$2, contact_id=$3, stage=$4 WHERE id=$5 AND user_id=$6", array($title, $value, $contact_id, $stage, $id, $user_id));

    header("Location: ../pages/deals.php");
    exit();
}

$result = pg_query_params($conn, "SELECT * FROM deals WHERE id=$1 AND user_id=$2", array($id, $user_id));
$deal = pg_fetch_assoc($result);

if (!$deal) {
    header("Location: ../pages/deals.php");
    exit();
}

$contacts_result = pg_query_params($conn, "SELECT id, name FROM contacts WHERE user_id=$1 ORDER BY name ASC", array($user_id));
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
    <title>Edit Deal | SyncBase</title>
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
            <a href="../pages/dashboard.php">Dashboard</a>
            <a href="../pages/contacts.php">Contacts</a>
            <a href="../pages/deals.php" class="active">Deals</a>
            <a href="../pages/emails.php">Emails</a>
            <a href="../pages/invoices.php">Invoices</a>
        </aside>

        <main class="main-content">
            <h2>Edit Deal</h2>
            <section class="form-section">
                <form action="edit_deal.php?id=<?= $id ?>" method="POST">
                    <label>Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($deal['title']) ?>" required>
                    <label>Value ($)</label>
                    <input type="number" name="value" value="<?= $deal['value'] ?>" step="0.01" min="0">
                    <label>Contact</label>
                    <select name="contact_id">
                        <option value="">-- No Contact --</option>
                        <?php foreach ($contacts as $contact): ?>
                        <option value="<?= $contact['id'] ?>" <?= $contact['id'] == $deal['contact_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($contact['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Stage</label>
                    <select name="stage">
                        <option value="lead" <?= $deal['stage'] == 'lead' ? 'selected' : '' ?>>Lead</option>
                        <option value="negotiation" <?= $deal['stage'] == 'negotiation' ? 'selected' : '' ?>>Negotiation</option>
                        <option value="closed" <?= $deal['stage'] == 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="../pages/deals.php" class="btn-secondary">Cancel</a>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
