<?php 
	if(isset($inscription)){
		?>
		<div style="margin-top: 200px;">
			Votre compte a été activé !
		</div>
		<?php 
	}
	else if(isset($pwdlost)){
		?>
		<div style="margin-top: 200px;">
			<table id="login-form">
				<form  method="POST" action="">
					<tr>
					<td><p><span id="mdp">Mot de passe perdu ?</span></p><p><label for="email">E-mail :</label><input class="input-default" type="email" id="email" name="email" required></p></td>
					</tr>
					<td>
				    <button class="btn btn-pink" type="submit" value="Envoyer"><a class="lienb">Envoyer</a></button>
					</td>
				</form>
			</table>
		</div>
		<?php 
	}
	else if(isset($envoi)){
		?>
			<div style="margin-top: 200px;">
				Un email a été envoyé à votre adresse, pensez à regarder aussi dans vos messages indésirables !
			</div>
		<?php 
	}
	else if(isset($recoverpwd)){
		?>
			<div style="margin-top:200px;">
				<form id="login-form" method="POST" action="">			    
				    <p>Nouveau mot de passe: <input type="password" name="new_password" required></p>
					<p>Resaisir le nouveau mot de passe: <input type="password" name="new_password_check" required>
				    <input type="submit" value="Envoyer">
				</form>
			</div>

		<?php
	}
?>