<?php 
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: reg.php");
    exit();
}

// Felhasználói adatok lekérése
$user_id = $_SESSION['user_id'];

$sql = "SELECT username, email, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Hiba: A felhasználó nem található az adatbázisban.";
    exit();
}

// Profilkép feltöltése
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $target_dir = "uploads/";
    // Hozzuk létre az uploads mappát ha nem létezik
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $fileName = basename($_FILES["profile_pic"]["name"]);
    $target_file = $target_dir . $fileName;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Engedélyezett típusok
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            // Adatbázis frissítés
            $sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $fileName, $_SESSION['user_id']);
            $stmt->execute();
            header("Location: myuser.php"); // Újratöltés friss adatokkal
            exit();
        } else {
            echo "⚠️ Hiba történt a feltöltés során.";
        }
    } else {
        echo "⚠️ Csak JPG, JPEG, PNG vagy GIF formátum engedélyezett.";
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Kajb Anna, Varasdi Vanda">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
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
        <li><a href="search.php">Keresés</a></li>
        <li><a href="shelters.php">Kutyamenhelyek</a></li>
        <li><a href="myuser.php">Profilom</a></li>
        <li><a href="connection.php">Kapcsolat</a></li>
        <li><a href="logout.php">Kijelentkezés</a></li>
    </ul>
</header>

<div class="myuser-container">
    <div class="myuser-card">
        <h2 class="myuser-title">Profilom</h2>

        <!-- Profilkép -->
        <div class="myuser-image">
            <img src="uploads/<?php echo $user['profile_pic'] ?: 'default.png'; ?>" alt="Profilkép">
        </div>

        <!-- Profilkép feltöltés -->
        <form action="myuser.php" method="post" enctype="multipart/form-data">
            <input type="file" name="profile_pic" class="myuser-upload-input" required>
            <button type="submit" class="myuser-btn-primary mt-2">Profilkép feltöltése</button>
        </form>

        <!-- Felhasználói adatok -->
        <div class="myuser-info">
            <p><strong>Név:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <a href="change_password.php" class="myuser-btn-secondary mt-2">Jelszó módosítása</a>
        </div>
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