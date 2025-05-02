<?php 

	// Töröljük a bejelentkezésnél elmentett adatokat
	setcookie("id", 0, time(), "/");
	
	header("Location: index.php");

?>