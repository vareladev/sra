<?php
	include ('sql-calls-assistants.php');
	
	get_assistant(9, "INNER","carnet", false);
	/*
	 * $id_evento [int]: id en db del evento a evaluar
	 * $join_type [string]: posibles valores: "inner", "left"
	 * $orderby [string]: posibles valores: "carnet", "codigo_unidad"
	 * $include_depto_emp [boolean]: Indica si se incluyen todos los empleados de los departamentos que marcan asistencia
	 */
	function get_assistant($id_evento, $join_type, $orderby, $include_depto_emp){		
		$array_sesiones = get_sesions_id($id_evento);
		
		if($include_depto_emp){
			
		}
	
		$query_inner = "SELECT history_extended.carnet, history_extended.nombre, history_extended.codigo_unidad, history_extended.unidad,<br>";
		
		$cantidad_sesiones = count($array_sesiones);
		for($i = 0; $i < $cantidad_sesiones; $i++){
			$query_inner = $query_inner."SEC_TO_TIME(SUM(history_extended.sesion".($i+1).")) AS sesion".($i+1);
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
										WHERE a.id_evento = ".$id_evento." OR a.id_evento IS NULL) AS history_extended <br>
										GROUP BY history_extended.carnet, history_extended.nombre, history_extended.codigo_unidad, history_extended.unidad<br>
										ORDER BY history_extended.".$orderby." ASC";
		echo $query_inner;
	}
	
	
?>