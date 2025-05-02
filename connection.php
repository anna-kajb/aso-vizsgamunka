<?php
	require "config.php";
	require "functions.php";
	session_start();

	//Ellenőrizzük, h van-e bejelentkezett felhasználó
	$felhasznalo_id = null; 
	$felhasznalo_email = ""; 
	
	if(isset($_COOKIE['id'])){
		
		$felhasznalo_id = intval($_COOKIE['id']);
	}

	// Bejelentkezett felhasználó lekérdezése
	$lekerdezes = "SELECT * FROM users WHERE id= ?"; // ? helyörző, ami egy konkrét értékkel tér vissza
	$talalt_felhasznalo = $conn->prepare($lekerdezes); //előkészítés
	$talalt_felhasznalo->bind_param("i", $felhasznalo_id); //megadja a helyörző értékét + milyen típusú adat
	$talalt_felhasznalo->execute(); //lekerdezes végrehajtása
	$felhasznalo = $talalt_felhasznalo->get_result()->fetch_assoc(); // eredmeny lekérés és adatbázishoz adás
	$talalt_felhasznalo->close();

	//Felhasználó ellenőrzés(regisztrált a felhasználó vagy sem )
	if($felhasznalo){
		
		$felhasznalo_email = $felhasznalo['email'];
		
	}else{ //ha nincs ilyen felhasználó, ne használjunk id-t
		
		$felhasznalo_id = NULL; 
	}

	// Ha van POST kérés, mentjük az adatokat az adatbázisba
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['msg-btn'])) {
		
		$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
		$message = htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8');

		//Ellenőrizzük, h az email cm szerepel az adatbázisban
		$lekerdezes = "SELECT id FROM users WHERE email = ?";
		$talalt_felhasznalo = $conn->prepare($lekerdezes);
		$talalt_felhasznalo->bind_param("s", $email);
		$talalt_felhasznalo->execute(); 
		$felhasznalo = $talalt_felhasznalo->get_result(); 
		$talalt_felhasznalo->close(); 
		
		$regisztralt_user_id = (mysqli_num_rows($felhasznalo) > 0) ? $felhasznalo->fetch_assoc()['id'] : NULL; 

		// Üzenet mentése adatbázisba
		//(ha nincs bejelentkezve, akkor NULL kerül a felhasznalo_id helyére)
		$tablaba_szuras = "INSERT INTO kapcsolat (felhasznalo_id, email, uzenet) VALUES (?, ?, ?)";
		$tabla = $conn->prepare($tablaba_szuras);
		$tabla->bind_param("iss", $regisztralt_user_id, $email, $message);

		if ($tabla->execute()) {
			
			$success_message = "Üzenet sikeresen elküldve!";
			
		} else {
			
			$error_message = "Hiba történt: " . $stmt->error;
		}
		$tabla->close();
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
		<title>A.S.O. - Kapcsolat</title>
		</head>
	<body>
		<header class="bg-dark text-light">
			<div class="logo-container">
				<a href="admin_login.php">
					<img id="logo" src="img/01-logo.jpg" alt="Logo">
				</a>
			</div>
			<ul class="menu">
				<li><a href="users.php">Kezdőlap</a></li>
				<li><a href="search.php">Keresés</a></li>
				<li><a href="shelters.php">Kutyamenhelyek</a></li>
				<li><a href="myuser.php">Profilom</a></li>
				<li><a href="connection.php">Kapcsolat</a></li>
				<li><a href="logout.php">Kijelentkezés</a></li>
			</ul>
		</header>
		
		<div class="contact-info">
			<h3>Elérhetőségeink</h3>
			<p class="p-tag"><strong>Email:</strong> info@peldadomain.hu</p>
			<p class="p-tag"><strong>Telefon:</strong> +36 30 123 4567</p>
			<p class="p-tag"><strong>Cím:</strong> 1051 Budapest, Példa utca 12.</p>
		</div>
		
		
		<!-- GYIK részleg -->
		<div class="gyik">
			<h2 class="gyik-h2">Gyakran ismételt kérdések (GYIK)</h2>
			
			<div class="gyik-item">
				<h3 class="gyik-question">Hogyan regisztrálhatok az oldalra?</h3>
				<p class="gyik-answer">A "Regisztráció/Bejelentkezés" fülre kattintva tudsz regisztrálni az oldalra.<br>
					Létrehozhatsz saját fiókot vagy bejelentkezhetsz anonim módon is.<br>
					Anonim módban a kijelentkezéskor minden adat automatikusan törlésre kerül.</p>
			</div>
			
			<div class="gyik-item">
				<h3 class="gyik-question">Mi a válaszidő?</h3>
				<p class="gyik-answer">Az általános válaszidő 24-48 óra. 
					Ha sürgős kérdésed van, kérjük vedd fel velünk a kapcsolatot telefonon vagy e-mailben.</p>
			</div>
			
			<div class="gyik-item">
				<h3 class="gyik-question">Hogyan módosíthatom a profilomat?</h3>
				<p class="gyik-answer">A profiladatokat a "Profilom" menüpontban tudod módosítani.
					Itt frissítheted az elérhetőségeidet, a jelszavadat és egyéb adatokat.</p>
			</div>
		</div>

		
		<!--Üzetenküldő form -->
		<div class="connection-page">
			<h2 class="connection-title">Kapcsolat</h2>

			<?php if (isset($success_message)): ?>
				<div class="alert alert-success connection-alert">
					<?php echo $success_message; ?>
				</div>
			<?php endif; ?>

			<?php if (isset($error_message)): ?>
				<div class="alert alert-danger connection-alert">
					<?php echo $error_message; ?>
				</div>
			<?php endif; ?>
			
			<form class="connection-form" method="POST" action="connection.php">
				<div class="form-group">
					<label class="connection-label" for="email">Email:</label>
					<input type="email" class="connection-input" name="email" id="email" required>
				</div>

				<div class="form-group">
					<label class="connection-label" for="message">Üzenet:</label>
					<textarea class="connection-input" name="message" id="message" required></textarea>
				</div>

				<button type="submit" name="msg-btn" class="connection-button">Küldés</button>
			</form>
		</div>
		
		<script>
			document.querySelectorAll('.gyik-question').forEach(function(question) {
				question.addEventListener('click', function() {
					let parent = question.parentNode;
					parent.classList.toggle('open'); // Nyitás-zárás
				});
			});
		</script>
		
		<footer class="footer mt-auto py-3">
    <div class="container text-center">
        <p class="mb-0">&copy; 2025 A.S.O. - Adományozást Segítő Oldal</p>
        <small>Készült: Kajb Anna & Varasdi Vanda projektmunkája keretében. <br> Minden jog fenntartva.</small>
    </div>
		</footer>
		
	</body>
</html>
