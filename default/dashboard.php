<?php
	session_start();
	if(!isset($_SESSION["nick"])){
		header('location: login.php');
	}
	
	
	$new_event_ok;
	if(isset($_POST["evento"]) && isset($_POST["sesion"]) && isset($_POST["snames"])){
		$event = $_POST["evento"];
		$sesion_array =  json_decode($_POST["sesion"]);
		$sname_array =  json_decode($_POST["snames"]);
		
		include('sql-calls.php');
		$new_event_ok = insert_event($event, $sesion_array,$sname_array);
	}
?>
<head>
	<?php include 'meta.php';?>
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
	
	<link rel="stylesheet" type="text/css" href="css/my.css">	
	
<script>
var lista_sesiones = [];
var lista_sname = [];

function addSesionToEventList() {
	if(checkSesionFormat()){
		var sname = document.getElementById("sname").value;
		var sesion = document.getElementById("sesion").value;
		//ingresando fecha a arreglo global:
		lista_sesiones.push(sesion.replace(/T/g, " ")+":00"); 
		lista_sname.push(sname);
		//agregando elementos a la tabla
		insertArrayIntoTable();
	}
	else{
		alert("Error: verifique que el nombre, la fecha y hora ingresada para la sesión sea válida.");
	}
}

function deleteElementById(index){
	//eliminando elemento
	lista_sesiones.splice(index, 1);
	lista_sname.splice(index, 1);
	//agregando elementos a la tabla
	insertArrayIntoTable();
}

function insertArrayIntoTable(){
  var sesion_index = 1;
  document.getElementById("container").innerHTML = "";
  // DRAW THE HTML TABLE
  var perrow = 1, // 3 items per row
      html = "<table><tr>";

  // Loop through array and add table cells
  for (var i=0; i<lista_sesiones.length; i++) {
    html += "<td>" + "<h5 class=\"text-center\">"+ sesion_index + ": " + lista_sname[i] +" - "+ lista_sesiones[i] + "</h5>" + "</td>";
	html += "<td>" + "<a href=\"#\"><i onclick=\"deleteElementById("+ sesion_index +")\" class=\"feather icon-trash-2\"></i></a>" + "</td>";
    // Break into next row
    var next = i+1;
    if (next%perrow==0 && next!=lista_sesiones.length) {
      html += "</tr><tr>";
    }
	sesion_index += 1;
  }
  html += "</tr></table>";

  // ATTACH HTML TO CONTAINER
  document.getElementById("container").innerHTML = html;
}

function deleteAllTableRows(){
	var myTable = document.getElementById("tablelist");
	var rowCount = myTable.rows.length;
	for (var x=rowCount-1; x>=0; x--) {
	   myTable.deleteRow(x);
	}
}

function checkSesionFormat(){
	var sname = document.getElementById("sname").value;
	var sesion = document.getElementById("sesion").value;
	
	if (sesion.length > 0 && sname.length > 0) {
		return true;
	}
	else{
		return false;
	}
}

function checkForm(){
	var evento_text = document.getElementById("evento").value;
	
	if(lista_sesiones.length > 0 && evento_text.length){
		var json_sesiones = JSON.stringify(lista_sesiones);
		var json_snames = JSON.stringify(lista_sname);
		//alert(json_sesiones);
		post('dashboard.php', {evento: evento_text, sesion: json_sesiones, snames: json_snames});
	}
	else{
		alert("Error debe ingresar un nombre de evento y al menos una sesión.");
	}
}

function post(path, params, method='post') {

  // The rest of this code assumes you are not using a library.
  // It can be made less wordy if you use one.
  const form = document.createElement('form');
  form.method = method;
  form.action = path;

  for (const key in params) {
    if (params.hasOwnProperty(key)) {
      const hiddenField = document.createElement('input');
      hiddenField.type = 'hidden';
      hiddenField.name = key;
      hiddenField.value = params[key];

      form.appendChild(hiddenField);
    }
  }

  document.body.appendChild(form);
  form.submit();
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
														<div class="mycard-admin">
															<?php
																if(isset($new_event_ok)){
																	if($new_event_ok){
																		echo '
																			<div class="alert alert-success icons-alert">
																				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
																					<i class="icofont icofont-close-line-circled"></i>
																				</button>
																				<p>El evento ha sido registrado con éxito. Ahora puede registrar asistentes en el evento.</p>
																			</div>																
																		';	
																	}
																	else{
																		echo '
																			<div class="alert alert-danger icons-alert">
																				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
																					<i class="icofont icofont-close-line-circled"></i>
																				</button>
																				<p>El evento ha sido registrado con éxito. Ahora puede registrar asistentes en el evento.</p>
																			</div>																
																		';	
																	}
																}
																
															?>
															<div class="row">
																<div class="col-md-12">
																	<h4 class="text-center">Crear nuevo evento</h3>
																</div>
															</div>
															<br>
															<div class="form-group form-primary">
																<input type="text" id="evento" name="evento" class="form-control myinput" placeholder="Titulo del evento">
															</div>
															<div class="row">
																<div class="col-md-12">
																	<h4 class="text-center">Agregar sesiones</h3>
																</div>
															</div>
															<br>
															<div class="row">
																<div class="col-md-12" >
																	<h5 class="text-center" style="padding-bottom: 10px;">Nombre de la sesión:</h5>
																</div>
															</div>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group form-primary">
																		<input type="text" id="sname" name="sname" class="form-control myinput">
																	</div>
																</div>
															</div>
															<div class="row">
																<div class="col-md-12">
																	<h5 class="text-center" style="padding-bottom: 10px;">Fecha y hora:</h5>
																</div>
															</div>	
															<div class="row">
																<div class="col-md-9">
																	<div class="form-group form-primary">
																		<input type="datetime-local" id="sesion" name="sesion" class="form-control myinput">
																	</div>
																</div>
																<div class="col-md-3" >
																	<button class="btn btn-primary mybutton" onclick="addSesionToEventList();">Agregar</button>
																</div>
															</div>															
															
															
															<div id="container"></div>
															<br>
															<div class="form-group form-primary" style="text-align:center;">
																	<button class="btn btn-success" onclick="checkForm();">Guardar evento</button>
															</div>
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
