<?php
session_start();
require 'config.php';

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    echo "Hiba: Nem vagy bejelentkezve!";
    header("Location: reg.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $target_dir = "uploads/";
    $file_name = basename($_FILES["profile_pic"]["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Engedélyezett formátumok
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    
    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            // Frissítjük a profilképet az adatbázisban
            $sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $file_name, $user_id);
            $stmt->execute();
            
            // Sikeres feltöltés esetén visszairányítás
            header("Location: myuser.php?success=1");
            exit();
        } else {
            echo "Hiba történt a feltöltés során.";
        }
    } else {
        echo "Csak JPG, PNG vagy GIF fájl engedélyezett.";
    }
} else {
    echo "Hiba: Nincs kiválasztott fájl.";
}
