<?php
	$server_url = "localhost";
	$usuario = "srauser";
	$password = "xvXbe9MADrXrAlzD";
	$bd = "sra";
	
	$con =  new mysqli($server_url, $usuario, $password, $bd);
	if (mysqli_connect_errno()) {
		die('Hubo un error: ' . mysqli_connect_error());
	}
?>
