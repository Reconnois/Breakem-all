<?php
	if(isset($listetournament)){	

		$cat = "<div class='admin-data-ihm-title align relative'>
			<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Début</span></div></div>
			<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Fin</span></div></div>
			<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Nom</span></div></div>
			<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Type</span></div></div>
			<div class='grid-md-4'><div class='admin-data-ihm-elem'><span class='capitalize'>Nombre de match</span></div></div>
		</div>";

		echo $cat;

		if(is_array($listetournament)){			
			foreach ($listetournament as $ligne => $tournament) {
				//Wrapper				
				echo "<div class='admin-data-ihm align relative'>";

					//Affichage
					echo "<div class='grid-md-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='capitalize tournament-datedebut-g'>" . $tournament->getStartDate() . "</span></div></div>";
					echo "<div class='grid-md-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='tournament-datefin-g'>" . $tournament->getEndDate() . "</span></div></div>";
					echo "<div class='grid-md-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='tournament-nom-g'>" . $tournament->getName() . "</span></div></div>";
					echo "<div class='grid-md-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='tournament-type-g'>" . $tournament->getTypeTournament() . "</span></div></div>";
					echo "<div class='grid-md-4 overflow-hidden'><div class='admin-data-ihm-elem'><span class='tournament-nbmatch-g'>" . $tournament->getNbMatch() . "</span></div></div>";
					//Fin 

					//Boutton
					echo "<div class='admin-data-ihm-btn hidden align'>";
						echo "<button class='admin-btn-default btn btn-yellow full admin-btn-modify open-form' type='button'><a>Modifier</a></button>";
						echo "<button class='admin-btn-default btn btn-white full admin-btn-delete' type='button'><a>Supprimer</a></button>";
					echo "</div>"; 
					//Fin Boutton

					//Formulaire
					echo "<div class='index-modal tournaments hidden-fade hidden'>";

						echo "<div class='index-modal-this index-modal-login align'>";
							
							echo "<div class='grid-md-4 inscription_rapide animation fade'>";
								echo "<form class='tournament-form admin-form' enctype='multipart/form-data' accept-charset='utf-8'>";
									//Title
									echo "<div class='grid-md-12 form-title-wrapper'>";
										echo "<img class='icon icon-size-4' src='" . WEBPATH . "/web/img/icon/icon-tournament.png'><span class='form-title'>Tournoi</span>";
									echo "</div>";
									//Label
									echo "<div class='grid-md-4 text-left'>";
									    echo "<label for='datedebut'>Début :</label>";
									    echo "<label for='datefin'>Fin :</label>";
									    echo "<label for='nom'>Nom :</label>";
									    echo "<label for='type'>Type :</label>";
								   		echo "<label for='description'>Description :</label>";
								   		echo "<label for='status'>Status :</label>";
									    echo "<label for='nbmatch'>Nombre de Match :</label>";
								   echo "</div>";
								    //Input
								    echo "<div class='grid-md-8'>";
										echo "<input type='text' name='id' class='hidden tournament-id-p' value='" . $tournament->getId() . "'>";
										echo "<input class='input-default admin-form-input-w tournament-datedebut-p' name='datedebut' type='text' value='" . $tournament->getStartDate() . "'>";
										echo "<input class='input-default admin-form-input-w tournament-datefin-p' name='datefin' type='text' value='" . $tournament->getEndDate() . "'>";
										echo "<input class='input-default admin-form-input-w tournament-nom-p' name='nom' type='text' value='" . $tournament->getName() . "'>";
										echo "<input class='input-default admin-form-input-w tournament-type-p' name='type' type='text' value='" . $tournament->getTypeTournament() . "'>";
										echo "<input class='input-default admin-form-input-w tournament-description-p' name='description' type='text' value='" . $tournament->getDescription() . "'>";
										echo "<input class='input-default admin-form-input-w tournament-status-p' name='status' type='text' value='" . $tournament->getStatus() . "'>";
										echo "<input class='input-default admin-form-input-w tournament-nbmatch-p' name='nbmatch' type='text' value='" . $tournament->getNbMatch() . "'>";
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