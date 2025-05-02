<?php
	require "config.php";
	session_start(); 

	// Ha van POST kérés, mentjük az adatokat az adatbázisba
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$felhasznalonev = $_POST['felhasznalonev'];
		$kutyamenhely_nev = $_POST['kutyamenhely_nev'];
		$utalt_osszeg = $_POST['utalt_osszeg'];

		// Adatok beszúrása
		$stmt = $conn->prepare("INSERT INTO adomanyok (felhasznalonev, kutyamenhely_nev, utalt_osszeg) VALUES (?, ?, ?)");
		$stmt->bind_param("ssd", $felhasznalonev, $kutyamenhely_nev, $utalt_osszeg);
		if ($stmt->execute()) {
			Message("Adomány sikeresen mentve!");
		} else {
			Message("Hiba: " . $stmt->error);
		}
		$stmt->close();
	}

	// Adatok lekérdezése
	$sql = "SELECT felhasznalonev, kutyamenhely_nev, utalt_osszeg, datum FROM adomanyok ORDER BY datum DESC";
	$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="hu">
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="Kajb Anna, Varasdi Vanda">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- Bootstrap CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="css/styles.css">
		<title>A.S.O.</title>
	</head>
	<body>
		<header class="bg-dark text-light">
			<div class="logo-container">
				<a href="admin_login.php">
					<img id="logo" src="img/01-logo.jpg" alt="Logo">
				</a>
			</div>
			<ul class="menu">
				<li><a href="index.php">Kezdőlap</a></li>
				<li><a href="reg.php">Regisztráció/Bejelentkezés</a></li>
				<li><a href="shelters.php">Kutyamenhelyek</a></li>
				<li><a href="connection.php">Kapcsolat</a></li>
				<li><a href="logout.php">Kijelentkezés</a></li>
			</ul>
		</header>
		
		<div class="container1">
			<h1 id="indexh1">Adományozást Segítő Oldal!</h1>
			<p>Üdvözlünk az oldalunkon!</p>
			<p>Azért hoztuk létre ezt a platformot, hogy megkönnyítsük az adományozást az ország kutyamenhelyei számára. Összegyűjtöttük Magyarország összes olyan menhelyét, amely elérhető a Google-ben, és nyilvánosságra hozta a bankszámlaszámát – így bárki egyszerűen, biztonságosan támogathatja őket, akár célzottan, akár véletlenszerűen választva.</p>
			<p>Ha úgy látod, hogy egy menhely kimaradt a listáról, örömmel fogadjuk a segítségedet! Ebben az esetben kérünk, hogy: amennyiben van weboldala, írd meg a menhely nevét, ha nincs online jelenléte, akkor add meg a megyét, a várost, a menhely nevét és a számlaszámot (ha lehetséges).</p>
			<p>A támogatásodat előre is hálásan köszönjük!</p>
			<p>A használathoz először <a href=reg.php style=color:blue;>regisztrálj</a>, majd jelentkezz be – de ha nem szeretnéd megosztani az adataidat, anonim módban is beléphetsz.</p>

		</div>
		
		<!-- Mobil nézetben a menü megjelenítése -->
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				const menuToggle = document.getElementById('menu-toggle');
				const menu = document.querySelector('.menu');

				menuToggle.addEventListener('click', () => {
					menu.classList.toggle('active');
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
