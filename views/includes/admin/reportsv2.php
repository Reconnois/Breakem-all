<?php
	if(isset($listesignalement)){	

		echo "<div class='admin-data-ihm-title align relative'>";
			echo "<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Emetteur</span></div></div>";
			echo "<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Accusé</span></div></div>";
			echo "<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Motif</span></div></div>";
			echo "<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Description</span></div></div>";
			echo "<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Date</span></div></div>";
		echo "</div>";

		if(is_array($listesignalement)){			
			foreach ($listesignalement as $ligne => $signalement) {
				//Wrapper				
				echo "<div class='admin-data-ihm align relative'>";

					//Affichage
					echo "<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize platform-emetteur-g'>" . $signalement->getPseudo_indic_user() . "</span></div></div>";
					echo "<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='platform-accuse-g'>" . $signalement->getPseudo_signaled_user() . "</span></div></div>";
					echo "<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize platform-subject-g'>" . $signalement->getSubject() . "</span></div></div>";
					echo "<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='platform-description-g'>" . $signalement->getDescription() . "</span></div></div>";
					echo "<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize platform-date-g'>" . $signalement->getDate() . "</span></div></div>";	
					//Fin Affichage

					//Boutton
					echo "<div class='admin-data-ihm-btn hidden align'>";
						echo "<button class='admin-btn-default btn btn-yellow full admin-btn-modify open-form' type='button'><a>Modifier</a></button>";
						echo "<button class='admin-btn-default btn btn-white full admin-btn-delete' type='button'><a>Supprimer</a></button>";
					echo "</div>"; 
					//Fin Boutton

					//Formulaire
					echo "<div class='index-modal platforms hidden-fade hidden'>";

						echo "<div class='index-modal-this index-modal-login align'>";
							
							echo "<div id='login-form' class='grid-md-3 inscription_rapide animation fade'>";
								echo "<form class='platform-form' enctype='multipart/form-data' accept-charset='utf-8'>";
									echo "<input type='text' name='id' class='hidden platform-id-p' value='" . $signalement->getId() . "'>";
								    echo "<label for='email'>Nom :</label>";
								    echo "<input class='input-default admin-form-input-w platform-nom-p' name='nom' type='text' value='" . $signalement->getName() . "'>";
								    echo "<label for='email'>Description :</label>";
								    echo "<textarea class='input-default admin-form-input-w platform-description-p' name='description' type='text'>" . $signalement->getDescription() . "</textarea>";							    						
								    echo "<div class='admin-avatar-wrapper m-a'>";																	
										echo "<img class='admin-avatar img-cover platform-img' src='" . $signalement->getImg() . "' title='Image de profil' alt='Image de profil'>";										
									echo "</div>";
									echo "<div class='text-center admin-input-file'>";								 
									echo "<input type='file' class='platform-image-p' name='profilpic'>";
									echo "</div>";
								    echo "<button type='button' class='platform-submit-form-btn btn btn-pink'><a>Valider</a></button>";
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
		echo "<div class='grid-md-12 no-platform align'><span>Aucun signalement enregistré pour le moment.</span></div>";		
	} 
?>		