<?php
	require "config.php";
	require "functions.php";
	session_start();
	
	// Üzenetre válasz emailben
	if (isset($_POST['send_reply'])) {
		$cimzett = $_POST['reply_email'];
		$valaszSzoveg = $_POST['reply_text'];
		$datum = $_POST['reply_datum']; 

		if (sendReplyEmail($cimzett, $valaszSzoveg)) {
			// ✅ Jelöld válaszként
			$stmt = $conn->prepare("UPDATE kapcsolat SET valaszolt = 1 WHERE email = ? AND datum = ?");
			$stmt->bind_param("ss", $cimzett, $datum);
			$stmt->execute();
			$stmt->close();

			Message("✉️ Sikeresen elküldted a választ!", "admin.php",  "success");
		} else {
			Message("❌ Hiba történt a válasz elküldésekor!", "admin.php", "error");
		}
	}



	// Ellenőrizd, hogy tényleg admin-e
	$id = $_COOKIE['id'];
	$result = $conn->query("SELECT role FROM admin WHERE id = $id");
	$row = $result->fetch_assoc();
	if ($row['role'] !== 'admin') {
		Message("Nincs jogosultságod ehhez az oldalhoz!", "admin_login.php", "error");
		exit();
	}

	// Üzenetek lekérdezése
	$lekerdezes = "SELECT felhasznalo_id, email, uzenet, datum, valaszolt FROM kapcsolat ORDER BY datum DESC";
	$talalt_uzenet = $conn->query($lekerdezes);

	// Új admin hozzáadása
	if (isset($_POST['add_admin'])) {
		$new_username = $_POST['new_username'];
		$new_email = $_POST['new_email'];
		$pass1 = $_POST['new_password1'];
		$pass2 = $_POST['new_password2'];

		if ($pass1 !== $pass2) {
			Message("A két jelszó nem egyezik!", "admin.php", "error");
		} else {
			$check = $conn->prepare("SELECT id FROM admin WHERE username = ? OR email = ?");
			$check->bind_param("ss", $new_username, $new_email);
			$check->execute();
			$check->store_result();

			if ($check->num_rows > 0) {
				Message("Már létezik ilyen nevű vagy e-mailű admin!", "admin.php", "error");
			} else {
				$hash = password_hash($pass1, PASSWORD_DEFAULT);
				$verification_code = getUniqueAdminVerificationCode($conn);
				$role = 'admin';

				$insert = $conn->prepare("INSERT INTO admin (username, email, password, verification_code, verified, role) VALUES (?, ?, ?, ?, 1, ?)");
				$insert->bind_param("sssss", $new_username, $new_email, $hash, $verification_code, $role);
				$insert->execute();

				logAdminAction($conn, $_COOKIE['id'], "Új admin hozzáadása: $new_username ($new_email)");
				sendAdminVerificationEmail($new_email, $new_username, $verification_code);

				Message("✅ Sikeresen létrehoztál egy új admint!", "admin.php",  "success");
			}
			$check->close();
		}
	}

	// Admin törlése
	if (isset($_POST['delete-admin'])) {
		$delete_id = $_POST['delete_id'];

		if ($delete_id != $_COOKIE['id']) {
			$stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
			logAdminAction($conn, $_COOKIE['id'], "Admin törlése (ID: $delete_id)");
			$stmt->bind_param("i", $delete_id);
			$stmt->execute();
			$stmt->close();

			sendAdminDeletionEmail($delete_id, $_COOKIE['id'], $conn);
			Message("✅ Admin sikeresen törölve!", "admin.php", "success");
		} else {
			Message("❌ Saját magad nem törölheted!", "admin.php", "error");
		}
	}
	

?>

<!DOCTYPE html>
<html lang="hu">
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="Kajb Anna, Varasdi Vanda">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

		<link rel="stylesheet" href="css/styles.css">
		<title>Admin</title>
	</head>
	<body>
	<header class="bg-dark text-light">
		<div class="logo-container">
			<a href="admin_login.php">
				<img id="logo" src="img/01-logo.jpg" alt="Logo">
			</a>
		</div>
	</header>

	<div class="container mt-5">
		<h2 class="text-center">Kapcsolatfelvételi Üzenetek</h2>
		<?php if (mysqli_num_rows($talalt_uzenet) > 0): ?>
		<table class="table table-dark table-striped mt-4">
			<thead>
				<tr>
					<th>Felhasználó</th>
					<th>Email</th>
					<th>Üzenet</th>
					<th>Dátum</th>
					<th>Válasz</th>
				</tr>
			</thead>
			<tbody>
				<?php while ($uzenet = $talalt_uzenet->fetch_assoc()): ?>
				<tr>
					<td><?= $uzenet['felhasznalo_id'] ? "Felhasználó #" . $uzenet['felhasznalo_id'] : 'Anonim' ?></td>
					<td><?= htmlspecialchars($uzenet['email']) ?></td>
					<td><?= htmlspecialchars($uzenet['uzenet']) ?></td>
					<td><?= $uzenet['datum'] ?></td>
					<td>
						<?= $uzenet['valaszolt'] ? '<span class="badge bg-success">Válaszolva</span>' : '<span class="badge bg-warning text-dark">Nincs válasz</span>' ?>
					</td>
					<td>
						<button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#replyModal<?= md5($uzenet['email'] . $uzenet['datum']) ?>">
							✉️ Válasz
						</button>
					</td>
				</tr>

				<!-- Modal ablak minden üzenethez -->
				<div class="modal fade" id="replyModal<?= md5($uzenet['email'] . $uzenet['datum']) ?>" tabindex="-1" aria-labelledby="replyModalLabel<?= md5($uzenet['email']) ?>" aria-hidden="true">
				  <div class="modal-dialog">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="replyModalLabel<?= md5($uzenet['email']) ?>">Válasz <?= htmlspecialchars($uzenet['email']) ?> részére</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
					  </div>
					  <form method="post">
						  <div class="modal-body">
							<input type="hidden" name="reply_email" value="<?= htmlspecialchars($uzenet['email']) ?>">
							<input type="hidden" name="reply_datum" value="<?= $uzenet['datum'] ?>">
							<div class="mb-3">
								<label for="reply_text" class="form-label">Üzenet szövege</label>
								<textarea name="reply_text" class="form-control" rows="4" placeholder="Írd ide a válaszod..." required></textarea>
							</div>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
							<button type="submit" name="send_reply" class="btn btn-success">Küldés</button>
						  </div>
					  </form>
					</div>
				  </div>
				</div>
			<?php endwhile; ?>
			</tbody>
		</table>
		<?php else: ?>
			<p class="text-center mt-4" style="color: white;" >Nincsenek üzenetek.</p>
		<?php endif; ?>
	</div>
	
			<!-- Adomány statisztikák -->
		<div class="stats-box">
			<h2 class="text-center">📊 Adományozási Statisztikák</h2>

			<?php
			// 1. Összes adomány
			$totalQuery = $conn->query("SELECT SUM(utalt_osszeg) AS osszeg FROM adomanyok");
			$totalRow = $totalQuery->fetch_assoc();
			$osszeg = $totalRow['osszeg'] ?? 0;

			echo "<p><strong>💰 Összesen beérkezett adomány:</strong> " . number_format($osszeg, 2, ',', ' ') . " Ft</p>";

			// 2. Top 5 adományozó
			$topUsers = $conn->query("
				SELECT felhasznalonev, SUM(utalt_osszeg) AS osszeg 
				FROM adomanyok 
				GROUP BY felhasznalonev 
				ORDER BY osszeg DESC 
				LIMIT 5
			");

			echo "<h4>🏆 Legnagyobb adományozók:</h4><ul>";
			while ($user = $topUsers->fetch_assoc()) {
				echo "<li><strong>" . htmlspecialchars($user['felhasznalonev']) . "</strong>: " . number_format($user['osszeg'], 2, ',', ' ') . " Ft</li>";
			}
			echo "</ul>";

			// 3. Top 3 menhely
			$topShelters = $conn->query("
				SELECT kutyamenhely_nev, SUM(utalt_osszeg) AS osszeg 
				FROM adomanyok 
				GROUP BY kutyamenhely_nev 
				ORDER BY osszeg DESC 
				LIMIT 3
			");

			echo "<h4>🐶 Top 3 menhely támogatás szerint:</h4><ol>";
			while ($menhely = $topShelters->fetch_assoc()) {
				echo "<li><strong>" . htmlspecialchars($menhely['kutyamenhely_nev']) . "</strong>: " . number_format($menhely['osszeg'], 2, ',', ' ') . " Ft</li>";
			}
			echo "</ol>";
			?>
		</div>


		<h2 class="admin-list-title">Jelenlegi Adminok</h2>
		<table class="admin-table">
			<tr>
				<th>ID</th>
				<th>Felhasználónév</th>
				<th>Email</th>
				<th>Művelet</th>
			</tr>
			<?php
			$admins = $conn->query("SELECT * FROM admin");
			while ($admin = $admins->fetch_assoc()) {
				echo "<tr>";
				echo "<td>" . $admin['id'] . "</td>";
				echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
				echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
				echo "<td>";
				if ($admin['id'] != $_COOKIE['id']) {
					echo "<form method='post' style='display:inline-block;'>
							<input type='hidden' name='delete_id' value='" . $admin['id'] . "'>
							<button type='submit' name='delete-admin' class='delete-btn'>Törlés</button>
						  </form>";
				} else {
					echo "<span class='self-admin'>Saját fiók</span>";
				}
				echo "</td></tr>";
			}
			?>
		</table>

		<!-- 📜 Naplózás itt -->
		<div class="admin-log-box">
			<h2>📜 Admin műveletek naplója</h2>
			<?php
			$logStmt = $conn->query("SELECT a.username, l.muvelet, l.datum 
									 FROM admin_logs l 
									 JOIN admin a ON l.admin_id = a.id 
									 ORDER BY l.datum DESC");

			if ($logStmt && $logStmt->num_rows > 0) {
				echo '<ul class="admin-log-list">';
				while ($row = $logStmt->fetch_assoc()) {
					echo '<li><strong>' . htmlspecialchars($row['username']) . '</strong> – ' . htmlspecialchars($row['muvelet']) . ' <em>(' . $row['datum'] . ')</em></li>';
				}
				echo '</ul>';
			} else {
				echo '<p>Nincs még rögzített művelet.</p>';
			}
			?>
		</div>

		<!-- 👤 Új admin hozzáadása -->
		<hr>
		<div class="admin-form-box">
			<h2>Új Admin Hozzáadása</h2>
			<form method="POST">
				<label for="username">Felhasználónév:</label>
				<input type="text" name="new_username" required>

				<label for="email">Email:</label>
				<input type="email" name="new_email" required>

				<label for="password1">Jelszó:</label>
				<input type="password" name="new_password1" required>

				<label for="password2">Jelszó újra:</label>
				<input type="password" name="new_password2" required>

				<input type="submit" name="add_admin" value="Admin hozzáadása">
			</form>
		</div>
		
		

	</body>
</html>

<?php $conn->close(); ?>
