<?php
	require_once "config.php";
	session_start();

	$mentve = false;

	// 🔒 Húzd be a felhasználónevét a users táblából a bejelentkezett ID alapján
	$felhasznalonev = null;
	if (isset($_COOKIE['id'])) {
		$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
		$stmt->bind_param("i", $_COOKIE['id']);
		$stmt->execute();
		$stmt->bind_result($username);
		if ($stmt->fetch()) {
			$felhasznalonev = $username;
		}
		$stmt->close();
	}

	if (isset($_GET['st']) && $_GET['st'] === 'Completed' && isset($_GET['amt']) && $felhasznalonev !== null) {
		$osszeg = floatval($_GET['amt']);
		$menhely = isset($_GET['shelter']) ? urldecode($_GET['shelter']) : 'Ismeretlen menhely';

		$stmt = $conn->prepare("INSERT INTO adomanyok (felhasznalonev, kutyamenhely_nev, utalt_osszeg) VALUES (?, ?, ?)");
		$stmt->bind_param("ssd", $felhasznalonev, $menhely, $osszeg);
		$stmt->execute();
		$stmt->close();

		$mentve = true;
	}



?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Köszönjük az adományod!</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f3f3f3;
            color: #333;
            text-align: center;
            padding: 80px 20px;
        }

        .thank-you-box {
            display: inline-block;
            background-color: #ffffff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2ecc71;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2rem;
        }

        a.vissza-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        a.vissza-btn:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="thank-you-box">
        <h1>Köszönjük az adományod!</h1>
        <p>Az adomány sikeresen megérkezett.<br>
        Nagy segítség minden menhely számára, amit tettél!</p>
		<p>
    <?php if ($mentve): ?>
        Az adomány (<?php echo number_format($osszeg, 0, ',', ' '); ?> Ft) sikeresen rögzítésre került a(z) <strong><?php echo htmlspecialchars($menhely); ?></strong> menhelyhez.<br>
        Nagy segítség minden menhely számára, amit tettél!
    <?php else: ?>
        Az adomány sikeresen megérkezett.<br>
        Köszönjük, hogy segítettél!
    <?php endif; ?>
		</p>
        <a href="shelters.php" class="vissza-btn">Vissza a menhelyekhez</a>
    </div>
</body>
</html>
