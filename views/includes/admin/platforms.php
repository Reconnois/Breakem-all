<?php
	if(isset($listeplatform)){	

		$cat = "<div class='grid-md-10 admin-data-ihm-title align relative grid-centered'>
			<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Image</span></div></div>
			<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Nom</span></div></div>
			<div class='grid-md-4 grid-sm-4 hidden-xs'><div class='admin-data-ihm-elem'><span class='capitalize'>Description</span></div></div>
			<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Status</span></div></div>
		</div>";

		echo $cat;

		if(is_array($listeplatform)){			
			foreach ($listeplatform as $ligne => $platform) {
				//Wrapper				
				echo "<div class='grid-md-10 admin-data-ihm align relative grid-centered'>";

					//Affichage
					echo "<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><div class='admin-data-ihm-elem-img-wrapper membres-img'><img class='admin-img-cover border-round platform-img-up' src='" . $platform->getImg() . "?lastmod=" . date('Y-m-d H:i:s') ."'></div></div></div>";
					echo "<div class='grid-md-4 grid-sm-4 grid-xs-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='capitalize platform-nom-g'>" . $platform->getName() . "</span></div></div>";
					echo "<div class='grid-md-4 grid-sm-4 hidden-xs overflow-hidden'><div class='admin-data-ihm-elem'><span class='platform-description-g'>" . $platform->getDescription() . "</span></div></div>";
					echo "<div class='grid-md-4 grid-sm-4 grid-xs-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='capitalize platform-status-g'><div class='align platform-status-g-ht'>";
						if($platform->getStatus() == 1){
							echo "<img class='icon icon-size-4' src='" . WEBPATH . "/web/img/icon/icon-unlock.png'>";
						}else{
							echo "<img class='icon icon-size-4' src='" . WEBPATH . "/web/img/icon/icon-lock.png'>";
						}
					echo "</div></span></div></div>";
					//Fin Affichage

					//Bouton
					echo "<div class='admin-data-ihm-btn hidden align'>";
						echo "<button class='admin-btn-default btn btn-yellow full admin-btn-modify open-form' type='button'><a>Modifier</a></button>";
/*						echo "<button class='admin-btn-default btn btn-white full admin-btn-delete' type='button'><a>Supprimer</a></button>";
*/					echo "</div>"; 
					//Fin Bouton

					//Formulaire
					echo "<div class='index-modal platforms hidden-fade hidden'>";

						echo "<div class='index-modal-this index-modal-login align'>";
							
							echo "<div class='grid-md-4 inscription_rapide animation fade'>";
								echo "<form class='platform-form admin-form' enctype='multipart/form-data' accept-charset='utf-8'>";
									//Title
									echo "<div class='grid-md-12 form-title-wrapper'>";
										echo "<img class='icon icon-size-4' src='" . WEBPATH . "/web/img/icon/icon-plateforme.png'><span class='form-title'>Plateforme</span>";
									echo "</div>";
									//Image
									echo "<div class='grid-md-12'>";
										echo "<div class='membre-form-img-size m-a hidden-xs'>";																	
											echo "<img class='img-cover platform-img membre-form-img-size' src='" . $platform->getImg() . "' title='Plateforme' alt='Plateforme'>";										
										echo "</div>";
										echo "<div class='text-center admin-input-file'>";								 
											echo "<input type='file' class='platform-image-p' name='profilpic'>";
										echo "</div>";
									echo "</div>";

								    echo "<div class='grid-md-12'>";
								    	echo "<div class='align'>";
									    	echo "<label for='nom'>Nom</label>";
											echo "<input type='text' name='id' class='hidden platform-id-p' value='" . $platform->getId() . "'>";
											echo "<input class='input-default admin-form-input-w platform-nom-p' name='nom' type='text' min='2' max='30' placeholder='Nom compris entre 2 et 30 caractères.' value='" . $platform->getName() . "' required>";
										echo "</div>";

										echo "<div class='align'>";
											echo "<label for='description'>Description</label>";
										    echo "<textarea class='input-default admin-textarea admin-form-input-w platform-description-p' name='description' type='text'>" . $platform->getDescription() . "</textarea>";							    														   
										echo "</div>";

										echo "<div class='relative'><span class='toggleCheck'><label for='status'>Status</label><input class='checkbox input-default platform-status-p admin-checkbox-ajust' id='platform-status-p' name='status' required type='checkbox' ";
											echo ($platform->getStatus()!==NULL  && $platform->getStatus()==-1) ? "checked=checked>" : ">";
										echo "<label class='ajusted-checkbox-label' for='status'>.</label></span></div>";								

									echo "</div>";
									//Submit
									echo "<div class='grid-md-12'>"; 
								    	echo "<button type='submit' class='admin-form-submit platform-submit-form-btn btn btn-pink'><a>Valider</a></button>";
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
		echo "<div class='grid-md-12 no-platform align'><span>Aucune plateforme enregistrée pour le moment.</span></div>";		
	} 
?>		