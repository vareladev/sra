<?php
	$id_evento = isset($_POST["popup-evento"]) ? $_POST["popup-evento"] : "--";
	$id_sesion = isset($_POST["popup-sesion"]) ? $_POST["popup-sesion"] : "--";
	$carnet = isset($_POST["popup-carnet"]) ? $_POST["popup-carnet"] : "--";
	$nombre = isset($_POST["popup-nombre"]) ? $_POST["popup-nombre"] : "--";
	
	//echo "nombre: ".$nombre." carnet: ".$carnet." evento: ".$id_evento." sesion: ".$id_sesion;
	include('sql-calls.php');
	save_person($carnet, $nombre, $id_evento, $id_sesion);
	//redireccionando
	header('location: register.php?evento='.$id_evento.'&sesion='.$id_sesion);
	
?>