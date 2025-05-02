<?php 

	require "config.php";

	$keresett = $_GET['keresett'];
	
	$lekerdezes = "SELECT * FROM users WHERE email='$keresett'";
	$talalt_felhasznalo = $conn->query($lekerdezes);
	
	if(mysqli_num_rows($talalt_felhasznalo) == 0){
		echo "<a style='color: green;'>Megfelelő email cím!</a>";
	}
	else{
		echo "<a style='color: red;'>Ez az email cím már foglalt!</a>";
	}

?>