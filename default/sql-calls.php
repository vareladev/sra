<?php
/**
 * Function used in index.php, gets an event list from db.
 */
function get_events(){
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT `id`,`nombre` FROM `evento` ORDER BY `id` ASC LIMIT 5;");
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
		  echo '<option value="'.$row["id"].'">'.$row["nombre"].'</option>';
		}	
	}	
	$stmt->close();
	include "sql-close.php";	
}

/**
 * Function used in index.php, gets an event name from db.
 * @param {int} event id
 */
function get_event_name($id_evento){
	$evento = "";
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT `nombre` FROM `evento` WHERE `id` = ?;");
	$stmt->bind_param("i", $id_evento);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			$evento = $row["nombre"];
		}	
	}	
	$stmt->close();
	include "sql-close.php";
	return $evento;
}

/**
 * Function used in register.php, gets a sesion list from db.
 * @param {int} event id
 * @param {int} sesion id, useful to define a selected item from sesion list.
 */
function get_sesions($id_evento, $id_sesion){
	$cont = 1;
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT `id`,TIME(`hora_ini`) AS hora_ini, TIME(`hora_fin`) AS hora_fin FROM `sesion` WHERE `id_evento` = ? ORDER BY `id` ASC;");
	$stmt->bind_param("i", $id_evento);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			if ($id_sesion == $row["id"])
				echo '<option value="'.$row["id"].'" selected>Sesión '.$cont.': '.substr_replace($row["hora_ini"] ,"", -3).'</option> ';
			else
				echo '<option value="'.$row["id"].'">Sesión '.$cont.': '.substr_replace($row["hora_ini"] ,"", -3).'</option> ';
			$cont +=1;
		}	
	}	
	$stmt->close();
	include "sql-close.php";	
}

/**
 * Function used in index.php, saves a new assistance, considering if previously a user has been registered
 * in the given sesion, this will avoid duplicate assistance.
 */
function save_assistance($carnet, $id_evento, $id_sesion){
	if (check_assistance($carnet, $id_evento, $id_sesion)){
		include 'sql-open.php';
		$stmt = $con->prepare("INSERT INTO `asistencia`(`carnet`, `id_evento`, `id_sesion`) VALUES (?,?,?);");
		$stmt->bind_param("sii", $carnet, $id_evento, $id_sesion);
		$stmt->execute();
		$stmt->close();
		include "sql-close.php";
	}
}

/**
 * Function used in index.php, checks if some user has been registered in the given sesion
 */
function check_assistance($carnet, $id_evento, $id_sesion){
	$result = true;
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT `id` FROM `asistencia`
							WHERE `carnet` = ?
								AND `id_evento` = ?
								AND `id_sesion` = ?;");
	$stmt->bind_param("sii", $carnet, $id_evento, $id_sesion);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		$result = false;
	}
	$stmt->close();
	include "sql-close.php";
	return $result;
}

/**
 * Check if employee exists in the db.
 * @param {string} employee's id
 */
function tail4_registers($id_evento, $id_sesion){
	$band = 0;
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT * 
							FROM (
							(SELECT e.carnet, CONCAT(e.nombre, ' ' ,e.apellido) as nombre, a.hora_asistencia
							FROM `asistencia` a, `empleado` e 
							WHERE a.carnet = e.carnet
								AND `id_evento` = ? 
								AND `id_sesion` = ?
							ORDER  BY `hora_asistencia` DESC LIMIT 4)
							UNION
							(SELECT e.carnet, e.nombre, a.hora_asistencia
							FROM `otros_asistencia` a, `otros_asistentes` e 
							WHERE a.carnet = e.carnet
								AND `id_evento` = ? 
								AND `id_sesion` = ?
							ORDER  BY `hora_asistencia` DESC LIMIT 4)) as last_regs
							ORDER BY `hora_asistencia` DESC LIMIT 4;");
	$stmt->bind_param("iiii", $id_evento, $id_sesion,$id_evento, $id_sesion);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			echo '
			<tr>
				<td class="column1">'.$row["carnet"].'</td>
				<td class="column2">'.$row["nombre"].'</td>
				<td class="column3">'.$row["hora_asistencia"].'</td>
			</tr>
			';
			$band += 1;
		}
	}
	for ($i = 4; $i > $band; $i--){
		echo '
		<tr>
			<td class="column1"> </td>
			<td class="column2"> </td>
			<td class="column3"> </td>
		</tr>
		';
	}	
	$stmt->close();
	include "sql-close.php";
}

/**
 * Check if employee exists in the db.
 * @param {string} employee's id
 */
function check_emp($carnet){	
	$response = false;
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT `nombre`, `apellido` FROM `empleado` WHERE `carnet` = ?;");
	$stmt->bind_param("s", $carnet);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		$response = true;	
	}	
	$stmt->close();
	include "sql-close.php";
	return $response;
}

/**
 * this functions are useful in new-event.php
 */
 
/**
 * Function used in new-event.php, insert a new event register into db, including all 
 * sesions relationed to this event
 */
function insert_event($event, $sesion_array){
	$event_ok = false;
	
	include 'sql-open.php';
	$stmt = $con->prepare("INSERT INTO `evento`(`nombre`) VALUES (?);");
	$stmt->bind_param("s", $event);
		
	if ($stmt->execute()) { 
		$event_ok = true;
	}
	
	$last_event_id = $stmt->insert_id; //getting last id
	$stmt->close();
	include "sql-close.php";
	insert_sesion($sesion_array,$last_event_id);
	return $event_ok;
}

/**
 * Function used in new-event.php, insert a new event register into db, including all 
 * sesions relationed to this event
 */
function insert_sesion($sesion_array,$last_event_id){
	include 'sql-open.php';
	for ($i = 0; $i < sizeof($sesion_array) ; $i++){
		$fecha = $sesion_array[$i];
		$stmt = $con->prepare("INSERT INTO `sesion`(`hora_ini`,`id_evento`) VALUES (?,?);");
		$stmt->bind_param("si",$fecha,$last_event_id);
		$stmt->execute();
	}
	$stmt->close();
	include "sql-close.php";
}


/**
 * this functions are useful in popup-register.php
 */
 
/**
 * Function used in index.php, saves a new assistance, considering if previously a user has been registered
 * in the given sesion, this will avoid duplicate assistance.
 */
function save_person($carnet, $nombre, $id_evento, $id_sesion){
	
	if (check_assistance($carnet, $id_evento, $id_sesion)){ //check if user exists
		include 'sql-open.php';
		$stmt = $con->prepare("INSERT INTO `otros_asistentes`(`carnet`, `nombre`) VALUES (?,?);");
		$stmt->bind_param("ss", $carnet, $nombre);
		$stmt->execute();
		$stmt->close();
		include "sql-close.php";
	}
	
	if(check_person_assistance($carnet, $id_evento, $id_sesion)){ 
		include 'sql-open.php';
		$stmt = $con->prepare("INSERT INTO `otros_asistencia`(`carnet`, `id_evento`, `id_sesion`) VALUES (?,?,?);");
		$stmt->bind_param("sii", $carnet, $id_evento, $id_sesion);
		$stmt->execute();
		$stmt->close();
		include "sql-close.php";
	}
}

/**
 * checks if some user has been registered in temp table
 * this validation is for users who aren't in the employee's table
 */
function check_person($carnet){
	$result = true;
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT `carnet` FROM `otros_asistentes` WHERE `carnet` = ?;");
	$stmt->bind_param("s", $carnet);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		$result = false;
	}
	$stmt->close();
	include "sql-close.php";
	return $result;
}

/**
 * Checks if some user has been registered in the given sesion, 
 * this validation is for users who aren't in the employee's table
 */
function check_person_assistance($carnet, $id_evento, $id_sesion){
	$result = true;
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT `id` FROM `otros_asistencia` WHERE `carnet` = ? AND `id_evento` = ? AND `id_sesion` = ?;");
	$stmt->bind_param("sii", $carnet, $id_evento, $id_sesion);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		$result = false;
	}
	$stmt->close();
	include "sql-close.php";
	return $result;
}


/**
 * this functions are useful in login.php
 */

 /**
 * Check if user exists
 */
 function check_login($nick, $pass){
	$result = true;
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT `id` FROM `admin` WHERE `nick` = ? AND `pass` = ?;");
	$stmt->bind_param("ss", $nick, $pass);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows == 0){
		$result = false;
	}
	$stmt->close();
	include "sql-close.php";
	return $result;
}

?>