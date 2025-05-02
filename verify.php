<?php
require "config.php";
require_once "functions.php";
session_start();

$success_message = "";
$error_message = "";

// Ha a felhasználó elküldi a hitelesítési kódot
if (isset($_POST['verify-btn'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $code = strtoupper(trim($_POST['verification_code'])); // Nagybetűsítve ellenőrizzük

    // Ellenőrizzük, hogy a kód és az e-mail egyezik-e az adatbázisban
    $query = "SELECT id FROM users WHERE email=? AND verification_code=? AND verified=0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Frissítjük a verified státuszt
        $updateQuery = "UPDATE users SET verified=1 WHERE id=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $success_message = "✅ Sikeres hitelesítés! Átirányítás...";
            $stmt->close();

            // Átirányítás a főoldalra 3 mp után
            header("refresh:3;url=users.php");
        } else {
            $error_message = "❌ Hiba történt a hitelesítés során.";
        }
    } else {
        $error_message = "⚠️ Hibás kód vagy e-mail cím!";
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiók Hitelesítése</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body class="verify-body">
    <div class="verify-container">
        <h2 class="verify-title">Fiók Hitelesítése</h2>
        <p class="verify-text">Add meg az e-mail címed és a hitelesítési kódot.</p>
        
        <?php if (!empty($success_message)): ?>
            <div class="verify-alert success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="verify-alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="verify.php" class="verify-form">
            <label class="verify-label">E-mail cím:</label>
            <input type="email" name="email" class="verify-input" required>

            <label class="verify-label">Hitelesítési kód:</label>
            <input type="text" name="verification_code" class="verify-input" required>

            <button type="submit" name="verify-btn" class="verify-button">✔ Hitelesítés</button>
        </form>
    </div>
</body>
</html>
