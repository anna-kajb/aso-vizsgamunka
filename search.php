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
		<div class="container-1">
			<!-- Fejléc -->
			<div class="header">
			  <h1 style="text-align: center; color: white;">Kutyamenhely Kereső</h1>
			</div>

			<!-- Keresősáv -->
			<div class="search-bar">
				<input type="text" id="search" placeholder="Keresés menhelyek között...">
				<div id="suggestions" class="suggestions-box"></div>
			</div>


			<!-- Menhelyek listája -->
			<div id="shelterList" class="shelter-list">
			  <!-- Ide kerül a dinamikusan generált lista -->
			</div>
		  </div>
      
      <script>
  document.addEventListener("DOMContentLoaded", function () {
      const searchInput = document.getElementById("search");
      const suggestionsBox = document.getElementById("suggestions");

      // 🔹 Menhelyek adatai
      const shelters = [
  { name: "Menhely az Állatokért", county: "Bács-Kiskun", city: "Kecskemét" },
  { name: "Misina", county: "Baranya", city: "Pécs" },
  { name: "Pécs Környéki Állatmentő Alapítvány", county: "Baranya", city: "Pécs" },
  { name: "Remény-Lak Állatvédő Egyesület", county: "Békés", city: "Békéscsaba" },
  { name: "Miskolci Állatsegítő Alapítvány", county: "Borsod-Abaúj-Zemplén", city: "Miskolc" },
  { name: "Tappancs Állatvédő Alapítvány", county: "Csongrád-Csanád", city: "Szeged" },
  { name: "Aska Segítőkéz Alapítvány", county: "Fejér", city: "Székesfehérvár" },
  { name: "HEROSZ Fehérvári Állatotthon", county: "Fejér", city: "Székesfehérvár" },
  { name: "Emberek az Állatokért Alapítvány", county: "Győr-Moson-Sopron", city: "Győr" },
  { name: "Együtt az Állatokért", county: "Hajdú-Bihar", city: "Debrecen" },
  { name: "Állatokat Védjük Együtt Alapítvány", county: "Heves", city: "Eger" },
  { name: "Szolnok Városi Állatotthon", county: "Jász-Nagykun-Szolnok", city: "Szolnok" },
  { name: "Tappancs Állatotthon Alapítvány", county: "Komárom-Esztergom", city: "Tatabánya" },
  { name: "Karancs Mentőkutyás Alapítvány", county: "Nógrád", city: "Salgótarján" },
  { name: "Csömöri Állatvédő Civil Szervezet", county: "Pest", city: "Csömör" },
  { name: "Noé Állatotthon Alapítvány", county: "Pest", city: "Budapest" },
  { name: "Rex Kutyaotthon Alapítvány", county: "Pest", city: "Budapest" },
  { name: "HEROSZ", county: "Pest", city: "Budapest" },
  { name: "Összefogás a Szánhúzókért", county: "Pest", city: "Budapest" },
  { name: "Budaörsi Állatmenhely", county: "Pest", city: "Budaörs" },
  { name: "Ürömi Menhely", county: "Pest", city: "Üröm" },
  { name: "Siófoki Állatvédők", county: "Somogy", city: "Siófok" },
  { name: "Barcsi Természet- és Állatvédő Egyesület", county: "Somogy", city: "Barcs" },
  { name: "Állatbarát Alapítvány", county: "Szabolcs-Szatmár-Bereg", city: "Nyíregyháza" },
  { name: "Hangtalanokért Állatvédő Egyesület", county: "Szabolcs-Szatmár-Bereg", city: "Nyíregyháza" },
  { name: "Olt-Alom Állatotthon", county: "Szabolcs-Szatmár-Bereg", city: "Nyíregyháza" },
  { name: "Ugatlak Alapítvány", county: "Szabolcs-Szatmár-Bereg", city: "Kisvárda" },
  { name: "Szekszárdi Kutyamenhely", county: "Tolna", city: "Szekszárd" },
  { name: "Ábránd Állatotthon", county: "Vas", city: "Szombathely" },
  { name: "Bogáncs Állatmenhely", county: "Vas", city: "Szombathely" },
  { name: "Vahur Menhely", county: "Vas", city: "Szombathely" },
  { name: "Assisi Szent Ferenc Alapítvány", county: "Vas", city: "Szombathely" },
  { name: "Fekete István Állatvédő Egyesület", county: "Vas", city: "Szombathely" },
  { name: "Vackoló Állatmenhely", county: "Veszprém", city: "Veszprém" },
  { name: "Olt-Alom Állatvédő Egyesület", county: "Veszprém", city: "Veszprém" },
  { name: "Alex Állatvédő Egyesület", county: "Veszprém", city: "Veszprém" },
  { name: "PUMI Veszprémi Állatvédelmi Kompetencia Központ", county: "Veszprém", city: "Veszprém" },
  { name: "Élettér Állat- és Természetvédő Egyesület", county: "Zala", city: "Zalaegerszeg" }
  ];

      // 🔹 Dinamikus keresési javaslatok
      searchInput.addEventListener("input", () => {
          const searchValue = searchInput.value.trim().toLowerCase();
          suggestionsBox.innerHTML = "";

          if (searchValue.length > 0) {
              const filteredSuggestions = shelters.filter(shelter => 
                  shelter.name.toLowerCase().includes(searchValue) || 
                  shelter.county.toLowerCase().includes(searchValue) ||
                  shelter.city.toLowerCase().includes(searchValue)
              );

              if (filteredSuggestions.length > 0) {
                  suggestionsBox.innerHTML = "<ul>" + 
                      filteredSuggestions.map(shelter => 
                          `<li onclick="selectShelter('${shelter.name}')">
                              <strong>${highlightMatch(shelter.name, searchValue)}</strong>
                              (${highlightMatch(shelter.county, searchValue)}, ${highlightMatch(shelter.city, searchValue)})
                          </li>`
                      ).join("") + 
                      "</ul>";
                  suggestionsBox.style.display = "block";
              } else {
                  suggestionsBox.style.display = "none";
              }
          } else {
              suggestionsBox.style.display = "none";
          }
      });

      // 🔹 Ha máshova kattintunk, a találati lista eltűnik
      document.addEventListener("click", (event) => {
          if (!event.target.closest(".search-bar")) {
              suggestionsBox.style.display = "none";
          }
      });
  });

  // 🔹 Kiválasztott menhely átirányítása a shelters.php oldalra
  function selectShelter(name) {
      window.location.href = `shelters.php?shelter=${encodeURIComponent(name)}`;
  }

  // 🔹 Keresési találatok kiemelése 
  function highlightMatch(text, search) {
      if (!search) return text;
      const regex = new RegExp(`(${search})`, "gi");
      return text.replace(regex, "<span style='background:black;'>$1</span>");
  }
</script>


		<footer class="footer mt-auto py-3">
    <div class="container text-center">
        <p class="mb-0">&copy; 2025 A.S.O. - Adományozást Segítő Oldal</p>
        <small>Készült: Kajb Anna & Varasdi Vanda projektmunkája keretében. <br> Minden jog fenntartva.</small>
    </div>
		</footer>

	
	</body>
</html>
