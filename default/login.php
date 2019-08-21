<?php
	session_start();
	if(isset($_SESSION["nick"])){
		header('location: dashboard.php');
	}

	$nick = $pass = "";
	$wronglogin = false;
	if(isset($_POST["nick"]) && isset($_POST["pass"])){ 
		$nick = $_POST["nick"];
		$pass = md5($_POST["pass"]);
		
		include('sql-calls.php');
		if(check_login($nick, $pass)){ //check login
			session_start();
			$_SESSION["nick"] = $nick;
			header('location: dashboard.php') or die();
		}
		else{
			$wronglogin = true;
		}
	}
?>
<!DOCTYPE html>
<html lang="es">
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
<?php
	if($wronglogin){
		echo "alert('Error: usuario y/o contraseña inválidos, verifique sus credenciales.');";
	}
?>
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
										<h3 class="text-center">Sistema de registro de asistencia</h3>
									</div>
								</div>
								<br>
								<form action="login.php" method="post">
									<div class="form-group form-primary">
										<input type="text" id="nick" name="nick" class="form-control myinput" required placeholder="Usuario">
									</div>
									<div class="form-group form-primary">
										<input type="password" id="pass" name="pass" class="form-control myinput" required placeholder="Contraseña">
									</div>										
									<div class="form-group form-primary">
										<button type="submit" class="btn btn-primary btn-md btn-block waves-effect waves-light text-center m-b-20 myinput">Iniciar sesión</button>
									</div>
								</form>
								<br>
							</div>
						</div>
					</div>
					<!-- end of form -->
                </div>
                <!-- end of col-sm-12 -->
            </div>
            <!-- end of row -->
        </div>
        <!-- end of container-fluid -->
    </section>
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
