<?php
	require "config.php";
	session_start(); 

	//Ellenőrizzük, hogy létezik a felhasználó profilja 
	if(isset($_COOKIE['id'])){

		$lekerdezes = "SELECT * FROM users WHERE id=$_COOKIE[id]";
		$talalt_felhasznalo = $conn->query($lekerdezes);
		$felhasznalo = $talalt_felhasznalo->fetch_assoc();
		
	}
	//ha nem, akkor átírányítjuk a bejelentkezés oldalra 
	else{
		header("Location: reg.php");
	}


	// Ha van POST kérés, mentjük az adatokat az adatbázisba
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$felhasznalonev = $felhasznalo['username']; // automatikus
	$kutyamenhely_nev = $_POST['kutyamenhely_nev'];
	$utalt_osszeg = $_POST['utalt_osszeg'];

	$stmt = $conn->prepare("INSERT INTO adomanyok (felhasznalonev, kutyamenhely_nev, utalt_osszeg) VALUES (?, ?, ?)");
	$stmt->bind_param("ssd", $felhasznalonev, $kutyamenhely_nev, $utalt_osszeg);
	if ($stmt->execute()) {
		echo "Adomány sikeresen mentve!";
	} else {
		echo "Hiba: " . $stmt->error;
	}
	$stmt->close();
}


	// Adatok lekérdezése
	$felhasznalonev = $felhasznalo['username'];

	$sql = "SELECT felhasznalonev, kutyamenhely_nev, utalt_osszeg, datum 
			FROM adomanyok 
			WHERE felhasznalonev = ?
			ORDER BY datum DESC";

	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $felhasznalonev);
	$stmt->execute();
	$result = $stmt->get_result();

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
		<title>A.S.O.</title>
	</head>
	<body>
		<header class="bg-dark text-light">
			<h1>Hello, <?=  $felhasznalo['username']; ?>!</h1>
			<ul class="menu">
				<li><a href="users.php">Kezdőlap</a></li>
				<li><a href="search.php">Keresés</a></li>
				<li><a href="shelters.php">Kutyamenhelyek</a></li>
				<li><a href="myuser.php">Profilom</a></li>
				<li><a href="connection.php">Kapcsolat</a></li>
				<li><a href="logout.php">Kijelentkezés</a></li>
			</ul>
		</header>
		<div class="donation-container">
	<h2>Legutóbbi adományok</h2> <br>
		<div>
			<?php
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					echo "<div class='donation-card'>";
					echo "<strong>Felhasználónév:</strong> " . htmlspecialchars($row['felhasznalonev']) . "<br>";
					echo "<strong>Kutyamenhely:</strong> " . htmlspecialchars($row['kutyamenhely_nev']) . "<br>";
					echo "<strong>Összeg:</strong> " . htmlspecialchars($row['utalt_osszeg']) . " Ft<br>";
					echo "<strong>Dátum:</strong> " . htmlspecialchars($row['datum']) . "<br>";
					echo "</div>";
				}
			} else {
				echo "<p>Még nincsenek adományok.</p>";
			}
			$conn->close();
			?>
		</div>
	</div>
		
		<footer class="footer mt-auto py-3">
    <div class="container text-center">
        <p class="mb-0">&copy; 2025 A.S.O. - Adományozást Segítő Oldal</p>
        <small>Készült: Kajb Anna & Varasdi Vanda projektmunkája keretében. <br> Minden jog fenntartva.</small>
    </div>
		</footer>
	</body>
</html>
