<?php
	session_start();
	require 'config.php';
	require 'functions.php';

	if (!isset($_SESSION['user_id'])) {
		echo "Hiba: Nem vagy bejelentkezve!";
		header("Location: reg.php");
		exit();
	}

	$user_id = $_SESSION['user_id'];
	$message = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$current_password = $_POST['current_password'];
		$new_password = $_POST['new_password'];
		$confirm_password = $_POST['confirm_password'];

		// Jelenlegi jelszó ellenőrzése
		$sql = "SELECT password FROM users WHERE id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$stmt->bind_result($hashed_password);
		$stmt->fetch();
		$stmt->close();

		if (!password_verify($current_password, $hashed_password)) {
			$message = "❌ Hibás jelenlegi jelszó!";
		} elseif ($new_password !== $confirm_password) {
			$message = "❌ A két új jelszó nem egyezik!";
		} elseif (strlen($new_password) < 8) {
			$message = "❌ Az új jelszónak legalább 8 karakter hosszúnak kell lennie!";
		} elseif (!preg_match('/[A-Z]/', $new_password)) {
			$message = "❌ Az új jelszónak tartalmaznia kell legalább egy nagybetűt!";
		} elseif (!preg_match('/[0-9]/', $new_password)) {
			$message = "❌ Az új jelszónak tartalmaznia kell legalább egy számot!";
		} else {
			$new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
			$sql = "UPDATE users SET password = ? WHERE id = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("si", $new_hashed_password, $user_id);
			$stmt->execute();
			$stmt->close();

		// Email értesítés küldése
		$user_query = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
		$user_query->bind_param("i", $user_id);
		$user_query->execute();
		$user_result = $user_query->get_result();
		if ($user_result && $user_result->num_rows > 0) {
			$user_data = $user_result->fetch_assoc();
			sendPasswordChangeNotification($user_data['email'], $user_data['username']);
		}
		$user_query->close();

		$message = "✅ Sikeres jelszóváltoztatás! Értesítést küldtünk az e-mail címedre.";

			}
		}
?>
<!DOCTYPE html>
<html lang="hu">
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="Kajb Anna, Varasdi Vanda">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Jelszó Módosítása</title>
		<link rel="stylesheet" href="css/styles.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	</head>
	<body>
		<div class="password-container">
			<div class="password-box">
				<h2>🔒 Jelszó Módosítása</h2>

				<?php if ($message): ?>
					<div class="password-message <?php echo strpos($message, '✅') !== false ? 'password-success' : 'password-error'; ?>">
						<?php echo $message; ?>
					</div>
				<?php endif; ?>

				<form method="POST" action="change_password.php" class="password-box">
					<label for="current_password">Jelenlegi jelszó</label>
					<input type="password" name="current_password" class="password-input" required>

					<label for="new_password">Új jelszó</label>
					<input type="password" name="new_password" id="new_password" class="password-input" required>
					<small id="password-requirements" class="password-requirements">🔐 Legalább 8 karakter, 1 nagybetű és 1 szám</small>

					<label for="confirm_password">Új jelszó újra</label>
					<input type="password" name="confirm_password" class="password-input" required>

					<button type="submit" class="password-btn">Mentés</button>
				</form>

				<a href="myuser.php" class="password-btn-secondary">⬅️ Vissza a profilhoz</a>
			</div>
		</div>

		<script>
			document.getElementById("new_password").addEventListener("focus", function () {
				document.getElementById("password-requirements").style.display = "block";
			});
			document.getElementById("new_password").addEventListener("blur", function () {
				document.getElementById("password-requirements").style.display = "none";
			});
		</script>
	</body>
</html>
