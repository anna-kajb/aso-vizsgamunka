<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'vandavarasdi2002@gmail.com'; // SAJÁT EMAIL CÍMED
    $mail->Password = 'pcxllbcmkdujzddd'; // Google által generált App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Biztonsági beállítás
    $mail->Port = 465; // SMTP port (ha 587 nem működik)

    $mail->setFrom('sajat_email@gmail.com', 'Admin');
    $mail->addAddress('tesztcim@gmail.com', 'Teszt Felhasználó');

    $mail->Subject = 'Teszt Email';
    $mail->Body    = 'Ez egy teszt email. Ha ezt megkapod, akkor az SMTP működik!';

    $mail->SMTPDebug = 2; // Hibakereső mód bekapcsolása
    $mail->Debugoutput = 'html';

    $mail->send();
    echo "Email sikeresen elküldve!";
} catch (Exception $e) {
    echo "Hiba történt: " . $mail->ErrorInfo;
}
?>
