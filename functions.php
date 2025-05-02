<?php 
	require "config.php"; 

	// 📌 Üzenet
	function Message($text, $redirectUrl = "reg.php", $type = "error") {
		if ($type === "success") {
			$bgColor = "#d4edda";
			$textColor = "#155724";
			$borderColor = "#c3e6cb";
			$title = "✅ Sikeres művelet";
		} else {
			$bgColor = "#f8d7da";
			$textColor = "#721c24";
			$borderColor = "#f5c6cb";
			$title = "⚠️ Hiba történt";
		}

		echo '
		<div style="display: flex; justify-content: center; align-items: center; height: 100vh; font-family: Arial, sans-serif;">
			<div style="background-color: ' . $bgColor . '; color: ' . $textColor . '; border: 1px solid ' . $borderColor . '; padding: 30px; border-radius: 8px; max-width: 400px; text-align: center;">
				<h4>' . $title . '</h4>
				<p>' . htmlspecialchars($text) . '</p>
				<a href="' . $redirectUrl . '" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: ' . $textColor . '; color: white; text-decoration: none; border-radius: 5px;">Vissza</a>
			</div>
		</div>';
		exit();
	}

	// 📌 Hitelesítési kód generálása
	function generateVerificationCode($length = 8) {
		$karakterek = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$code = "";

		for ($i = 0; $i < $length; $i++) {
			$code .= $karakterek[random_int(0, strlen($karakterek) - 1)];
		}
		return $code;
	}

	// 📌 Egyedi hitelesítési kód (users)
	function getUniqueVerificationCode($conn) {
		do {
			$code = generateVerificationCode();
			$stmt = $conn->prepare("SELECT id FROM users WHERE verification_code = ?");
			$stmt->bind_param("s", $code);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();
		} while ($result->num_rows > 0);
		return $code;
	}

	// 📌 Egyedi hitelesítési kód (admin)
	function getUniqueAdminVerificationCode($conn) {
		do {
			$code = generateVerificationCode();
			$stmt = $conn->prepare("SELECT id FROM admin WHERE verification_code = ?");
			$stmt->bind_param("s", $code);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();
		} while ($result->num_rows > 0);
		return $code;
	}

	// 📬 PHPMailer import
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require 'vendor/autoload.php';

	// 📌 Közös email küldő (belső)
	function sendEmailTemplate($to, $username, $code, $link, $subject, $buttonText) {
		$mail = new PHPMailer(true);
		try {
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'vandavarasdi2002@gmail.com';
			$mail->Password = 'pcxllbcmkdujzddd';
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port = 465;
			$mail->CharSet = 'UTF-8';

			$mail->setFrom('vandavarasdi2002@gmail.com', 'Kutyamentő Rendszer');
			$mail->addAddress($to, $username);

			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body = "
				<div style='font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; padding: 20px; border-radius: 10px;'>
					<h2 style='color: #4CAF50;'>Szia, " . htmlspecialchars($username) . "!</h2>
					<p style='font-size: 16px; color: #333;'>Az alábbi kóddal aktiválhatod a fiókodat:</p>
					<div style='font-size: 22px; font-weight: bold; color: #333; padding: 10px; background-color: #fff; border: 1px solid #ddd; display: inline-block; margin: 10px auto;'>"
						. htmlspecialchars($code) . "
					</div>
					<p><a href='$link' style='background-color: #4CAF50; color: white; text-decoration: none; padding: 10px 20px; font-size: 16px; border-radius: 5px;'>$buttonText</a></p>
					<p style='font-size: 14px; color: #777; margin-top: 20px;'>Ha nem te regisztráltál, hagyd figyelmen kívül ezt az emailt.</p>
				</div>";
			$mail->AltBody = "Szia, $username!\n\nKódod: $code\nLink: $link";

			$mail->send();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	function sendVerificationEmail($to, $username, $code) {
		$link = "http://localhost/Vizsgamunka/verify.php?email=" . urlencode($to);
		return sendEmailTemplate($to, $username, $code, $link, "✔ Regisztráció megerősítése", "Aktiválás most");
	}

	function sendAdminVerificationEmail($to, $username, $code) {
		$link = "http://localhost/Vizsgamunka/verify_admin.php?email=" . urlencode($to) . "&code=" . urlencode($code);
		return sendEmailTemplate($to, $username, $code, $link, "✔ Admin fiók megerősítése", "Admin fiók aktiválása");
	}

	function sendPasswordChangeNotification($to, $username) {
		$mail = new PHPMailer(true);
		try {
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'vandavarasdi2002@gmail.com';
			$mail->Password = 'pcxllbcmkdujzddd';
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port = 465;
			$mail->CharSet = 'UTF-8';

			$mail->setFrom('vandavarasdi2002@gmail.com', 'ASO');
			$mail->addAddress($to, $username);
			$mail->isHTML(true);
			$mail->Subject = '🔐 Jelszómódosítás történt';

			$mail->Body = '
				<div style="font-family: Arial, sans-serif; padding: 20px;">
					<h2>Szia ' . htmlspecialchars($username) . '!</h2>
					<p>A jelszavad sikeresen megváltozott. Ha nem te végezted, kérlek <a href="mailto:vandavarasdi2002@gmail.com">értesíts bennünket</a>!</p>
				</div>';
			$mail->AltBody = "Szia $username! Jelszómódosítás történt.";

			$mail->send();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	function sendPasswordResetEmail($to, $username, $code) {
		$mail = new PHPMailer(true);
		try {
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'vandavarasdi2002@gmail.com';
			$mail->Password = 'pcxllbcmkdujzddd';
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port = 465;
			$mail->CharSet = 'UTF-8';

			$mail->setFrom('vandavarasdi2002@gmail.com', 'Kutyamentő Rendszer');
			$mail->addAddress($to, $username);
			$mail->isHTML(true);
			$mail->Subject = '🔐 Jelszó visszaállítás';

			$mail->Body = "
				<div style='font-family: Arial; padding: 20px;'>
					<h2>Szia, $username!</h2>
					<p>Jelszó visszaállítási kérelmet kaptunk.</p>
					<p>Kódod: <strong>$code</strong></p>
					<a href='http://localhost/Vizsgamunka/reset_password.php?email=" . urlencode($to) . "&code=$code'>Kattints ide</a> a jelszó beállításához.
				</div>";
			$mail->AltBody = "Szia $username! Jelszó reset kód: $code";

			$mail->send();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	function sendPasswordChangeConfirmation($to, $username) {
		$mail = new PHPMailer(true);
		try {
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'vandavarasdi2002@gmail.com';
			$mail->Password = 'pcxllbcmkdujzddd';
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port = 465;
			$mail->CharSet = 'UTF-8';

			$mail->setFrom('vandavarasdi2002@gmail.com', 'Jelszóértesítő');
			$mail->addAddress($to, $username);
			$mail->isHTML(true);
			$mail->Subject = '🔐 Sikeres jelszómódosítás';

			$mail->Body = '
				<div style="font-family: Arial; padding: 20px;">
					<h2>Szia, ' . htmlspecialchars($username) . '!</h2>
					<p>A jelszavadat sikeresen módosítottad.</p>
				</div>';
			$mail->AltBody = "Szia $username! A jelszavad módosítva lett.";

			$mail->send();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	// 📌 Admin műveletek naplózása
	function logAdminAction($conn, $admin_id, $muvelet) {
		$stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, muvelet) VALUES (?, ?)");
		$stmt->bind_param("is", $admin_id, $muvelet);
		$stmt->execute();
		$stmt->close();
	}

	function sendAdminDeletionEmail($deletedAdminId, $deleterAdminId, $conn) {
		// Lekérdezés
		$stmt = $conn->prepare("SELECT username, email FROM admin WHERE id = ?");
		$stmt->bind_param("i", $deletedAdminId);
		$stmt->execute();
		$stmt->bind_result($deletedName, $deletedEmail);
		$stmt->fetch();
		$stmt->close();

		$stmt2 = $conn->prepare("SELECT username FROM admin WHERE id = ?");
		$stmt2->bind_param("i", $deleterAdminId);
		$stmt2->execute();
		$stmt2->bind_result($deleterName);
		$stmt2->fetch();
		$stmt2->close();

		$to = "vandavarasdi2002@gmail.com";
		$subject = "⚠️ Admin törlés történt";
		$message = "
			<h2>Admin törlés</h2>
			<p><strong>$deleterName</strong> törölte <strong>$deletedName</strong> fiókját.</p>
			<p>Email: $deletedEmail</p>";

		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8\r\n";
		$headers .= "From: no-reply@aso.hu\r\n";

		mail($to, $subject, $message, $headers);
	}
	

	function sendReplyEmail($to, $replyText) {
		$mail = new PHPMailer(true);
		try {
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'vandavarasdi2002@gmail.com';
			$mail->Password = 'pcxllbcmkdujzddd'; // 🔐 App password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port = 465;
			$mail->CharSet = 'UTF-8';

			$mail->setFrom('vandavarasdi2002@gmail.com', 'ASO Admin');
			$mail->addAddress($to);
			$mail->isHTML(true);
			$mail->Subject = 'ASO Válasz';
			$mail->Body    = nl2br(htmlspecialchars($replyText));
			$mail->AltBody = $replyText;

			$mail->send();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	//Tartalom lekérő függvény
	function getPageContent($conn, $slug) {
		
		$stmt = $conn->prepare("SELECT tartalom FROM pages WHERE slug = ?");
		$stmt->bind_param("s", $slug);
		$stmt->execute();
		$result = $stmt->get_result();
		
    if ($row = $result->fetch_assoc()) {
        return $row['tartalom'];
    }
    return "<p>Tartalom nem található.</p>";
}


?>
