<?php

function get_sesions_id($id_evento){
	$array_sesiones = array();
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT s.id FROM evento e, sesion s WHERE e.id = s.id_evento AND e.id = ?;");
	$stmt->bind_param("i", $id_evento);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			array_push($array_sesiones, $row["id"]);
		}	
	}	
	$stmt->close();
	include "sql-close.php";
	return $array_sesiones;
}


?>