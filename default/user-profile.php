<?php
echo '
	<li class="user-profile header-notification">
		<div class="dropdown-primary dropdown">
			<div class="dropdown-toggle" data-toggle="dropdown">
				<span>'.$_SESSION["nick"].'</span>
				<i class="feather icon-chevron-down"></i>
			</div>
			<ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
				<li>
					<a href="session-close.php">
						<i class="feather icon-log-out"></i> Cerrar sesión
					</a>
				</li>
			</ul>

		</div>
	</li>';

?>