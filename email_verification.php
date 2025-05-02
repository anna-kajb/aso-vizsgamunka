<?php
require "functions.php"; // Hogy elérjük a függvényeket

if (isset($_POST['email']) && isset($_POST['code'])) {
    $email = $_POST['email'];
    $code = $_POST['code'];

    if (kuldHitelesitoEmail($email, $code)) {
        echo "Hitelesítési e-mail elküldve!";
    } else {
        echo "Hiba történt az e-mail küldésekor.";
    }
}
?>
