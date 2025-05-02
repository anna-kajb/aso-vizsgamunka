<?php

	require 'config.php';
	require 'functions.php';

	$message = "";

	if (isset($_GET['email']) && isset($_GET['code'])) {
		$email = $_GET['email'];
		$code = $_GET['code'];

		// Ellenőrizzük, hogy az email és a kód létezik-e
		$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ?");
		$stmt->bind_param("ss", $email, $code);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows !== 1) {
			$message = "❌ Érvénytelen vagy lejárt kód!";
		}
	} else {
		$message = "❌ Hiányzó adatok!";
	}

	// POST beküldés
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
		$new_password = $_POST['new_password'];
		$confirm = $_POST['confirm_password'];
		$email = $_POST['email'];
		$input_code = $_POST['verification_code'];

		// Lekérjük az aktuális kódot az adatbázisból
		$stmt = $conn->prepare("SELECT verification_code, username FROM users WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->bind_result($stored_code, $username);
		$stmt->fetch();
		$stmt->close();

		if ($stored_code !== $input_code) {
			$message = "❌ Hibás hitelesítő kód!";
		} elseif ($new_password !== $confirm) {
			$message = "❌ A két jelszó nem egyezik!";
		} elseif (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
			$message = "❌ A jelszónak legalább 8 karaktert, egy nagybetűt és egy számot kell tartalmaznia!";
		} else {
			$hashed = password_hash($new_password, PASSWORD_DEFAULT);
			$update = $conn->prepare("UPDATE users SET password = ?, verification_code = NULL WHERE email = ?");
			$update->bind_param("ss", $hashed, $email);
			$update->execute();

			$message = "✅ A jelszó sikeresen frissítve lett!";
			sendPasswordChangeConfirmation($email, $username);
		}
	}
?>


<!DOCTYPE html>
<html lang="hu">
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="Kajb Anna, Varasdi Vanda">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="css/styles.css">
			
		<!-- Bootstrap CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="css/styles.css">
		<title>Jelszó visszaállítás</title>
	</head>
	<body class="reg_body">
		<main class="reg_main">
			<div class="reg_box">
				<h2 class="regh1">🔐 Jelszó visszaállítása</h2>

				<?php if ($message): ?>
					<p class="password-message"><?php echo $message; ?></p>
				<?php endif; ?>

				<?php if (isset($_GET['email']) && isset($_GET['code']) && empty($message)): ?>
					<form method="post">
						<input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
							
						<label for="new_password">Új jelszó:</label>
						<input type="password" name="new_password" required>

						<label for="confirm_password">Új jelszó újra:</label>
						<input type="password" name="confirm_password" required>

						<label for="verification_code">Hitelesítő kód (amit emailben kaptál):</label>
						<input type="text" name="verification_code" required>

						<button type="submit">Jelszó frissítése</button>
					</form>

				<?php endif; ?>

				<p class="reg-span"><a href="reg.php">⬅️ Vissza a bejelentkezéshez</a></p>
			</div>
		</main>
	</body>
</html>
