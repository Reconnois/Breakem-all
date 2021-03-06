<?php
	if(isset($listeteam)){	
	
		$cat = "<div class='grid-md-10 admin-data-ihm-title align relative grid-centered'>
			<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Image</span></div></div>
			<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Nom</span></div></div>
			<div class='grid-md-4 grid-sm-4 hidden-xs'><div class='admin-data-ihm-elem'><span class='capitalize'>Slogan</span></div></div>
			<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Status</span></div></div>
			
		</div>";

		echo $cat;

		if(is_array($listeteam)){			
			foreach ($listeteam as $ligne => $team) {
				//Wrapper				
				echo "<div class='grid-md-10 admin-data-ihm align relative grid-centered'>";

					//Affichage
					//Je met un timestamp après l'image pour ne pas la sauvegarder dans le cache si jamais on la modifie (fichier avec le meme nom) voir : http://stackoverflow.com/questions/728616/disable-cache-for-some-images
					echo "<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><div class='admin-data-ihm-elem-img-wrapper membres-img'><img class='admin-img-cover border-round team-img-up' src='" . $team->getImg() . "?lastmod=" . date('Y-m-d H:i:s') ."'></div></div></div>";
					echo "<div class='grid-md-4 grid-sm-4 grid-xs-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='capitalize team-name-g'>" . $team->getName() . "</span></div></div>";
					echo "<div class='grid-md-4 grid-sm-4 hidden-xs overflow-hidden'><div class='admin-data-ihm-elem'><span class='capitalize team-slogan-g'>" . $team->getSlogan() . "</span></div></div>";
					echo "<div class='grid-md-4 grid-sm-4 grid-xs-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='capitalize team-status-g'><div class='align team-status-g-ht'>";
						if($team->getStatus() == 1){
							echo "<img class='icon icon-size-4' src='" . WEBPATH . "/web/img/icon/icon-unlock.png'>";
						}else{
							echo "<img class='icon icon-size-4' src='" . WEBPATH . "/web/img/icon/icon-lock.png'>";
						}
					echo "</div></span></div></div>";
					//Fin Affichage

					//Bouton
					echo "<div class='admin-data-ihm-btn hidden align'>";
						echo "<button class='admin-btn-default btn btn-yellow full admin-btn-modify open-form' type='button'><a>Modifier</a></button>";
					echo "</div>"; 
					//Fin Bouton

					//Formulaire
					echo "<div class='index-modal teams hidden-fade hidden'>";

						echo "<div class='index-modal-this index-modal-login align'>";
							
							echo "<div class='grid-md-4 inscription_rapide animation fade'>";
								echo "<form class='team-form admin-form' enctype='multipart/form-data' accept-charset='utf-8'>";
									//Title
									echo "<div class='grid-md-12 form-title-wrapper'>";
										echo "<img class='icon icon-size-4' src='" . WEBPATH . "/web/img/icon/icon-team.png'><span class='form-title'>Team</span>";
									echo "</div>";
									//Image
									echo "<div class='grid-md-12'>";
										echo "<div class='membre-form-img-size m-a hidden-xs'>";																	
											echo "<img class='img-cover team-img membre-form-img-size' src='" . $team->getImg() . "' title='Image de profil' alt='Image de profil'>";										
										echo "</div>";
										echo "<div class='text-center admin-input-file'>";								 
											echo "<input type='file' class='team-image-p' name='profilpic'>";
										echo "</div>";
									echo "</div>";

									echo "<div class='grid-md-12'>";

									    echo "<div class='grid-md-12'>";
									    echo "<div class='align'>";
									    	echo "<label for='name'>Nom</label>";
											echo "<input type='text' name='id' class='hidden team-id-p' value='" . $team->getId() . "'>";
											echo "<input class='input-default admin-form-input-w team-name-p' min='2' max='30' placeholder='Nom compris entre 2 et 30 caractères.'  name='name' type='text' value='" . $team->getName() . "' required>";
										echo "</div>";

										echo "<div class='align'>";
											echo "<label for='slogan'>Slogan</label>";
											echo "<input class='input-default admin-form-input-w team-slogan-p' name='slogan' type='text' value='" . $team->getSlogan() . "'>";
										echo "</div>";

										echo "<div class='align'>";
											echo "<label for='description'>Description</label>";
											echo "<input class='input-default admin-form-input-w team-description-p' name='description' type='text' value='" . $team->getDescription() . "'>";
										echo "</div>";

										echo "<div class='relative'><span class='toggleCheck'><label for='status'>Désactiver</label><input class='checkbox input-default admin-checkbox-ajust team-status-p' id='team-status-p' name='status' required type='checkbox' ";
											echo ($team->getStatus()!==NULL  && $team->getStatus()==-1) ? "checked=checked>" : ">";
										echo "<label class='ajusted-checkbox-label' for='status'>.</label></span></div>";								
										echo "</div>";
									echo "</div>";
									//Submit
									echo "<div class='grid-md-12'>"; 
								    	echo "<button type='submit' class='admin-form-submit team-submit-form-btn btn btn-pink'><a>Valider</a></button>";
						  			echo "</div>";
						  		echo "</form>";
						  	echo "</div>";
						echo "</div>";
					echo "</div>";
					//Fin Formulaire
				echo "</div>";
				//Fin Wrapper
			}					
		}
	}else{
		echo "<div class='grid-md-12 no-platform align'><span>Aucune team enregistrée pour le moment.</span></div>";		
	} 
?>		