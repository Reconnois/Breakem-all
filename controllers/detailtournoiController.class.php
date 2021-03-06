<?php

class detailtournoiController extends template{

	/*
	**Methode par défault de detailtournoiController -> set la vue par défault d'une page concernant un tournoi
	*/
	public function detailtournoiAction(){
		$v = new view();
		$this->assignConnectedProperties($v);
		$v->assign("css", "detailtournoi");
		$v->assign("js", "detailtournoi");
		$v->assign("title", "Tournoi <<name>>");
		$v->assign("content", "Tournoi <<name>>");
		$v->setView("detailtournoiDOM");
	}
	/*
	 ** /!\ AjaxOnly /!\
	 ** @params: $_POST:  
	 		(string) 't': lien($_get) du tournoi visité
	 		(string) 'sJeton': jeton créé à chaque reload de page (anti CSRF)

	 ** #### Crée la première rangé de matchs ou un premier match si le nombre d'équipes est impair ####
	*/
	public function createFirstMatchsAction(){
		$filteredinputs = $this->checkBasicInputsAndRights()['inputs'];
		$link = $filteredinputs['t'];
		$matchedTournament = $this->isUserTournamentOwner($link);
		if(!($matchedTournament instanceof tournament))
			$this->echoJSONerror("erreur DT_CFM_0", "impossible d'agir sur ce tournoi");

		// Recuperer tous les participants
		$rm = new registerManager();
		$allRegistered = $rm->getTournamentParticipants($matchedTournament);
		if(!!$allRegistered){
			foreach ($allRegistered as $key => $usr) {
				$matchedTournament->addRegisteredUser($usr);
			}
		}

		// Recuperer toutes les équipes avec le nombre de places prises
		$ttm = new teamtournamentManager();
		$allTournTeams = $ttm->getTournamentTeams($matchedTournament);
		if(!!$allTournTeams){
			foreach ($allTournTeams as $key => $teamtournament) {
				$usersInTeam = $rm->getTeamTournamentUsers($teamtournament);
				if(is_array($usersInTeam))
					$teamtournament->addUsers($usersInTeam);
				if($teamtournament->getTakenPlaces() < $matchedTournament->getMaxPlayerPerTeam() && $teamtournament->getTakenPlaces() != 0)
					$matchedTournament->addFreeTeam($teamtournament);
				else if($teamtournament->getTakenPlaces() == $matchedTournament->getMaxPlayerPerTeam())
					$matchedTournament->addFullTeam($teamtournament);
			}
			if($this->canMatchsBeCreated($matchedTournament)){
				// algo pour random les rencontres en prévoyant le cas où le nb d'équipe est impair
					// ds ce cas : créer un round préliminaire pour en éliminer une.
				$createdMatchs = $this->createMatchs($matchedTournament, 2);
				// print_r($createdMatchs);
				// exit;
				if( is_array($createdMatchs) && count($createdMatchs) > 0 ){
					foreach ($createdMatchs as $key => $createdMatch) {
						// print_r($createdMatch);
						$mm = new matchsManager();
						$mm->mirrorObject = $createdMatch;
						if($mm->create()){
							$dbMatch = $mm->getLastCreatedMatchOfTournament($matchedTournament);
							// print_r($dbMatch);
							if($dbMatch !== false){
								if(!$this->createMatchParticipantsOfMatch($dbMatch, $createdMatch->gtAllTeamsTournament()))
									$this->echoJSONerror("erreur: DT_CFM_1", "Problème interne lors de la création des matchs d'ouverture");
							}
							else{
								// On ne peut pas récupérer le dernier match créé et on ne peut pas relier les matchsparticipants au dernier match sans l'id nouvellement créé en db -> il faut donc supprimer le match avant d'afficher l'erreur
								$this->echoJSONerror("erreur: DT_CFM_2", "Problème interne lors de la création des matchs d'ouverture");
							}
						}
						else
							$this->echoJSONerror("erreur: DT_CFM_3", "Problème interne lors de la création des matchs d'ouverture");
					}
					if (count($createdMatchs) === 1 )
						echo json_encode(["success" => "Il y aura un premier match éliminatoire afin pour revenir à un nombre d'équipes pair"]);
					else
						echo json_encode(["success" => "La première série de matchs a été créée !"]);
					exit;
				}
				else
					$this->echoJSONerror("erreur: DT_CFM_4", "Problème interne lors de la création du match d'ouverture");
			}
		};
	}
	/*
	 ** /!\ AjaxOnly /!\
	 ** @params: $_POST:  
	 		(string) 't': lien($_get) du tournoi visité
	 		(string) 'sJeton': jeton créé à chaque reload de page (anti CSRF)
	 		(int) 'mId': identifiant "public" d'un match
	 		(int) 'ttId': identifiant "public" d'une équipe de tournoi

	 ** #### Définit le vainqueur d'un match de tournoi  ####
	 ** ########## Annonce le vainqueur du tournoi ########## 
	 ** ###### Ajoute les points en db ######
	*/
	public function selectWinnerAction(){
		$filteredinputs = $this->checkBasicInputsAndRights()['inputs'];
		$link = $filteredinputs['t'];
		$args = array(
            'mId' => FILTER_VALIDATE_INT,
            'ttId' => FILTER_VALIDATE_INT
		);
		$additionnalRequiredPosts = filter_input_array(INPUT_POST, $args);
		$additionnalRequiredPosts = array_filter($additionnalRequiredPosts);
		foreach ($args as $key => $value) {
			if(!isset($additionnalRequiredPosts[$key]))
				$this->echoJSONerror("inputs","missing input " . $key);
    	}		
		$filteredinputs = array_merge($filteredinputs, $additionnalRequiredPosts);
		$matchedTournament = $this->isUserTournamentOwner($link);
		if(!($matchedTournament instanceof tournament) )
			$this->echoJSONerror("erreur DT_SW_0", "impossible d'agir sur ce tournoi");
		// Si le tournoi n'a pas encore de gagnant 
		if(!is_numeric($matchedTournament->getIdWinningTeam())){
			// Recupérer toutes les infos du tournoi 
			$matchedTournament = $this->getFullyAlimentedTournament($matchedTournament);

			$m = new matchs(['id' => $filteredinputs['mId']]);
			$winnerTT = new teamtournament(['id' => $filteredinputs['ttId']]);
			$teamAndMatchArr = $this->getTeamOfMatchAndMatch($matchedTournament, $m, $winnerTT);
			if(is_array($teamAndMatchArr)){
				$m = $teamAndMatchArr['m'];
				$winnerTT = $teamAndMatchArr['tt'];
				// À partir d'ici on peut être sûr d'avoir reçu un match qui n'est pas encore joué et que l'équipe reçue participe bien à ce match
				/* ---> On peut donc update la table match et renvoyer un success à la view */
				$mm = new matchsManager();
				if($mm->setMatchWinner($m, $winnerTT)){
					// Vider totalement les matchs, teams et users du tournoi
					$matchedTournament = $this->isUserTournamentOwner($link);
					// L'alimenter des nouvelles modifs
					$matchedTournament = $this->getFullyAlimentedTournament($matchedTournament);
					// Savoir si tous les matchs ont donc été joués
					$winnerTeamsArr = $this->getLastWinningTeams($matchedTournament, true);

					// Il reste des matchs à jouer dans le denrier round
					if($winnerTeamsArr === true){
						// Attribuer les points aux participants du matchs
						$mpm = new matchParticipantsManager();
						$allPoints = $this->getPointsToGiveInMatch($m, $matchedTournament);
						if(!$mpm->setPointsOfTeamTournamentInMatch($m, $allPoints['winner'], $allPoints['loser']))
							$this->echoJSONerror("erreur: DT_SW_0", "L'attribution des points a échoué");
						echo json_encode(["success"=>"L'équipe ".$matchedTournament->gtPublicTeamIdToPrint($winnerTT) . " remporte donc le match"]);
						exit;
					};
					if( is_array($winnerTeamsArr) && count($winnerTeamsArr) === 1  && isset($winnerTeamsArr['end']) ){
						$ttm = new tournamentManager();
						if($ttm->setTournamentWinner($matchedTournament, $winnerTeamsArr['end'][0])){
							unset($ttm);
							// Attribuer les points aux participants du matchs avec un plus gros coeff puisque c'est la finale
							$mpm = new matchParticipantsManager();
							$allPoints = $this->getPointsToGiveInMatch($m, $matchedTournament, true);
							if(!$mpm->setPointsOfTeamTournamentInMatch($m, $allPoints['winner'], $allPoints['loser']))
								$this->echoJSONerror("erreur: DT_SW_0", "L'attribution des points a échoué");
							echo json_encode(["success" => "Le tournoi ".$matchedTournament->getName()." a donc trouvé son vainqueur"]);
							exit;
						}
						else
							$this->echoJSONerror("erreur: DT_SW_1", "Impossible de définir le gagnant du tournoi, si le problème persiste veuilez contacter un admin");
					}
					else if( is_array($winnerTeamsArr) && count($winnerTeamsArr) > 0 ){
						// Attribuer les points aux participants du matchs
						$mpm = new matchParticipantsManager();
						$allPoints = $this->getPointsToGiveInMatch($m, $matchedTournament);
						if(!$mpm->setPointsOfTeamTournamentInMatch($m, $allPoints['winner'], $allPoints['loser']))
							$this->echoJSONerror("erreur: DT_SW_6", "L'attribution des points a échoué");
						echo json_encode(["success"=>"L'équipe ".$matchedTournament->gtPublicTeamIdToPrint($winnerTT) . " remporte donc le match"]);
						exit;
					}
					else{
						$this->echoJSONerror("erreur: DT_SW_1", "Aucune équipe gagnante n'a été trouvée, si le problème persiste veuillez contacter un admin");
					}
				}
				else
					$this->echoJSONerror("erreur: DT_SW_2", "Impossible de définir l'équipe ".$matchedTournament->gtPublicTeamIdToPrint($winnerTT)." comme gagnante, si le problème persiste veuillez contacter un admin");
				exit;
			}
			else
				$this->echoJSONerror("erreur: DT_SW_3", "L'équipe et le match ne correspondent pas");
		}
		else
			$this->echoJSONerror("erreur: DT_SW_4", "Le tournoi a déjà un gagnant !");
	}
	/*
	 ** /!\ AjaxOnly /!\
	 ** @params: $_POST:  
	 		(string) 't': lien($_get) du tournoi visité
	 		(string) 'sJeton': jeton créé à chaque reload de page (anti CSRF)

	 ** #### Crée la prochaine rangé de matchs du tournoi ####
	*/
	public function createNextMatchsAction(){
		$filteredinputs = $this->checkBasicInputsAndRights()['inputs'];
		$link = $filteredinputs['t'];

		$matchedTournament = $this->isUserTournamentOwner($link);
		if(!($matchedTournament instanceof tournament))
			$this->echoJSONerror("erreur DT_CNM_0", "impossible d'agir sur ce tournoi");

		if(!is_numeric($matchedTournament->getIdWinningTeam())){
			$matchedTournament = $this->getFullyAlimentedTournament($matchedTournament);

			$winnerTeamsArr = $this->getLastWinningTeams($matchedTournament);
			if(!!$winnerTeamsArr){				
				if( is_array($winnerTeamsArr) && count($winnerTeamsArr) === 1 && isset($winnerTeamsArr['end'])){
					$ttm = new tournamentManager();
					if($ttm->setTournamentWinner($matchedTournament, $winnerTeamsArr['end'][0])){
						unset($ttm);
						echo json_encode(["success" => "Le tournoi ".$matchedTournament->getName()." a donc trouvé son vainqueur"]);
						exit;
					}
					else
						$this->echoJSONerror("erreur: DT_CNM_4", "Impossible de définir le gagnant du tournoi, si le problème persiste veuilez contacter un admin");
				}
				else{
					// Il reste au moins deux équipes et donc au moins un tour à jouer, le nombre d'équipe est assurément impair
					// (puisque le premier tour permet d'éliminer une seule équipe si le nb d'équipe est impair)
					$arrayOfMatchs = $this->createNextRoundOfMatchs($matchedTournament, $winnerTeamsArr);
					foreach ($arrayOfMatchs as $key => $mirrorMatch) {
						$mm = new matchsManager();
						$mm->mirrorObject = $mirrorMatch;
						if($mm->create()){
							$dbMatch = $mm->getLastCreatedMatchOfTournament($matchedTournament);
							// var_dump($dbMatch);
							if($dbMatch instanceof matchs){
								if(!$this->createMatchParticipantsOfMatch($dbMatch, $mirrorMatch->gtAllTeamsTournament()))
									$this->echoJSONerror("DT_CNM_5", "Problème interne lors de la création des matchs suivants");
							}
							else{
								// On ne peut pas récupérer le dernier match créé et on ne peut pas relier les matchsparticipants au dernier match sans l'id nouvellement créé en db -> il faut donc supprimer le match avant d'afficher l'erreur
								$this->echoJSONerror("DT_CNM_6", "Problème interne lors de la création des matchs suivants");
							}
						}
						else
							$this->echoJSONerror("DT_CNM_7", "Problème interne lors de la création des matchs suivants");
					}
					echo json_encode(["success" => "La prochaine rangée de matchs a été créée !"]);
					exit;	
				}
			}
			else
				$this->echoJSONerror("erreur: DT_CNM_3", "un des matchs n'a pas de vainqueur, si le problème persiste veuillez contacter un admin");
		}
		else
			$this->echoJSONerror("erreur DT_CNM_8", "Ce tournoi a déjà son vainqueur !");
	}
	/*
	 ** /!\ AjaxOnly /!\
	 ** @params: $_POST:  
	 		(string) 't': lien($_get) du tournoi visité
	 		(string) 'sJeton': jeton créé à chaque reload de page (anti CSRF)
	 		(string) 'pseudo': pseudo de l'user à kicker du tournoi

	 ** #### Kick un utilisateur inscrit au tournoi ####
	*/
	public function kickUserAction(){
		$filteredinputs = $this->checkBasicInputsAndRights()['inputs'];
		$link = $filteredinputs['t'];
		$args = array(
            'pseudo' => FILTER_SANITIZE_STRING
		);
		$additionnalRequiredPosts = filter_input_array(INPUT_POST, $args);
		$additionnalRequiredPosts = array_filter($additionnalRequiredPosts);
		foreach ($args as $key => $value) {
			if(!isset($additionnalRequiredPosts[$key]))
				$this->echoJSONerror("","manque: " . $key);
    	}		
		$filteredinputs = array_merge($filteredinputs, $additionnalRequiredPosts);
		$matchedTournament = $this->isUserTournamentOwner($link);
		if(!($matchedTournament instanceof tournament) )
			$this->echoJSONerror("erreur DT_KU_0", "Impossible d'agir sur ce tournoi");

		if(is_numeric($matchedTournament->getIdWinningTeam()))
			$this->echoJSONerror("", "Impossible d'agir sur un tournoi fini");

		$matchedTournament = $this->getFullyAlimentedTournament($matchedTournament, true, true);
		if(!!$matchedTournament->gtAllMatchs())
			$this->echoJSONerror("", "Impossible d'agir sur un tournoi déjà débuté");

		$um = new userManager();
		$u = new user(['pseudo' => trim($additionnalRequiredPosts['pseudo'])]);
		$u = $um->userByPseudoInstance($u);
		if(!($u instanceof user) )
			$this->echoJSONerror("", "Cet utilisateur n'existe pas");

		if(!$matchedTournament->isUserRegistered($u))
			$this->echoJSONerror("", "Cet utilisateur n'est pas inscrit à votre tournoi");

		$rm = new registerManager();
		if($rm->deleteRegisteredFromTournament($matchedTournament, $u))
			echo json_encode(["success" => "l'utilisateur ".$u->getPseudo(). " a bien été supprimé du tournoi"]);
		else
			$this->echoJSONerror("erreur DT_KU_1", "Impossible de kick " .$u->getPseudo(). " du tournoi");
		exit;
	}		


	/* ####  Méthodes privées censées seulement être utilisées par les public de cette classe et un peu aussi pour DR(m)Y #### */
		// Le tournoi reçu doit contenir toutes les équipes ainsi que les inscrits
		private function canMatchsBeCreated(tournament $t){
			// Recuperer tous les matchs du tournoi
			$matchsManager = new matchsManager();
			$allMatchs = $matchsManager->getMatchsOfTournament($t);
			if(!!$allMatchs)
				$this->echoJSONerror("", "Les matchs sont déjà créés pour ce tournoi !");
			if($t->getNumberRegistered() < $t->getMaxPlayer() / 2)
				$this->echoJSONerror("", "Pas assez d'inscrit pour créer les matchs !");
			else if($t->getNumberRegistered() >= $t->getMaxPlayer() / 2){
				foreach ($t->gtFreeTeams() as $key => $teamT) {
					print_r($teamT);
					if($teamT->getTakenPlaces() >= $t->getMaxPlayerPerTeam()/2)
						$this->echoJSONerror("", "Pas assez d'inscrit dans la team ". $teamT->getId());
				}
			}

			return true;
		}
		// Le tournoi reçu doit contenir toutes les équipes ainsi que les inscrits
		/*
		 **@params (tournament), (int) nombre d'équipes à faire jouer dans un match
		 **@returns (array[(matchs)]
		 ** ###### Crée les mirroirs de la première rangée de matchs d'un tournoi #####
		*/
		private function createMatchs(tournament $t, $teamsPerMatch){
			$num = $t->getNumberRegistered();
			$prekey = "s_";
			$allPlayingTeams = $t->gtAllTeams();
			$len = count($allPlayingTeams);
			if($len === 1)
				$this->echoJSONerror("", "Seule une équipe a été trouvée pour ce tournoi !");
			// On crée n matchs où n = $num/2
			if($num%$teamsPerMatch===0){
				$maxNumbMatch = $len/$teamsPerMatch;
				$mirrorMatchs = [];		

				$teamIndexes = [];
				while(count($mirrorMatchs) < $maxNumbMatch){
					$currentTeamIndexes = [];
					for ($i=0; $i < $teamsPerMatch; $i++) {
						$newIndex=rand(0, $len-1);
						while( in_array($newIndex, $teamIndexes) )
							$newIndex=rand(0, $len-1);

						$custom_key = $prekey.$newIndex;
						$currentTeamIndexes[$custom_key] = $newIndex;
						$teamIndexes[$custom_key] = $newIndex;
					}
					

					$match = new matchs([
						"startDate" => time(),
						'idTournament' =>$t->getId(),
						"matchNumber" => 1
					]);
					foreach ($currentTeamIndexes as $key => $tInd) {
						$match->addTeamTournament($allPlayingTeams[$tInd]);
					}
					$mirrorMatchs[] = $match;
				}
				return $mirrorMatchs;
			}
			// On crée un pré-match pour éliminer assez d'équipe pour qu'il ne reste que des manches "normales"
			else{
				// On se concentrera sur les matchs binaires avec 2 teams participantes
				$numberOfTeamsToEliminate = $num%$teamsPerMatch;
				$match = new matchs([
					"startDate" => time(),
					'idTournament' =>$t->getId(),
					"matchNumber" => 1
				]);

				$firstTeamIndex=rand(0, $len-1);
				$secondTeamIndex=rand(0, $len-1);
				while($secondTeamIndex === $firstTeamIndex)
					$secondTeamIndex=rand(0, $len-1);

				$match->addTeamTournament($allPlayingTeams[$firstTeamIndex]);
				$match->addTeamTournament($allPlayingTeams[$secondTeamIndex]);

				return [0=>$match];
			}
		}
		/*
		 **@params (matchs) match récupéré en db, (array) teamtournaments ac id
		 **returns (boolean)
		 --> Insert en db les équipes asssociées au match nouvellement inséré en base
		*/
		private function createMatchParticipantsOfMatch(matchs $m, $teamsTournamentsArray){		
			foreach ($teamsTournamentsArray as $key => $tt) {
				$mpm = new matchParticipantsManager();
				$mp = new matchParticipants([
					'idMatch' => $m->getId(),
					'idTeamTournament' => $tt->getId()
				]);
				$mpm->mirrorObject = $mp;
				if(!$mpm->create()){
					unset($mpm);
					$this->echoJSONerror("X3", "Problème interne lors de la création de matchs participants");
				}
			}
			if(isset($mpm))unset($mpm);
			return true;
		}
		/*
		 ** #### En se basant sur les idPublic des matchs et teamtournament reçus
		 	#### retourne les id respectives stockées en db 
		*/
		private function getTeamOfMatchAndMatch(tournament $t, matchs $m, teamtournament $tt){
			$realMatchId = $t->gtRevertPublicMatchId($m);
			$realTeamId = $t->gtRevertPublicTeamId($tt);
			foreach ($t->gtAllMatchs() as $key => $match) {
				if(!$match->gtWinningTeam() && $match->getId() == $realMatchId){
					foreach ($match->gtAllTeamsTournament() as $key => $team) {
						if($team->getId() == $realTeamId)
							return ['m'=>$match, 'tt'=>$team];
					}
				}
				else if($match->gtWinningTeam() && $match->getId() == $realMatchId)
					$this->echoJSONerror("erreur: DT_GTMM_1", "Ce match s'est déjà vu choisir un vainqueur");

			}
			return false;
		}
		/*
		 ** ### Retourne toutes les équipes pas encore éliminées d'un tournoi (préalablement fully alimenté) ###
		*/
		private function getLastWinningTeams(tournament $t, $returnLeftMatchesToPlay = false){
			$leftMatchesToPlay = [];
			$allMatchs = $t->gtAllMatchs();
			$winnerTeams = [];
			// Il faut aussi vérifier que le match fait partie du dernier tour joué dans le tournoi
			foreach ($allMatchs as $key => $match) {
				// Ce match fait partie du dernier round à jouer -pour l'instant-
				if($match->getMatchNumber() == $t->gtBiggestMatchNumber()){
					// Ce match n'a pas encore de vainqueur
					if(!($match->gtWinningTeam() instanceof teamtournament)){
						if($returnLeftMatchesToPlay)
							$leftMatchesToPlay[] = $match;
						else
							return false;
							
					}
					// Il en a un
					else
						$winnerTeams[] = $match->gtWinningTeam();
				}			
			}
			if( $returnLeftMatchesToPlay && count($leftMatchesToPlay)>0 )
				return true;
			if ( count($winnerTeams) === 0 )
				return false;

			// Cas où le seul match joué est le match d'ouverture "impair".
			// Il faut rajouter toutes les équipes n'ayant pas encore joué
			if( count($allMatchs) === 1 ){
				foreach ($t->gtAllTeams() as $key => $teamTournament) {
					if($teamTournament->getTakenPlaces() > 0){
						if($teamTournament->getId() != $allMatchs[0]->gtAllTeamsTournament()[0]->getId() && $teamTournament->getId() != $allMatchs[0]->gtAllTeamsTournament()[1]->getId())
							$winnerTeams[] = $teamTournament;
					}
				}
				return $winnerTeams;
			}
			// if( count($winnerTeams)%2 !== 0 && count($winnerTeams) <= 1)
			// 	return false;
			if( $t->gtNumberOfRoundsPlanned() == $t->gtBiggestMatchNumber() )
				return ["end" => $winnerTeams];
			else if($t->gtNumberOfRoundsPlanned() > $t->gtBiggestMatchNumber() && count($t->gtMatchesSortedByRank()[$t->gtBiggestMatchNumber()])===1 ){
				// On est dans le cas où le dernier round joué n'est pas le dernier mais il n'y a eu qu'un match
				// Il faut donc rajouter à $winnerTeams toutes les équipes ayant gagnées au round précédent
				$matchesOfPreviousRank = $t->gtMatchesSortedByRank()[$t->gtBiggestMatchNumber()-1];
				$teamsOfLastRank = $t->gtMatchesSortedByRank()[$t->gtBiggestMatchNumber()][0]->gtAllTeamsTournament();
				foreach ($matchesOfPreviousRank as $key => $prevMatch) {
					$oldWinningTeam = $prevMatch->gtWinningTeam();
					$oldTeamDidntPlayLastRound = true;
					foreach ($teamsOfLastRank as $key => $lastMatchTeam) {						
						if($oldWinningTeam->getId() == $lastMatchTeam->getId())
							$oldTeamDidntPlayLastRound =  false;
					}
					if( $oldTeamDidntPlayLastRound )
						$winnerTeams[] = $oldWinningTeam;
				}

			}
			return $winnerTeams;
		}
		/*
		 ** Crée la prochaine rangée de matchs mirroirs d'un tournoi (non pas la première)
		*/
		private function createNextRoundOfMatchs(tournament $t, $arrayOfLeftTeams, $teamsPerMatch=2){
			// print_r($arrayOfLeftTeams);
			$len = count($arrayOfLeftTeams);
			$numberOfMatchs = count($arrayOfLeftTeams)/2;
			$mirrorMatchs = [];
			$prekey = "s_";
			$teamIndexes = [];
			if(count($arrayOfLeftTeams)%2 === 0){
				while(count($mirrorMatchs) < $numberOfMatchs){
					$teamIndexedsOfCurrentMatch = [];
					for ($i=0; $i < $teamsPerMatch; $i++) {
						$newIndex=rand(0, $len-1);
						while( in_array($newIndex, $teamIndexes) )
							$newIndex=rand(0, $len-1);

						$custom_key = $prekey.$newIndex;
						$teamIndexes[$custom_key] = $newIndex;
						$teamIndexedsOfCurrentMatch[] = $newIndex;
					}
					

					$match = new matchs([
						"startDate" => time(),
						'idTournament' =>$t->getId(),
						"matchNumber" => $t->gtBiggestMatchNumber()+1
					]);
					foreach ($teamIndexedsOfCurrentMatch as $key => $tInd) {
						$match->addTeamTournament($arrayOfLeftTeams[$tInd]);
					}
					$mirrorMatchs[] = $match;
				}
				return $mirrorMatchs;
			}
			
			else{
				// On se concentrera sur les matchs binaires avec 2 teams participantes
				$match = new matchs([
					"startDate" => time(),
					'idTournament' =>$t->getId(),
					"matchNumber" => $t->gtBiggestMatchNumber()+1
				]);

				$firstTeamIndex=rand(0, $len-1);
				$secondTeamIndex=rand(0, $len-1);
				while($secondTeamIndex === $firstTeamIndex)
					$secondTeamIndex=rand(0, $len-1);

				$match->addTeamTournament($arrayOfLeftTeams[$firstTeamIndex]);
				$match->addTeamTournament($arrayOfLeftTeams[$secondTeamIndex]);

				return [0=>$match];
			}
		}
		/*
		 ** /!\ AjaxOnly /!\
		 ** @params: $_POST:  
		 		(string) 't': lien($_get) du tournoi visité
		 		(string) 'sJeton': jeton créé à chaque reload de page (anti CSRF)
		 ** @return: (array)
		 		(array) 'inputs': les $_POST reçus et donc validés
		 		(string) 'link': le lien 't' correspondant au tournoi consulté
		 ** #### Méthode de sécurité vérifiant le dernier tournoi consulté et le jeton (CSRF reçu) ####
		*/
		private function checkBasicInputsAndRights(){
			if(!$this->isVisitorConnected())
				$this->echoJSONerror("","Vous n'êtes pas connecté !");
			if(!isset($_SESSION['lastTournamentChecked']))
				$this->echoJSONerror("tournoi","aucun tournoi visité");
			$args = array(
	            't' => FILTER_SANITIZE_STRING,
	            'sJeton' => FILTER_SANITIZE_STRING
			);
			$filteredinputs = filter_input_array(INPUT_POST, $args);
			$filteredinputs = array_filter($filteredinputs);
			foreach ($args as $key => $value) {
				if(!isset($filteredinputs[$key]))
					$this->echoJSONerror("inputs","missing input " . $key);
	    	}

			// SECU ANTI CSRF
			if($filteredinputs['sJeton'] !== $_SESSION['sJeton'])
				$this->echoJSONerror("csrf","jetons ".$filteredinputs['sJeton']." et ".$_SESSION['sJeton']." differents !");
			$link = $filteredinputs['t'];
			// On vérifie que l'user tente de bien de s'inscrire au tournoi qu'il a visité	
			if($link !== $_SESSION['lastTournamentChecked'])
				$this->echoJSONerror("tournoi","link different du dernier tournoi visité");
			return ['inputs'=> $filteredinputs, 'link'=> $link];
		}
		/*
		 ** @params: (string): $link -> lien -traité- d'un tournoi	 		
		 ** @return: (false) || (instanceof tournament)	 		
		 ** #### Vérifie que l'user connecté est bien admin du tournoi et que le lien du tournoi ne contient pas "null" quelque part (LEFT OUTER JOIN issue) ####
		*/
		private function isUserTournamentOwner($link){
			if($link === false || is_numeric(strpos($link, 'null'))) 
				return false;

			$tm = new tournamentManager();
			$matchedTournament = $tm->getTournamentWithLink($link);
			// Si le tournoi existe bien en base
			if($matchedTournament instanceof tournament){
				// Si l'user est le créateur du tournoi
				if( $this->getConnectedUser()->getId() == $matchedTournament->getIdUserCreator() )
				{
					unset($tm);
					return $matchedTournament;
				}
				// Si l'user est super admin
				else if( (int) $this->getConnectedUser()->getStatus() > 2){
					unset($tm);
					return $matchedTournament;
				}
			}
			return false;
		}
		/*
		 ** @params: instanceof tournament, instanceof matchs
		 ** @returns: (array) [(int), (int)]
		 ** #### Calcule les points à attribuer aux vainqueurs et aux perdants d'un match
		*/
		private function getPointsToGiveInMatch(matchs $m, tournament $t, $isFinal = false){
			$registeredCoeff = (count($t->gtAllRegistered()) / count($t->gtParticipatingTeams())) + 1;
			$coeff = ( $registeredCoeff + (int) $m->getMatchNumber() + ((int) $m->gtAllTeamsTournament() - 1)) / 5;
			if($isFinal)
				$coeff *= 2;
			$winnerPoints = (int) (5 + 5*$coeff);
			$losersPoints = (int) (1 + 1*$coeff);
			return ['winner' => $winnerPoints, 'loser' => $losersPoints];
		}
}
