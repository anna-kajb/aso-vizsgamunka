<?php 

	require 'config.php';
	require 'functions.php';

	$message = ""; 

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$email = $_POST['email'];

		// Helyes SQL és lekérdezés
		$sql = "SELECT * FROM users WHERE email = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows === 1) {
			$user = $result->fetch_assoc();
			$code = getUniqueVerificationCode($conn);

			// Kód mentése
			$update = $conn->prepare("UPDATE users SET verification_code = ? WHERE email = ?");
			$update->bind_param("ss", $code, $email);
			$update->execute();

			// E-mail küldés
			sendPasswordResetEmail($email, $user['username'], $code);

			// Átirányítás reset oldalra kóddal együtt
			header("Location: reset_password.php?email=" . urlencode($email) . "&code=" . urlencode($code));
			exit();
		} else {
			$message = "❌ Nem található ilyen e-mail cím!";
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
		<title>Elfelejtett jelszó</title>
	</head>
	<body class="reg_body">
		<main class="reg_main">
			<div class="reg_box">
				<h2 class="regh1">Elfelejtett jelszó</h2>

				<?php if ($message): ?>
					<p class="password-message"><?php echo $message; ?></p>
				<?php endif; ?>

				<form method="POST">
					<input type="email" name="email" placeholder="E-mail cím" required><br><br>
					<input type="submit" value="Új jelszó kérése">
				</form>

				<p class="reg-span"><a href="reg.php">Vissza a bejelentkezéshez</a></p>
			</div>
		</main>
	</body>
</html>
	

