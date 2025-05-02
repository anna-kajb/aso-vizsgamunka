<?php
	require "config.php";
	require "functions.php";
	session_start();

	if (!isset($_SESSION['verify_email'])) {
		echo "Hozzáférés megtagadva!";
		exit();
	}

	$email = $_SESSION['verify_email'];
	$success = false;
	$error = "";

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$code = $_POST['code'] ?? '';

		$stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND verification_code = ?");
		$stmt->bind_param("ss", $email, $code);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows == 1) {
			$conn->query("UPDATE admin SET verified = 1, verification_code = NULL WHERE email = '$email'");
			unset($_SESSION['verify_email']);
			header("Location: admin_login.php");
			exit();
		} else {
			$error = "Hibás kód, kérlek ellenőrizd az e-mailt!";
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
		<title>Admin Hitelesítés</title>
</head>
<body class="reg_body">
    <main class="reg_main">
        <div class="reg_box">
            <h2 class="regh1">Admin fiók megerősítése</h2>
            <?php if ($error): ?>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="post">
                <input type="text" name="code" placeholder="Hitelesítési kód" required><br><br>
                <input type="submit" value="Megerősítem!">
            </form>
        </div>
    </main>
</body>
</html>
