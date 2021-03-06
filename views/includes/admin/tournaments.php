<?php
	if(isset($listetournament)){	

		$cat = "<div class='grid-md-10 admin-data-ihm-title align relative grid-centered'>
			<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Nom</span></div></div>		
			<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Début</span></div></div>
			<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Fin</span></div></div>
			<div class='grid-md-4 grid-sm-4 grid-xs-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Status</span></div></div>
		</div>";

		echo $cat;

		if(is_array($listetournament)){			
			foreach ($listetournament as $ligne => $tournament) {
				//Wrapper				
				echo "<div class='grid-md-10 admin-data-ihm align relative grid-centered'>";

					//Affichage
					echo "<div class='grid-md-4 grid-sm-4 grid-xs-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='tournament-name-g'>" . $tournament->getName() . "</span></div></div>";				
					echo "<div class='grid-md-4 grid-sm-4 grid-xs-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='capitalize tournament-datedebut-g'>" . date('d-m-Y', $tournament->getStartDate()) . "</span></div></div>";
					echo "<div class='grid-md-4 grid-sm-4 grid-xs-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='tournament-datefin-g'>" . date('d-m-Y', $tournament->getEndDate()) . "</span></div></div>";
					echo "<div class='grid-md-4 grid-sm-4 grid-xs-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='capitalize tournament-status-g'><div class='align tournament-status-g-ht'>";
						if($tournament->getStatus() == 1){
							echo "<img class='icon icon-size-4' src='" . WEBPATH . "/web/img/icon/icon-unlock.png'>";
						}else{
							echo "<img class='icon icon-size-4' src='" . WEBPATH . "/web/img/icon/icon-lock.png'>";
						}
					echo "</div></span></div></div>";
					//Fin 

					//Bouton
					echo "<div class='admin-data-ihm-btn hidden align'>";
						echo "<button class='admin-btn-default btn btn-yellow full admin-btn-modify open-form' type='button'><a>Modifier</a></button>";
					echo "</div>"; 
					//Fin Bouton

					//Formulaire
					echo "<div class='index-modal tournaments hidden-fade hidden'>";

						echo "<div class='index-modal-this index-modal-login align'>";
							
							echo "<div class='grid-md-4 inscription_rapide animation fade'>";
								echo "<form class='tournament-form admin-form' enctype='multipart/form-data' accept-charset='utf-8'>";
									//Title
									echo "<div class='grid-md-12 form-title-wrapper'>";
										echo "<img class='icon icon-size-4' src='" . WEBPATH . "/web/img/icon/icon-tournament.png'><span class='form-title'>Tournoi</span>";
									echo "</div>";

								    echo "<div class='grid-md-12'>";

								    echo "<div class='align'>";
								    	echo "<label for='nom'>Nom</label>";
										echo "<input type='text' name='id' class='hidden tournament-id-p' value='" . $tournament->getId() . "'>";
										echo "<input class='input-default admin-form-input-w tournament-name-p' name='name' type='text' value='" . $tournament->getName() . "'>";
									echo "</div>";

									echo "<div class='align'>";
										echo "<label for='description'>Description</label>";
										echo "<input class='input-default admin-form-input-w tournament-description-p' name='description' type='text' value='" . $tournament->getDescription() . "'>";
									echo "</div>";

										echo "<div class='relative'><span class='toggleCheck'><label for='status'>Status</label><input class='checkbox input-default tournament-status-p admin-checkbox-ajust' id='tournament-status-p' name='status' required type='checkbox' ";
											echo ($tournament->getStatus()!==NULL  && $tournament->getStatus()==-1) ? "checked=checked>" : ">";
										echo "<label class='ajusted-checkbox-label' for='status'>.</label></span></div>";								
									echo "</div>";
									//Submit
									echo "<div class='grid-md-12'>"; 
								    	echo "<button type='button' class='admin-form-submit tournament-submit-form-btn btn btn-pink'><a>Valider</a></button>";
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
		echo "<div class='grid-md-12 no-platform align'><span>Aucun tournoi enregistré pour le moment.</span></div>";		
	} 
?>		