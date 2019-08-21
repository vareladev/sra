<?php
function hide_event($id_evento){
	include 'sql-open.php';
	$stmt = $con->prepare("UPDATE `evento` SET `isvisible`= 0 WHERE `id` = ?;");
	$stmt->bind_param("i", $id_evento);
	$stmt->execute();
	$stmt->close();
	include "sql-close.php";
}

function is_event_held($id_evento){
	$response = false;
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT DATE(s.hora_ini) as dia FROM evento e, sesion s WHERE e.id = s.id_evento AND e.id = ? ORDER BY s.hora_ini ASC LIMIT 1;");
	$stmt->bind_param("i", $id_evento);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			$event_date = strtotime($row["dia"]);
			$today = strtotime(date('Y-m-d'));
			if($event_date > $today){
				$response = true;
			}
		}	
	}	
	$stmt->close();
	include "sql-close.php";
	return $response;
}


function get_event_detail(){
	$correl = 1;
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT e.id, e.nombre, COUNT(s.id) as sesiones FROM evento e, sesion s WHERE e.id = s.id_evento AND e.isvisible = 1 GROUP BY e.id, e.nombre ORDER BY e.id DESC;");
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			$array_date = get_event_datetime($row["id"]);
			echo '
				<tr>
					<td>'.$correl.'</td>
					<td>'.$row["nombre"].'</td>
					<td><center>'.$array_date[0].'</center></td>
					<td><center>'.$array_date[1].'</center></td>
					<td><center>'.$row["sesiones"].'</center></td>
					<td><center><a href="event-list.php?ide='.$row["id"].'"><i class="feather icon-trash-2"></a></center></td>
				</tr>
			';
			$correl ++;
		}	
	}	
	$stmt->close();
	include "sql-close.php";
}


function get_event_datetime($id_evento){
	$array_date = array();
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT DATE(s.hora_ini) as dia, TIME(s.hora_ini) as hora FROM evento e, sesion s WHERE e.id = s.id_evento AND e.id = ? ORDER BY s.hora_ini ASC LIMIT 1;");
	$stmt->bind_param("i", $id_evento);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			array_push($array_date, $row["dia"]);
			array_push($array_date, $row["hora"]);
		}	
	}	
	$stmt->close();
	include "sql-close.php";
	return $array_date;
}

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

function get_depto_assist($id_evento){
	$array_deptos = array();
	include 'sql-open.php';
	$stmt = $con->prepare("SELECT e.id_unidad 
							FROM empleado e, asistencia a 
							WHERE e.carnet = a.carnet AND a.id_evento =? 
							GROUP BY e.id_unidad;");
	$stmt->bind_param("i", $id_evento);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			array_push($array_deptos, $row["id_unidad"]);
		}	
	}	
	$stmt->close();
	include "sql-close.php";
	return $array_deptos;	
}

//ejecutando query y creando tabla
	function get_assist_data($consulta, $set_title, $set_date, $table_subtitle){
		include 'sql-open.php';
		$con->set_charset("utf8");
		$result = $con->query($consulta);

		if($result->num_rows > 0){
			$fieldcount=mysqli_num_fields($result);
			
			//Definiendo tema de la tabla
			if ($set_title){
				echo '<tr><td colspan="'.$fieldcount.'"><b style="font-size: 20px;">Lista de asistencia a evento: '.get_event_name($_POST["evento"]).'</b></tr>';
			}
			if ($set_date){
				echo '<tr><td colspan="'.$fieldcount.'"><b>Fecha del evento: '.get_evento_date($_POST["evento"]).'</b></tr>';
			}
			echo '<tr><td colspan="'.$fieldcount.'"><b>'.$table_subtitle.'</b></td></tr>';
			echo '<tr><td colspan="'.$fieldcount.'">&nbsp;&nbsp;</td></tr>';
			//definiendo cabecera de la tabla
			echo '<tr>';
				echo '<td><center><b>Carnet</b></center></td>';
				echo '<td><center><b>Nombre</b></center></td>';
				echo '<td><center><b>Código unidad</b></center></td>';
				echo '<td><center><b>Unidad</b></center></td>';
				$fieldcountaux = $fieldcount;
				$sesioncount = 1;
				while($fieldcountaux > 4){
					echo '<td><center><b>Sesion '.$sesioncount.'</b></center></td>';
					$sesioncount ++;
					$fieldcountaux --;
				}
			echo '</tr>';
			//definiendo datos
			while($row = $result->fetch_assoc()) {
				echo '<tr>';
					echo '<td>'.$row["carnet"].'</td>';
					echo '<td>'.$row["nombre"].'</td>';
					echo '<td>'.$row["codigo_unidad"].'</td>';
					echo '<td>'.$row["unidad"].'</td>';
					$fieldcountaux = $fieldcount;
					$sesioncount = 1;
					while($fieldcountaux > 4){
						echo '<td><center>'.$row["sesion".$sesioncount].'</center></td>';
						$sesioncount ++;
						$fieldcountaux --;
					}
				echo '</tr>';
			}
			echo '<tr><td colspan="'.$fieldcount.'">&nbsp;&nbsp;</td></tr>';
		}	
		include "sql-close.php";
			
	}	
		
	/*
	 * $id_evento [int]: id en db del evento a evaluar
	 * $join_type [string]: posibles valores: "inner", "left"
	 * $orderby [string]: posibles valores: "carnet", "codigo_unidad"
	 * $include_depto_emp [boolean]: Indica si se incluyen todos los empleados de los departamentos que marcan asistencia
	 */
	function create_query_assistants($id_evento, $join_type, $orderby, $include_depto_emp, $by_fac){		
		$array_sesiones = get_sesions_id($id_evento);
		$array_deptos = array();
		
		if($include_depto_emp){
			$array_deptos = get_depto_assist($id_evento);
		}
	
		$query_inner = "SELECT history_extended.carnet, history_extended.nombre, history_extended.codigo_unidad, history_extended.unidad,<br>";
		
		$cantidad_sesiones = count($array_sesiones);
		for($i = 0; $i < $cantidad_sesiones; $i++){
			$query_inner = $query_inner."IF(SUM(history_extended.sesion".($i+1).") = 0, '--', SEC_TO_TIME(SUM(history_extended.sesion".($i+1)."))) AS sesion".($i+1);
			if ($i+1 < $cantidad_sesiones)
				$query_inner = $query_inner.",<br>";
		}
		$query_inner = $query_inner."<br>FROM (<br>SELECT e.carnet, CONCAT(e.nombre, ' ', e.apellido) AS nombre, u.id AS codigo_unidad, u.unidad, <br>";
		for($i = 0; $i < $cantidad_sesiones; $i++){
			$query_inner = $query_inner."case when a.id_sesion = ".$array_sesiones[$i]." THEN TIME_TO_SEC(TIME(a.hora_asistencia)) ELSE 0 END AS sesion".($i+1);
			if ($i+1 < $cantidad_sesiones)
				$query_inner = $query_inner.",<br>";
		}
		$query_inner = $query_inner."<br>FROM unidad u ".$join_type." JOIN empleado e<br>
										ON u.id = e.id_unidad<br>
										".$join_type." JOIN asistencia a<br>
										ON e.carnet = a.carnet<br>
										WHERE (a.id_evento = ".$id_evento." OR a.id_evento IS NULL) <br>";
		//si se incluye personal de la unidad si tiene asistencia
		if($include_depto_emp){
			$query_inner = $query_inner."AND u.id IN (";
			for($i = 0; $i < count($array_deptos); $i++){
				$query_inner = $query_inner.$array_deptos[$i];
				if (($i+1) < count($array_deptos))
					$query_inner = $query_inner.", ";
			}
			$query_inner = $query_inner.")";
		}
		//si se incluye filtrar por facultad
		if($by_fac){
			$facultad_id=$_POST["facultades"];
			if($facultad_id == 0){ //todas las facultades
				$query_inner = $query_inner."AND u.id_facultad IN (1,2,3) ";
			}
			else{
				$query_inner = $query_inner."AND u.id_facultad = ".$facultad_id." ";
			}
		}
		
		$query_inner = $query_inner.") AS history_extended <br>
										GROUP BY history_extended.carnet, history_extended.nombre, history_extended.codigo_unidad, history_extended.unidad<br>
										ORDER BY history_extended.".$orderby." ASC;";	
		return $query_inner;
	}
	
	/*
	 * $id_evento [int]: id en db del evento a evaluar
	 * $assist_regex [int]: posibles valores: 1, 2. define el usuario a buscar
	 */
	function create_query_assistants_students($id_evento,$assist_regex){	
		$cond_regex = "";
		if($assist_regex == 1){
			$cond_regex = "[0-9]{8}";
		}
		else{
			$cond_regex = "[A-Z][A-Z][0-9]{5}";
		}

		$array_sesiones = get_sesions_id($id_evento);
	
		$query_inner = "SELECT history_extended.carnet, history_extended.nombre, history_extended.codigo_unidad, history_extended.unidad,<br>";
		
		$cantidad_sesiones = count($array_sesiones);
		for($i = 0; $i < $cantidad_sesiones; $i++){
			//$query_inner = $query_inner."SEC_TO_TIME(SUM(history_extended.sesion".($i+1).")) AS sesion".($i+1);
			$query_inner = $query_inner."IF(SUM(history_extended.sesion".($i+1).") = 0, '--', SEC_TO_TIME(SUM(history_extended.sesion".($i+1)."))) AS sesion".($i+1);
			if ($i+1 < $cantidad_sesiones)
				$query_inner = $query_inner.",<br>";
		}
		$query_inner = $query_inner."<br>FROM (<br>SELECT e.carnet, e.nombre, '-' AS codigo_unidad, '-' AS unidad, <br>";
		for($i = 0; $i < $cantidad_sesiones; $i++){
			$query_inner = $query_inner."case when a.id_sesion = ".$array_sesiones[$i]." THEN TIME_TO_SEC(TIME(a.hora_asistencia)) ELSE 0 END AS sesion".($i+1);
			if ($i+1 < $cantidad_sesiones)
				$query_inner = $query_inner.",<br>";
		}
		$query_inner = $query_inner."<br>FROM otros_asistentes e INNER JOIN otros_asistencia a<br>
										ON e.carnet = a.carnet<br>
										WHERE a.id_evento = ".$id_evento." <br>
											AND e.carnet REGEXP '".$cond_regex."'<br>";
		
		$query_inner = $query_inner.") AS history_extended <br>
										GROUP BY history_extended.carnet, history_extended.nombre, history_extended.codigo_unidad, history_extended.unidad<br>
										ORDER BY history_extended.carnet ASC;";	
		return $query_inner;
	}

?>