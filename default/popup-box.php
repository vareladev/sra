<?php
	function popup_adduser($id_evento,$id_sesion){
		$popup_carnet ="";
		if(isset($_POST["carnet"])){
			$popup_carnet = $_POST["carnet"];
			
			$is_student = false;
			if(preg_match('/[0-9]{8}/', $popup_carnet)){ 
				$is_student = true;
			}
			
			$title = ($is_student ? "Registrar estudiante:" : "Agregar asistente");
			$msj = ($is_student ? "Ingrese nombre del estudiante" : "El usuario con el carnet '".$popup_carnet."' no ha sido encontrado, ¿Desea agregarlo a la base de datos y marcar su asistencia?");
			echo '
				<div id="modal-wrapper" class="modal">
				  <form class="modal-content animate" action="popup-register.php" method="post">
					   
					<div class="imgcontainer">
					  <span onclick="document.getElementById(\'modal-wrapper\').style.display=\'none\'" class="close" title="Close PopUp">&times;</span>
					  <h2 style="text-align:center">'.$title.'</h2>
					  <div style="margin: 20px;">'.$msj.' <br>
						<b>Verifique que el carnet este bien escrito</b>
					  </div>
					</div>

					<div class="container">
					  <input style="display:none;" readonly  type="text" name="popup-evento" value="'.$id_evento.'">
					  <input style="display:none;" readonly  type="text" name="popup-sesion" value="'.$id_sesion.'">
					  <input readonly class="popup-input" type="text" placeholder="Carnet" name="popup-carnet" id="popup-carnet" value="'.$popup_carnet.'">
					  <input class="popup-input" type="text" placeholder="nombre" name="popup-nombre" id="popup-nombre" required>        
					  <button class="popup-button" type="submit">Guardar asistente</button>
					</div>
					
				  </form>
				</div>
				';
		}
	}
	//style="display:none;" readonly 
	
?>