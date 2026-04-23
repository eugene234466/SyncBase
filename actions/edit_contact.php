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
    header("Location: ../pages/contacts.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $company = trim($_POST["company"]);
    $notes = trim($_POST["notes"]);

    if (empty($name)) {
        header("Location: ../pages/contacts.php?error=Name is required");
        exit();
    }

    pg_query_params($conn, "UPDATE contacts SET name=$1, email=$2, phone=$3, company=$4, notes=$5 WHERE id=$6 AND user_id=$7", array($name, $email, $phone, $company, $notes, $id, $user_id));

    header("Location: ../pages/contacts.php");
    exit();
}

$result = pg_query_params($conn, "SELECT * FROM contacts WHERE id=$1 AND user_id=$2", array($id, $user_id));
$contact = pg_fetch_assoc($result);

if (!$contact) {
    header("Location: ../pages/contacts.php");
    exit();
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
    <title>Edit Contact | SyncBase</title>
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
            <a href="../pages/contacts.php" class="active">Contacts</a>
            <a href="../pages/deals.php">Deals</a>
            <a href="../pages/emails.php">Emails</a>
            <a href="../pages/invoices.php">Invoices</a>
        </aside>

        <main class="main-content">
            <h2>Edit Contact</h2>
            <section class="form-section">
                <form action="edit_contact.php?id=<?= $id ?>" method="POST">
                    <label>Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($contact['name']) ?>" required>
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($contact['email']) ?>">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($contact['phone']) ?>">
                    <label>Company</label>
                    <input type="text" name="company" value="<?= htmlspecialchars($contact['company']) ?>">
                    <label>Notes</label>
                    <textarea name="notes"><?= htmlspecialchars($contact['notes']) ?></textarea>
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="../pages/contacts.php" class="btn-secondary">Cancel</a>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
