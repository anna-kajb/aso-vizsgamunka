<?php

	$conn = new mysqli("localhost", "root", "", "vizsgamunka2");
	
	if($conn->connect_error){
		die("Nem sikerült kapcsolódni a szerverrel!".$conn->connenct_error);
	}


?>