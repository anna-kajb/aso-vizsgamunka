<?php
	ob_start(); // Bufferelés, hogy ne legyen header error
	session_start();
	require "config.php";
	require_once "functions.php";

	$login = false;

	// REGISZTRÁCIÓ
	if (isset($_POST['reg-btn'])) {
		$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		$username = $_POST['username'];
		$pass1 = $_POST['pass1'];
		$pass2 = $_POST['pass2'];

		// Ellenőrizzük, hogy már létezik-e a felhasználónév vagy e-mail
		$query = "SELECT * FROM users WHERE username=? OR email=?";
		$stmt = $conn->prepare($query);
		$stmt->bind_param("ss", $username, $email);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();

		if ($result->num_rows == 0) {
			if ($pass1 === $pass2) {
				$hashed_password = password_hash($pass1, PASSWORD_DEFAULT);
				$verification_code = getUniqueVerificationCode($conn); // Egyedi kód generálása

				// Új felhasználó beszúrása az adatbázisba
				$insertQuery = "INSERT INTO users (username, email, password, verification_code, verified, profile_pic) VALUES (?, ?, ?, ?, 0, 'default.png')";
				$stmt = $conn->prepare($insertQuery);
				$stmt->bind_param("ssss", $username, $email, $hashed_password, $verification_code);

				if ($stmt->execute()) {
					$user_id = $stmt->insert_id; // Az új ID lekérése
					$stmt->close();

					sendVerificationEmail($email, $username, $verification_code);

					// REGISZTRÁCIÓ UTÁN AUTOMATIKUS BEJELENTKEZTETÉS
					$_SESSION['user_id'] = $user_id;
					setcookie("id", $user_id, time() + 3600, "/");
					
					header("Location: verify.php");
					exit();
				} else {
					Message("Hiba történt a regisztráció során.", "reg.php");

				}
			} else {
				Message("A két jelszó nem egyezik!", "reg.php");

			}
		} else {
			Message("Már létezik ilyen nevű vagy e-mail című felhasználó!", "reg.php");
		}
	}

	// BEJELENTKEZÉS
	if (isset($_POST['login-btn'])) {
		$username_email = $_POST['username_email'];
		$password = $_POST['password'];

		$lekerdezes = "SELECT * FROM users WHERE (username=? OR email=?) AND verified=1";
		$stmt = $conn->prepare($lekerdezes);
		$stmt->bind_param("ss", $username_email, $username_email);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();

		if ($result->num_rows == 1) {
			$felhasznalo = $result->fetch_assoc();

			if (password_verify($password, $felhasznalo['password'])) {
				$_SESSION['user_id'] = $felhasznalo['id'];
				$_SESSION['username'] = $felhasznalo['username']; // 🔥 EZ KELL NEKÜNK

				setcookie("id", $felhasznalo['id'], time() + 3600, "/");

				header("Location: users.php");
				exit();
			} else {
				Message("Hibás Jelszó!", "reg.php");
			}
		} else {
			Message("Nincs ilyen felhasználó vagy még nem hitelesítetted a fiókodat!", "reg.php");
		}

	}

	

	// ANONIM BELÉPÉS
	if (isset($_GET['anon']) && $_GET['anon'] == 'true') {
		$random_number = rand(1000, 9999);
		$anon_username = "Noname_" . $random_number;
		$titkositott_jelszo = password_hash("nonamepassword", PASSWORD_DEFAULT);
		$anon_email = "anon_" . $random_number . "@example.com";

		// Új anonim felhasználó beszúrása
		$stmt = $conn->prepare("INSERT INTO users (username, email, password, verification_code, verified, profile_pic) VALUES (?, ?, ?, '', 1, 'default.png')");
		$stmt->bind_param("sss", $anon_username, $anon_email, $titkositott_jelszo);

		if (!$stmt->execute()) {
			die("Hiba az anonim fiók létrehozásánál: " . $stmt->error);
		}

		$anon_id = $conn->insert_id;
		$stmt->close();

		$_SESSION['user_id'] = $anon_id;
		setcookie("id", $anon_id, time() + 3600, "/");

		header("Location: users.php");
		exit();
	}
?>

<!DOCTYPE html>
<html lang="hu">
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="Kajb Anna, Varasdi Vanda">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="css/styles.css">
		
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script src="https://kit.fontawesome.com/086e7cefb3.js" crossorigin="anonymous"></script>

		<title>Regisztráció - Bejelentkezés</title>
	</head>
	<body class="reg_body">
		<header class="bg-dark text-light">
		<div class="logo-container">
			<a href="admin_login.php">
				<img id="logo" src="img/01-logo.jpg" alt="Logo">
			</a>
		</div>
		<ul class="menu">
			<li><a href="index.php">Kezdőlap</a></li>
		
			<li><a href="shelters.php">Kutyamenhelyek</a></li>
			
			<li><a href="connection.php">Kapcsolat</a></li>
			
		</ul>
		</header>
		<main class="reg_main">
			<div id="reg-form" class="reg_box" style="display: none;">
				<h1 class="regh1" style="color: black;">Regisztráció</h1>
				<form method="post" action="reg.php">
					<input name="username" type="text" placeholder="Felhasználónév" required><br><br>
					
					<input name="email" type="email" placeholder="E-mail" required><br><br>
					<label id="email-msg"></label>
					
					<input id="pass1" name="pass1" type="password" placeholder="Jelszó" required><br><br>
					<label style="font-size: 12px; color: gray; display: none;" id="length"><span id="length-check"></span> A jelszó legyen minimum 8 karakter hosszú!</label>
					<br>
					<label style="font-size: 12px; color: gray; display: none;" id="upper"><span id="upper-check"></span> A jelszó tartalmazzon minimum egy nagybetűt!</label>
					<br>
					<label style="font-size: 12px; color: gray; display: none;" id="number"><span id="number-check"></span> A jelszó tartalmazzon minimum egy számot!</label>
					<br><br>
					
					<input id="pass1" name="pass2" type="password" placeholder="Jelszó újra" required><br><br>
					
					<input name="reg-btn" type="submit" value="Regisztrálok!" required><br><br>
					
					<span class="reg-span">Már van fiókod? <a href="#" onclick="openForm('login')">Lépj be!</a></span><br><br>
					
					<button name="anon-btn" type="button" onclick="window.location.href='reg.php?anon=true';">Belépés Név Nélkül</button><br>

					<p style="font-size: 12px; color: gray;">* Az anonim belépéssel ideiglenes fiókot hozunk létre. 
					Nem tárolunk semmilyen e-mail címet vagy jelszót. Kilépés után az adatod törlődik.</p>
				</form>
			</div>

			<div id="login-form" class="reg_box" style="display: block;">
				<h1 class="regh1" >Bejelentkezés</h1>
				<form method="post" action="reg.php">
					<input name="username_email" type="text" placeholder="Felhasználónév/E-mail" required><br><br>
					
					<input name="password" type="password" placeholder="Jelszó" required><br><br>
					
					<input name="login-btn" type="submit" value="Bejelentkezek!" required><br><br>
					<span class="reg-span">Még nincs fiókod? <a href="#" onclick="openForm('reg')">Regisztrálj!</a></span><br><br>
					
					<p class="reg-span">
						<a href="forgot_password.php">Elfelejtetted a jelszavad?</a>
					</p><br>
					
					<button  name="anon-btn" type="button" onclick="window.location.href='reg.php?anon=true';">
						Belépés név nélkül
					</button>
					
					<p style="font-size: 12px; color: gray;">* Az anonim belépéssel ideiglenes fiókot hozunk létre.
					Nem tárolunk semmilyen e-mail címet vagy jelszót. Kilépés után az adatod törlődik.</p>
				</form>
			</div>
		</main>
		
		<footer class="footer mt-auto py-3">
    <div class="container text-center">
        <p class="mb-0">&copy; 2025 A.S.O. - Állatmentő Szolgálat Online</p>
        <small>Készült: Kajb Anna & Varasdi Vanda projektmunkája keretében. <br> Minden jog fenntartva.</small>
    </div>
		</footer>
		
		
	</body>
	
	<script>
		function openForm(form) {
			var loginForm = document.getElementById("login-form");
			var regForm = document.getElementById("reg-form");

			if (form == "login") {
				loginForm.style.display = "block";
				regForm.style.display = "none";
			} else {
				loginForm.style.display = "none";
				regForm.style.display = "block";
			}
		}
	</script>
	
	<script>
	// Ellenőrzi, hogy a jelszó tartalmaz-e nagybetűt
	function containsUpper(str) {
		return /[A-Z]/.test(str);
	}

	// Ellenőrzi, hogy a jelszó tartalmaz-e számot
	function containsNumber(str) {
		return /\d/.test(str);
	}

	document.getElementById('pass1').addEventListener('keyup', (e) => {
		var password = e.target.value;
		var lengthText = document.getElementById("length");
		var upperText = document.getElementById("upper");
		var numberText = document.getElementById("number");

		// Az üzenetek megjelenítése
		lengthText.style.display = "block";
		upperText.style.display = "block";
		numberText.style.display = "block";

		// Hossz
		if (password.length >= 8) {
			document.getElementById("length-check").innerHTML = '<i class="fa-solid fa-check"></i>';
			lengthText.style.color = "green";
		} else {
			document.getElementById("length-check").innerHTML = '<i class="fa-solid fa-x"></i>';
			lengthText.style.color = "red";
		}

		// Nagybetű
		if (containsUpper(password)) {
			document.getElementById("upper-check").innerHTML = '<i class="fa-solid fa-check"></i>';
			upperText.style.color = "green";
		} else {
			document.getElementById("upper-check").innerHTML = '<i class="fa-solid fa-x"></i>';
			upperText.style.color = "red";
		}

		// Szám
		if (containsNumber(password)) {
			document.getElementById("number-check").innerHTML = '<i class="fa-solid fa-check"></i>';
			numberText.style.color = "green";
		} else {
			document.getElementById("number-check").innerHTML = '<i class="fa-solid fa-x"></i>';
			numberText.style.color = "red";
		}
	});

</script>
</html>
