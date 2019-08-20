<?php
	session_start();
	if(!isset($_SESSION["nick"])){
		header('location: login.php');
	}
	include('sql-calls.php');
	
	//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	$is_table_ready = false;
	if(isset($_POST["evento"]) && isset($_POST["include"]) && isset($_POST["orderby"])){
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
		echo '<table style="display:none;" border="1" id="tblassistants">';
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
		$is_table_ready = true;
	}
	//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
?>
<head>
	<?php include 'meta.php';?>
	<meta http-equiv="Content-Language" content="es" />
	<meta charset="UTF-8" />
	<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="table/vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="table/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="table/vendor/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="table/vendor/select2/select2.min.css">
	<link rel="stylesheet" type="text/css" href="table/vendor/perfect-scrollbar/perfect-scrollbar.css">
	<link rel="stylesheet" type="text/css" href="table/css/main.css">	
    <!-- Google font--><link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,800" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\themify-icons\themify-icons.css">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\icofont\css\icofont.css">
    <!-- feather Awesome -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\feather\css\feather.css">
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\datatables.net-bs4\css\dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\pages\data-table\css\buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\datatables.net-responsive-bs4\css\responsive.bootstrap4.min.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\jquery.mCustomScrollbar.css">
<script>
window.onload = function () {
    document.getElementById('facultades').disabled=true;
	<?php
		if($is_table_ready){
			$filename = get_event_name($_POST["evento"]);
			$filename = str_replace (" ","_",$filename);
			$filename = "Asistencia_".$filename;
			echo 'tableToExcel("'.$filename.'");';
		}
	?>
}

function enableSchoolList() { 
    var radioValue = document.querySelector('input[name="include"]:checked').value;
    if(radioValue=="4"){
		document.getElementById("facultades").removeAttribute('disabled');
    }
    else{
        document.getElementById("facultades").setAttribute('disabled', true);
    }
}

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
<div id="pcoded" class="pcoded">
    <div class="pcoded-overlay-box"></div>
    <div class="pcoded-container navbar-wrapper">

        <nav class="navbar header-navbar pcoded-header">
			<div class="navbar-wrapper">
				<div class="navbar-container container-fluid">
					<ul class="nav-left">
						<li class="header-search">
							<div class="main-search morphsearch-search">
								<h4><span>Sistema de registro de asistencia a eventos</span></h4>
							</div>
						</li>
					</ul>
					<ul class="nav-right">
						<?php include 'user-profile.php';?>
					</ul>
				</div>
			</div>
		</nav>
			
        <!-- Sidebar inner chat end-->
        <div class="pcoded-main-container">
            <div class="pcoded-wrapper">
                
                <nav class="pcoded-navbar">
                        <?php include 'menu.php';?>
                    </nav>
					
                <div class="pcoded-content">
                    <div class="pcoded-inner-content">
                        <!-- Main-body start -->
                        <div class="main-body">
                            <div class="page-wrapper">

                                <!-- Page-body start -->
                                <div class="page-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <!-- Zero config.table start -->
                                            <div class="card">
                                                <div class="card-block">

												
													<!-- Create event card start -->
													<div class="">
														<div class="mycard">
															<form action="assistants.php" method="post">
															<!-- block select event start-->
															<div class="row">
																<div class="col-md-12">
																	<h5 class="">Seleccione el evento:</h5>
																</div>
															</div>
															<br>
															<div class="form-group form-primary">
																<div class="form-group form-primary">
																	<select id="evento" name="evento" class="form-control" style="height:40px; font-size: 17px;">
																		<?php
																			get_events();
																		?>
																	<select>
																</div>
															</div>
															<!-- block select event end-->
															<!-- block select persons start-->
															<div class="row">
																<div class="col-md-12">
																	<h5 class="">Seleccione quienes serán incluidos en la lista:</h5>
																</div>
															</div>
															<br>
															<div class="form-group form-primary">
																<div class="myinputr">
																  <input type="radio" id="includeonly" name="include" value="1" required onChange="enableSchoolList()">
																  <label for="includeonly">Solo asistentes</label>
																</div>
																<div class="myinputr">
																  <input type="radio" id="includedeptos" name="include" value="2" onChange="enableSchoolList()">
																  <label for="includedeptos">Solo a los departamentos con asistentes</label>
																</div>
																<div class="myinputr">
																  <input type="radio" id="includeall" name="include" value="3" onChange="enableSchoolList()">
																  <label for="includeall">Todos los empleados</label>
																</div>
																<div class="myinputr">
																  <input type="radio" id="includeFacul" name="include" value="4" onChange="enableSchoolList()">
																  <label for="includeFacul">Por facultad:</label>
																</div>
																<div class="form-group form-primary myinputr">
																	<select id="facultades" name="facultades" class="form-control" style="height:40px; font-size: 17px;">
																		  <option value="0">Todas las facultades</option>
																		  <option value="1">Ciencias sociales y humanidades</option>
																		  <option value="2">Ciencias económicas y empresariales</option>
																		  <option value="3">Ingeniería y arquítectura</option>
																	</select>
																</div>
															</div>
															<!-- block select persons end-->
															<!-- block select order start-->
															<div class="row">
																<div class="col-md-12">
																	<h5 class="">Seleccione el criterio de orden de la lista:</h5>
																</div>
															</div>
															<br>
															<div class="form-group form-primary">
																<div class="myinputr">
																  <input type="radio" id="orderbyid" name="orderby" value="1" required>
																  <label for="orderbyid">Ordenar lista según carnet</label>
																</div>
																<div class="myinputr">
																  <input type="radio" id="orderbydepto" name="orderby" value="2">
																  <label for="orderbydepto">Ordernar lista por departamentos</label>
																</div>
															</div>
															<!-- block select order end-->
															<!-- block select others start-->
															<div class="row">
																<div class="col-md-12">
																	<h5 class="">Otros criterios:</h5>
																</div>
															</div>
															<br>
															<div class="form-group form-primary">
																<div class="myinputr">
																  <input type="checkbox" name="alumnos" value="alumnos">
																  <label for="alumnos">Incluir alumos que asistieron al evento</label>
																</div>
																<div class="myinputr">
																  <input type="checkbox" name="otros" value="otros">
																  <label for="otros">Incluir personal no registrado en la BD</label>
																</div>
															</div>
															<!-- block select others end-->
															<div class="form-group form-primary" style="text-align:center;">
																<input class="btn btn-primary" type="submit" value="Descargar lista">
															</div>
															</form>
														</div>
													</div>
													<!-- Create event card end -->
            
												
												
                                                </div>
                                            </div>
                                            <!-- Zero config.table end -->
                                        </div>
                                    </div>
                                </div>
                                <!-- Page-body end -->
                            </div>
                        </div>
                        <!-- Main-body end -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Required Jquery -->
<script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
<script type="text/javascript" src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
<script type="text/javascript" src="..\files\bower_components\popper.js\js\popper.min.js"></script>
<script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.min.js"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="..\files\bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>
<!-- modernizr js -->
<script type="text/javascript" src="..\files\bower_components\modernizr\js\modernizr.js"></script>
<script type="text/javascript" src="..\files\bower_components\modernizr\js\css-scrollbars.js"></script>

<!-- data-table js -->
<script src="..\files\bower_components\datatables.net\js\jquery.dataTables.min.js"></script>
<script src="..\files\bower_components\datatables.net-buttons\js\dataTables.buttons.min.js"></script>
<script src="..\files\assets\pages\data-table\js\jszip.min.js"></script>
<script src="..\files\assets\pages\data-table\js\pdfmake.min.js"></script>
<script src="..\files\assets\pages\data-table\js\vfs_fonts.js"></script>
<script src="..\files\bower_components\datatables.net-buttons\js\buttons.print.min.js"></script>
<script src="..\files\bower_components\datatables.net-buttons\js\buttons.html5.min.js"></script>
<script src="..\files\bower_components\datatables.net-bs4\js\dataTables.bootstrap4.min.js"></script>
<script src="..\files\bower_components\datatables.net-responsive\js\dataTables.responsive.min.js"></script>
<script src="..\files\bower_components\datatables.net-responsive-bs4\js\responsive.bootstrap4.min.js"></script>
<!-- i18next.min.js -->
<script type="text/javascript" src="..\files\bower_components\i18next\js\i18next.min.js"></script>
<script type="text/javascript" src="..\files\bower_components\i18next-xhr-backend\js\i18nextXHRBackend.min.js"></script>
<script type="text/javascript" src="..\files\bower_components\i18next-browser-languagedetector\js\i18nextBrowserLanguageDetector.min.js"></script>
<script type="text/javascript" src="..\files\bower_components\jquery-i18next\js\jquery-i18next.min.js"></script>
<!-- Custom js -->
<script src="..\files\assets\pages\data-table\js\data-table-custom.js"></script>

<script src="..\files\assets\js\pcoded.min.js"></script>
<script src="..\files\assets\js\vartical-layout.min.js"></script>
<script src="..\files\assets\js\jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="..\files\assets\js\script.js"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-23581568-13');
</script>
</body>

</html>
