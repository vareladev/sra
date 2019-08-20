<html lang="es">
<head>
<meta http-equiv="Content-Language" content="es" />
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
<script>
window.onload = function() {
	tableToExcel("prueba");
	window.location.replace("assistants.php");
};

function tableToExcel(name) {
	var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) };
    table = document.getElementById('tblassistants');
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML};
	//console.log(uri + base64(format(template, ctx)));
	downloadURI(uri + base64(format(template, ctx)), name) 
  }
  
function downloadURI(uri, name) {
    var link = document.createElement("a");
    link.download = name;
    link.href = uri;
    link.click();
}
  
function exportTableToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel; charset=UTF-8;';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    // Specify file name
    filename = filename?filename+'.xls':'excel_data.xls';
    
    // Create download link element
    downloadLink = document.createElement("a");
    
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {encoding:"UTF-8",type:"text/plain;charset=UTF-8"});
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
    
        // Setting the file name
        downloadLink.download = filename;
        
        //triggering the function
        downloadLink.click();
    }
}

</script>
</head>
<body>
<!-- Pre-loader start -->
<div class="theme-loader">
    <div class="ball-scale">
        <div class='contain'>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
        </div>
    </div>
</div>
<!-- Pre-loader end -->

<?php
	include('sql-calls.php');
	/*
	 * $_POST["evento"] [int]: id en db del evento a evaluar
	 * $_POST["include"] [int]: posibles valores: 1: solo asistentes, 3: todo el personal, 
								2: departamentos que registraron asistencia, 4: por facultad
	 * $_POST["orderby"] [int]: posibles valores: 1: ordenar por carnet, 2: ordenar por codigo de departamento
	 */
	include ('sql-calls-assistants.php');
	$join_type = "";
	$order_by = "";
	$in_condition = false;
	$fac_condition = false;
	$table_title = "";
	
	if ($_POST["include"] == 1){
		$join_type = "INNER";
		$table_title = "Lista incluye solo a las personas que asistieron";
	}
	else if($_POST["include"] == 2){
		$join_type = "LEFT";
		$in_condition = true;
		$table_title = "Lista incluye a todos los empleados de los departamentos que registraron asistencia";
	}
	else if($_POST["include"] == 3){
		$join_type = "LEFT";
		$table_title = "Lista incluye a todo el personal";
	}
	else if($_POST["include"] == 4){
		$join_type = "LEFT";
		$fac_condition = true;
		switch($_POST["facultades"]){
			case 0:	$table_title = "Lista incluye a las facultades de Ciencias Sociales y Humanidades, Ciencias Económicas y Empresariales e Ingeniería y Arquítectura";
					break;
			case 1:	$table_title = "Lista incluye al personal de la facultad de Ciencias Sociales y Humanidades";
					break;
			case 2:	$table_title = "Lista incluye al personal de la facultad de Ciencias económicas y Empresariales";
					break;
			case 3:	$table_title = "Lista incluye al personal de la facultad de Ingeniería y Arquítectura";
					break;
			
		}
	}
	
	if ($_POST["orderby"] == 1){
		$order_by = "carnet";
	}
	else{
		$order_by = "codigo_unidad";
	}
	
	//creando query
	$query = create_query_assistants($_POST["evento"], $join_type,$order_by, $in_condition, $fac_condition );
	
	//creando tabla
	echo '<table border="1" id="tblassistants">';
		get_assist_data(str_replace("<br>", " ", $query), true, true, $table_title);
		if(isset($_POST['alumnos'])){
			//echo "incluir alumnos<br>";
			$query = create_query_assistants_students($_POST["evento"],1);
			get_assist_data(str_replace("<br>", " ", $query), false, false, "Listado de alumnos que asistieron al evento");
		}
		if(isset($_POST['otros'])){
			//echo "incluir otros empleados<br>";
			$query = create_query_assistants_students($_POST["evento"],2);
			get_assist_data(str_replace("<br>", " ", $query), false, false, "Listado de empleados que no definidos en la base de datos pero que asistieron al evento");
		}
	echo '</table>';
	echo "<input type=\"button\" onclick=\"tableToExcel('asistencia')\" value=\"Export to Excel\">";	
	
	
	
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
						echo '<td>'.$row["sesion".$sesioncount].'</td>';
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
			$query_inner = $query_inner."SEC_TO_TIME(SUM(history_extended.sesion".($i+1).")) AS sesion".($i+1);
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


</body>
</html>
