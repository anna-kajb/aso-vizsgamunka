<?php 
  require "config.php";
  session_start(); 
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Kajb Anna, Varasdi Vanda">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <title>A.S.O.</title>
	<script>
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedShelter = urlParams.get("shelter");

            if (selectedShelter) {
                const shelterCard = document.querySelector(`[data-name='${selectedShelter}']`);
                if (shelterCard) {
                    shelterCard.classList.add("highlight");
                    shelterCard.scrollIntoView({ behavior: "smooth", block: "center" });
                    const details = shelterCard.querySelector(".card-details");
                    if (details) details.style.display = "block";
                }
            }
        });
    </script>
    <style>
        
    </style>
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
				<li><a href="users.php">Profilom</a></li>
				<li><a href="connection.php">Kapcsolat</a></li>
				<li><a href="logout.php">Kijelentkezés</a></li>
			</ul>
		</header>
		
		<div class="container">
			<div class="cards">
				<?php
		$shelters = [
			["name" => "Menhely az Állatokért", "city" => "Kecskemét", "county" => "Bács-Kiskun", "image" => "shelters-1.jpg"],
			["name" => "Misina", "city" => "Pécs", "county" => "Baranya", "image" => "shelters-2.jpg"],
			["name" => "Pécs Környéki Állatmentő Alapítvány", "city" => "Pécs", "county" => "Baranya", "image" => "shelters-3.jpg"],
			["name" => "Remény-Lak Állatvédő Egyesület", "city" => "Békéscsaba", "county" => "Békés", "image" => "shelters-4.jpg"],
			["name" => "Miskolci Állatsegítő Alapítvány", "city" => "Miskolc", "county" => "Borsod-Abaúj-Zemplén", "image" => "shelters-5.avif"],
			["name" => "Tappancs Állatvédő Alapítvány", "city" => "Szeged", "county" => "Csongrád-Csanád", "image" => "shelters-6.avif"],
			["name" => "Aska Segítőkéz Alapítvány", "city" => "Székesfehérvár", "county" => "Fejér", "image" => "shelters-7.avif"],
			["name" => "HEROSZ Fehérvári Állatotthon", "city" => "Székesfehérvár", "county" => "Fejér", "image" => "shelters-8.avif"],
			["name" => "Emberek az Állatokért Alapítvány", "city" => "Győr", "county" => "Győr-Moson-Sopron", "image" => "shelters-9.avif"],
			["name" => "Együtt az Állatokért", "city" => "Debrecen", "county" => "Hajdú-Bihar", "image" => "shelters-10.avif"],
			["name" => "Állatokat Védjük Együtt Alapítvány", "city" => "Eger", "county" => "Heves", "image" => "shelters-11.avif"],
			["name" => "Szolnok Városi Állatotthon", "city" => "Szolnok", "county" => "Jász-Nagykun-Szolnok", "image" => "shelters-12.avif"],
			["name" => "Tappancs Állatotthon Alapítvány", "city" => "Tatabánya", "county" => "Komárom-Esztergom", "image" => "shelters-13.avif"],
			["name" => "Karancs Mentőkutyás Alapítvány", "city" => "Salgótarján", "county" => "Nógrád", "image" => "shelters-14.avif"],
			["name" => "Csömöri Állatvédő Civil Szervezet", "city" => "Csömör", "county" => "Pest", "image" => "shelters-15.avif"],
			["name" => "Noé Állatotthon Alapítvány", "city" => "Budapest", "county" => "Pest", "image" => "shelters-16.jpg"],
			["name" => "Rex Kutyaotthon Alapítvány", "city" => "Budapest", "county" => "Pest", "image" => "shelters-17.avif"],
			["name" => "HEROSZ", "city" => "Budapest", "county" => "Pest", "image" => "shelters-18.avif"],
			["name" => "Összefogás a Szánhúzókért", "city" => "Budapest", "county" => "Pest", "image" => "shelters-19.avif"],
			["name" => "Budaörsi Állatmenhely", "city" => "Budaörs", "county" => "Pest", "image" => "shelters-20.avif"],
			["name" => "Ürömi Menhely", "city" => "Üröm", "county" => "Pest", "image" => "shelters-21.avif"],
			["name" => "Siófoki Állatvédők", "city" => "Siófok", "county" => "Somogy", "image" => "shelters-22.avif"],
			["name" => "Barcsi Természet- és Állatvédő Egyesület", "city" => "Barcs", "county" => "Somogy", "image" => "shelters-23.avif"],
			["name" => "Állatbarát Alapítvány", "city" => "Nyíregyháza", "county" => "Szabolcs-Szatmár-Bereg", "image" => "shelters-24.avif"],
			["name" => "Hangtalanokért Állatvédő Egyesület", "city" => "Nyíregyháza", "county" => "Szabolcs-Szatmár-Bereg", "image" => "shelters-25.avif"],
			["name" => "Olt-Alom Állatotthon", "city" => "Nyíregyháza", "county" => "Szabolcs-Szatmár-Bereg", "image" => "shelters-26.avif"],
			["name" => "Ugatlak Alapítvány", "city" => "Kisvárda", "county" => "Szabolcs-Szatmár-Bereg", "image" => "shelters-27.avif"],
			["name" => "Szekszárdi Kutyamenhely", "city" => "Szekszárd", "county" => "Tolna", "image" => "shelters-28.avif"],
			["name" => "Ábránd Állatotthon", "city" => "Szombathely", "county" => "Vas", "image" => "shelters-29.avif"],
			["name" => "Bogáncs Állatmenhely", "city" => "Szombathely", "county" => "Vas", "image" => "shelters-30.avif"],
			["name" => "Vahur Menhely", "city" => "Szombathely", "county" => "Vas", "image" => "shelters-31.avif"],
			["name" => "Assisi Szent Ferenc Alapítvány", "city" => "Szombathely", "county" => "Vas", "image" => "shelters-32.avif"],
			["name" => "Fekete István Állatvédő Egyesület", "city" => "Szombathely", "county" => "Vas", "image" => "shelters-33.avif"],
			["name" => "Vackoló Állatmenhely", "city" => "Veszprém", "county" => "Veszprém", "image" => "shelters-34.avif"],
			["name" => "Olt-Alom Állatvédő Egyesület", "city" => "Veszprém", "county" => "Veszprém", "image" => "shelters-35.avif"],
			["name" => "Alex Állatvédő Egyesület", "city" => "Veszprém", "county" => "Veszprém", "image" => "shelters-36.avif"],
			["name" => "PUMI Veszprémi Állatvédelmi Kompetencia Központ", "city" => "Veszprém", "county" => "Veszprém", "image" => "shelters-37.avif"],
			["name" => "Élettér Állat- és Természetvédő Egyesület", "city" => "Zalaegerszeg", "county" => "Zala", "image" => "shelters-38.jpg"]
		];
		?>

		<div class="row g-4">
			<?php foreach ($shelters as $shelter): ?>
				<div class="col-12 col-md-6 col-lg-4">
					<div class='card h-100 text-center' data-name="<?= htmlspecialchars($shelter['name']) ?>">
						<img src='img/shelters/<?= $shelter['image'] ?>' class="card-img-top" alt="<?= htmlspecialchars($shelter['name']) ?>">
						<div class="card-body">
							<h5 class="card-title"><?= htmlspecialchars($shelter['name']) ?></h5>
							<p class="card-text">
								<strong>Város:</strong> <?= htmlspecialchars($shelter['city']) ?><br>
								<strong>Megye:</strong> <?= htmlspecialchars($shelter['county']) ?>
							</p>
							<a href='donate.php?shelter=<?= urlencode($shelter['name']) ?>' class='btn btn-success'>Adományozás</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<script>
			document.querySelectorAll(".card").forEach(card => {
				card.addEventListener("click", () => {
					const details = card.querySelector(".card-details");
					details.style.display = details.style.display === "block" ? "none" : "block";
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
