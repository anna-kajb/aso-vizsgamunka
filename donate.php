<?php
	session_start();
	$shelterName = isset($_GET['shelter']) ? urldecode($_GET['shelter']) : 'Ismeretlen menhely';

	// 🔁 Dinamikus visszatérési URL
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
	$baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
	$returnUrl = $baseUrl . "/koszonjuk.php";
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Adományozás - <?php echo htmlspecialchars($shelterName); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: sans-serif;
            background-color: #fefefe;
            padding: 30px;
            text-align: center;
        }

        .paypal-form {
            display: inline-block;
            padding: 20px;
            border: 2px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            margin-top: 30px;
        }

        .info {
            margin-top: 20px;
            color: #2c3e50;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <h1>Adományozás a(z) <em><?php echo htmlspecialchars($shelterName); ?></em> részére</h1>

    <p class="info">A PayPal segítségével biztonságosan tudsz adományozni.<br>
    Az adomány összegéből 300 Ft kezelési költséget vonunk le, a többit a menhely kapja.</p>

    <div class="paypal-form">
        <form action="https://www.paypal.com/donate" method="post" target="_top">
			<input type="hidden" name="business" value="kajbanna21@gmail.com" />
			<input type="hidden" name="no_recurring" value="0" />
			<input type="hidden" name="item_name" value="Adomány a(z) <?php echo htmlspecialchars($shelterName); ?> részére" />
			<input type="hidden" name="currency_code" value="HUF" />
			<input type="hidden" name="return" value="http://<?php echo $_SERVER['HTTP_HOST']; ?>/anna/Vizsgamunka/koszonjuk.php?shelter=<?php echo urlencode($shelterName); ?>">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" 
				   border="0" name="submit" title="Donate with PayPal" 
				   alt="PayPal – Donate" />
			<img alt="" border="0" src="https://www.paypal.com/en_HU/i/scr/pixel.gif" width="1" height="1" />
		</form>

    </div>
</body>
</html>
