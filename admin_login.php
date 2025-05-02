<?php
	require "config.php";
require "functions.php";
session_start();

// Regisztráció
if (isset($_POST['reg-btn'])) {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    $lekerdezes = "SELECT * FROM admin WHERE username='$username' OR email='$email'";
    $talalt_felhasznalo = $conn->query($lekerdezes);

    if (mysqli_num_rows($talalt_felhasznalo) == 0) {
        if ($pass1 == $pass2) {
            $titkositott_jelszo = password_hash($pass1, PASSWORD_DEFAULT);
            $verification_code = getUniqueAdminVerificationCode($conn);

            $stmt = $conn->prepare("INSERT INTO admin (username, email, password, verification_code, verified, role) VALUES (?, ?, ?, ?, 0, 'admin')");
            $stmt->bind_param("ssss", $username, $email, $titkositott_jelszo, $verification_code);
            $stmt->execute();
            $stmt->close();

            sendAdminVerificationEmail($email, $username, $verification_code);

            $_SESSION['verify_email'] = $email;
            header("Location: verify_admin.php");
            exit();
        } else {
           Message("A két jelszó nem egyezik!", "admin_login.php");

        }
    } else {
        Message("Már létezik ilyen nevű/e-mail című felhasználó!", "admin_login.php");

    }
}

// Bejelentkezés
if (isset($_POST['login-btn'])) {
    $username_email = $_POST['username_email'];
    $password = $_POST['password'];

    $lekerdezes = "SELECT * FROM admin WHERE (username=? OR email=?) AND verified=1";
    $stmt = $conn->prepare($lekerdezes);
    $stmt->bind_param("ss", $username_email, $username_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $felhasznalo = $result->fetch_assoc();

        if (password_verify($password, $felhasznalo['password'])) {
            if ($felhasznalo['role'] == 'admin') {
                setcookie("id", $felhasznalo['id'], time() + 3600, "/");
                header("Location: admin.php");
                exit();
            } else {
                Message("Nincs admin jogosultságod!", "admin_login.php");
            }
        } else {
            Message("Hibás jelszó!",  "admin_login.php");
        }
    } else {
        Message("Hibás adatok vagy még nem erősítetted meg az e-mail címet.",  "admin_login.php");
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
		<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script src="https://kit.fontawesome.com/086e7cefb3.js" crossorigin="anonymous"></script>
		<title>Admin Bejelentkezés - Regisztráció</title>
	</head>
	<body class="reg_body">
	<main class="reg_main">
		<!--<div id="reg-form" class="reg_box" style="display: none;">
			<h1 class="regh1" style="color: black;">Regisztráció</h1>
			<form method="post" action="admin_login.php">
				<input name="username" type="text" placeholder="Felhasználónév" required><br><br>
				<input name="email" type="email" placeholder="E-mail" required><br><br>
				<input id="pass1" name="pass1" type="password" placeholder="Jelszó" required><br><br>
				<input id="pass1" name="pass2" type="password" placeholder="Jelszó újra" required><br><br>
				<input name="reg-btn" type="submit" value="Regisztrálok!"><br><br>
				<span class="reg-span">Már van fiókod? <a href="#" onclick="openForm('login')">Lépj be!</a></span><br><br>
			</form>
		</div> -->

		<div id="login-form" class="reg_box" style="display: block;">
			<h1 class="regh1">Bejelentkezés</h1>
			<form method="post" action="admin_login.php">
				<input name="username_email" type="text" placeholder="Felhasználónév/E-mail" required><br><br>
				<input name="password" type="password" placeholder="Jelszó" required><br><br>
				<input name="login-btn" type="submit" value="Bejelentkezek!"><br><br>
				
			</form>
		</div>
	</main>
	</body>

	<script>
		function openForm(form) {
			var loginForm = document.getElementById("login-form");
			var regForm = document.getElementById("reg-form");

			if (form === "login") {
				loginForm.style.display = "block";
				regForm.style.display = "none";
			} else {
				loginForm.style.display = "none";
				regForm.style.display = "block";
			}
		}
	</script>
</html>
