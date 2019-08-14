<?php
	if(isset($_POST["evento"]) && isset($_POST["sesion"])){
		$event = $_POST["evento"];
		$sesion_array =  json_decode($_POST["sesion"]);
		
		include('sql-calls.php');
		insert_event($event, $sesion_array);
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include('meta.php'); ?>

	<link rel="stylesheet" type="text/css" href="table/vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="table/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="table/vendor/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="table/vendor/select2/select2.min.css">
	<link rel="stylesheet" type="text/css" href="table/vendor/perfect-scrollbar/perfect-scrollbar.css">
	<link rel="stylesheet" type="text/css" href="table/css/main.css">
	<!--===============================================================================================-->
	<!-- feather Awesome -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\feather\css\feather.css">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\themify-icons\themify-icons.css">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\icofont\css\icofont.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
	
<script>
var lista_sesiones = [];

function addSesionToEventList() {
	if(checkSesionFormat()){
		//obteniendo valor de la fecha
		var sesion = document.getElementById("sesion").value;
		//ingresando fecha a arreglo global:
		//var date = new Date(sesion);
		lista_sesiones.push(sesion.replace(/T/g, " ")+":00"); 
		//agregando elementos a la tabla
		insertArrayIntoTable();
	}
	else{
		alert("Error: verifique que la fecha y hora ingresada para la sesión sea válida.");
	}
}

function deleteElementById(index){
	//eliminando elemento
	lista_sesiones.splice(index, 1);
	//agregando elementos a la tabla
	insertArrayIntoTable();
}

function insertArrayIntoTable(){
  var sesion_index = 0;
  document.getElementById("container").innerHTML = "";
  // DRAW THE HTML TABLE
  var perrow = 1, // 3 items per row
      html = "<table><tr>";

  // Loop through array and add table cells
  for (var i=0; i<lista_sesiones.length; i++) {
    html += "<td>" + "<h5 class=\"text-center\">Sesión "+ (sesion_index+1) +": "+ lista_sesiones[i] + "</h5>" + "</td>";
	html += "<td>" + "<a href=\"#\"><i onclick=\"deleteElementById("+ sesion_index +")\" class=\"feather icon-trash-2\"></i></a>" + "</td>";
    // Break into next row
    var next = i+1;
    if (next%perrow==0 && next!=lista_sesiones.length) {
      html += "</tr><tr>";
    }
	sesion_index += 1;
  }
  html += "</tr></table>";
  
  alert(html);

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
	var sesion = document.getElementById("sesion").value;
	if (sesion.length > 0) {
		return true;
	}
	else{
		return false;
	}
}

function checkForm(){
	var evento_text = document.getElementById("evento").value;
	if(lista_sesiones.length > 0 && evento_text.length > 0){
		var json_sesiones = JSON.stringify(lista_sesiones);
		//alert(json_sesiones);
		post('/sra/default/new-event.php', {evento: evento_text, sesion: json_sesiones});
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

<body class="fix-menu">
    <section class="login-block">
        <!-- Container-fluid starts -->
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Authentication card start -->
                            <div class="auth-box card">
                                <div class="">
									<div class="mycard">
										<div class="row">
											<div class="col-md-12">
												<h3 class="text-center">Crear nuevo evento</h3>
											</div>
										</div>
										<br>
										<div class="form-group form-primary">
											<input type="text" id="evento" name="evento" class="form-control myinput" placeholder="Titulo del evento">
										</div>
										<div class="row">
											<div class="col-md-12">
												<h3 class="text-center">Agregar sesiones</h3>
											</div>
										</div>
										<br>
										<div class="form-group form-primary">
											<input type="datetime-local" id="sesion" name="sesion" class="form-control myinput">
										</div>
										<div class="form-group form-primary">
												<button class="btn btn-primary btn-md btn-block waves-effect waves-light text-center m-b-20 myinput" onclick="addSesionToEventList();">Agregar sesión</button>
										</div>
										<div id="container"></div>
										<br>
										<div class="form-group form-primary">
												<button type="submit" class="btn btn-primary btn-md btn-block waves-effect waves-light text-center m-b-20 myinput" onclick="checkForm();">Guardar evento</button>
										</div>
										<br>
                                    </div>
                                </div>
				
                <!-- end of col-sm-12 -->
            </div>
            <!-- end of row -->
        </div>
        <!-- end of container-fluid -->
    </section>
    <!-- Warning Section Starts -->
    <!-- Older IE warning message -->
    <!--[if lt IE 10]>
<div class="ie-warning">
    <h1>Warning!!</h1>
    <p>You are using an outdated version of Internet Explorer, please upgrade <br/>to any of the following web browsers to access this website.</p>
    <div class="iew-container">
        <ul class="iew-download">
            <li>
                <a href="http://www.google.com/chrome/">
                    <img src="../files/assets/images/browser/chrome.png" alt="Chrome">
                    <div>Chrome</div>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/en-US/firefox/new/">
                    <img src="../files/assets/images/browser/firefox.png" alt="Firefox">
                    <div>Firefox</div>
                </a>
            </li>
            <li>
                <a href="http://www.opera.com">
                    <img src="../files/assets/images/browser/opera.png" alt="Opera">
                    <div>Opera</div>
                </a>
            </li>
            <li>
                <a href="https://www.apple.com/safari/">
                    <img src="../files/assets/images/browser/safari.png" alt="Safari">
                    <div>Safari</div>
                </a>
            </li>
            <li>
                <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    <img src="../files/assets/images/browser/ie.png" alt="">
                    <div>IE (9 & above)</div>
                </a>
            </li>
        </ul>
    </div>
    <p>Sorry for the inconvenience!</p>
</div>
<![endif]-->
    <!-- Warning Section Ends -->
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
    <!-- i18next.min.js -->
    <script type="text/javascript" src="..\files\bower_components\i18next\js\i18next.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\i18next-xhr-backend\js\i18nextXHRBackend.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\i18next-browser-languagedetector\js\i18nextBrowserLanguageDetector.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-i18next\js\jquery-i18next.min.js"></script>
    <script type="text/javascript" src="..\files\assets\js\common-pages.js"></script>
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
