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

// Handle POST - save changes
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

    pg_prepare($conn, "update_contact", "UPDATE contacts SET name=$1, email=$2, phone=$3, company=$4, notes=$5 WHERE id=$6 AND user_id=$7");
    pg_execute($conn, "update_contact", array($name, $email, $phone, $company, $notes, $id, $user_id));

    header("Location: ../pages/contacts.php");
    exit();
}

// Handle GET - fetch contact and show form
pg_prepare($conn, "get_contact", "SELECT * FROM contacts WHERE id=$1 AND user_id=$2");
$result = pg_execute($conn, "get_contact", array($id, $user_id));
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Sekuya&display=swap" rel="stylesheet">
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
            <a href="dashboard.php">Dashboard</a>
            <a href="contacts.php">Contacts</a>
            <a href="deals.php">Deals</a>
            <a href="emails.php">Emails</a>
            <a href="invoices.php">Invoices</a>
        </aside>

        <main class="main-content">
            <h2>Edit Contact</h2>
            <form action="edit_contact.php?id=<?php echo $id; ?>" method="POST">
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($contact['name']); ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($contact['email']); ?>">

                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($contact['phone']); ?>">

                <label>Company</label>
                <input type="text" name="company" value="<?php echo htmlspecialchars($contact['company']); ?>">

                <label>Notes</label>
                <textarea name="notes"><?php echo htmlspecialchars($contact['notes']); ?></textarea>

                <button type="submit" class="btn">Save Changes</button>
                <a href="../pages/contacts.php" class="btn-secondary">Cancel</a>
            </form>
        </main>
    </div>
</body>
</html>