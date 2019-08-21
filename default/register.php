<?php
	include('sql-calls.php');
	
	//variables globales necesarias
	$id_evento = -1;
	$id_sesion = -1;
	$carnet = "";
	$employee = false;
	$student = false;
	
	
	if(isset($_POST["evento"]) && !isset($_POST["carnet"]) && !isset($_POST["sesion"])){ //first loop
		$id_evento = $_POST["evento"];
		$id_sesion = 1;
	}
	else if(isset($_GET["sesion"]) && isset($_GET["evento"])){ //when register is a student or unknow employee
		$id_evento = $_GET["evento"];
		$id_sesion = $_GET["sesion"];
	}
	else if(isset($_POST["carnet"]) && isset($_POST["sesion"]) && isset($_POST["evento"])){ //rest of loops
		$id_evento = $_POST["evento"];
		$id_sesion = $_POST["sesion"];
		$carnet = strtoupper($_POST["carnet"]);
		
		if(preg_match('/[0-9]{8}/', $carnet)){ //check if user is student
			$student = true;
		}
		elseif(check_emp($carnet)){ //check if employee id exists in db
			save_assistance(strtoupper($carnet), $id_evento, $id_sesion);
		}
		else{
			$employee = true;
		}
	}
	else if(!isset($_POST["evento"])){
			header('location: index.php');
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
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\themify-icons\themify-icons.css">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\icofont\css\icofont.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
	
	<link rel="stylesheet" type="text/css" href="css/popup.css">
<script>
window.onload = check_user_onload;

function check_user_onload() {
	<?php
		//user doesn't exists....
		/*if (strcmp($emp_name, "") == 0){
			echo "var carnet = \"".$carnet."\";";
			echo 'confirmNewUser(carnet);';
		}*/
		if ($employee){
			echo 'openPopUp();';
		}
		if ($student){
			echo 'openPopUp();';
		}
	?>
}

function confirmNewUser(carnet){
	var answer = false;
	if (confirm("Error: El carnet ingresado no corresponde con ningún usuario de la base de datos. ¿Desea ingresar el registro de todos modos?")) {
		answer = true;
	}
	if (answer){
		var person = prompt("Ingrese nombre:");
		if (person != null && person != "") {
			alert("carnet: "+carnet+", nombre: "+person);
		} 
	}
}

function validate() {
	var input_event = document.getElementById("evento").value;
	var input_text = document.getElementById("carnet").value;
	var selected_sesion = document.getElementById("sesion").value;
	
	var regex_emp = new RegExp("[A-Z]{2}[0-9]{5}");
	var regex_est = new RegExp("[0-9]{8}");
	
	if (regex_emp.test(input_text.toUpperCase()) || regex_est.test(input_text)) {
		post('register.php', {carnet: input_text, sesion: selected_sesion, evento: input_event});
	} else {
		alert("Carnet con formato inválido");
	}
}

/**
 * sends a request to the specified url from a form. this will change the window location.
 * @param {string} path the path to send the post request to
 * @param {object} params the paramiters to add to the url
 * @param {string} [method=post] the method to use on the form
 */

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

//popup
function openPopUp(){
	document.getElementById('modal-wrapper').style.display='block';
}
</script>
</head>

<body class="fix-menu">
	<?php
		include ('popup-box.php');
		popup_adduser($id_evento,$id_sesion);
	?>
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
												<h3 class="text-center">Asistencia <?php echo get_event_name($id_evento);?></h3>
											</div>
										</div>
										<br>
										<div class="form-group form-primary">
											<input style="display: none;" type="text" id="evento" name="evento" value="<?php echo $id_evento;?>">
											<input type="text" id="carnet" name="carnet" class="form-control myinput" required autofocus placeholder="Carnet" onKeyDown="if(event.keyCode==13) validate();">
										</div>
										<div class="form-group form-primary">
											<select id="sesion" name="sesion" class="form-control" style="height:50px; font-size: 20px;">
												<?php
													get_sesions($id_evento, $id_sesion);
												?>
											<select>
										</div>											
										<div class="form-group form-primary">
												<button type="submit" class="btn btn-primary btn-md btn-block waves-effect waves-light text-center m-b-20 myinput" onclick="validate()">Registrar</button>
										</div>
										
                                    </div>
                                </div>
								<div class="row">
									<div class="col-md-12">
										<h3 class="text-center">Últimos registros</h3>
									</div>
								</div>
								<div style="padding-left: 15%; padding-right:15%; padding-bottom:30px;">
									<div class="limiter">
										<div class="wrap-table100">
											<div class="table100">
												<table>
													<thead>
														<tr class="table100-head">
															<th class="column1">Hora</th>
															<th class="column2">Carnet</th>
															<th class="column3">Nombre</th>
														</tr>
													</thead>
													<tbody>
														<?php
															tail4_registers($id_evento, $id_sesion);
														?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								<div>
                            </div>
                        <!-- end of form -->
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
  
// If user clicks anywhere outside of the modal, Modal will close
var modal = document.getElementById('modal-wrapper');
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
</body>

</html>
