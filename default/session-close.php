<?php
	session_start();
	if(isset($_SESSION["nick"])){
		unset ($_SESSION["nick"]);
	}
	header('location: index.php');
?>