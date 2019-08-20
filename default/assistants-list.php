<html lang="es">
<head>
<meta http-equiv="Content-Language" content="es" />
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
<script>
window.onload = function() {
	//tableToExcel("prueba");
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

<?php
	include('sql-calls.php');
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
	//echo $query;
	
	//creando tabla
	echo '<table border="1" id="tblassistants">';
	get_assist_data(str_replace("<br>", " ", $query), true, true, $table_title);
	if(isset($_POST['alumnos'])){ //incluir alumnos
		$query = create_query_assistants_students($_POST["evento"],1);
		get_assist_data(str_replace("<br>", " ", $query), false, false, "Listado de alumnos que asistieron al evento");
	}
	if(isset($_POST['otros'])){ //incluir otros empleados
		$query = create_query_assistants_students($_POST["evento"],2);
		get_assist_data(str_replace("<br>", " ", $query), false, false, "Listado de empleados que no definidos en la base de datos pero que asistieron al evento");
	}
	echo '</table>';
	echo "<input type=\"button\" onclick=\"tableToExcel('asistencia')\" value=\"Export to Excel\">";	
	
?>


</body>
</html>
