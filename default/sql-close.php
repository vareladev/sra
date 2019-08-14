<?php
	//mysql_close($link);
	$res = mysqli_close ( $con );
	if(!($res)){
		echo "error al cerrar la conexion a la base de datos...";
	}
?>