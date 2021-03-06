<?php

class gestiontournoiController extends template{
	
	public function __construct(){
		parent::__construct();

		//Visiteur ou membre banni
		if(!($this->isVisitorConnected()) || $this->connectedUser->getStatus()<1){
		 	header('Location: ' .WEBPATH.'/index');
		}
	}

	//Pour setter la date de fin automatiquement: utilise setStartDate($valeur, true)
	// public function setStartDate($v, $setEndDate = false){
	// 	$this->_startDate = $v;
	// 	if($setEndDate){
	// 		$this->setEndDate($v+$this->_gtMaxIntervalBetweenDates());
	// 		$this->_endDateWasAutoCreated = true;
	// 	}
			
	// }
	public function gestiontournoiAction(){

		$v = new view();
		$this->assignConnectedProperties($v);

		$args = array('t' => FILTER_SANITIZE_STRING);

		$filteredinputs = filter_input_array(INPUT_GET, $args);

		//Si lien fourni sinon redirection liste tournoi
		if(isset($filteredinputs['t']) && !empty($filteredinputs) && $this->isVisitorConnected()){

			$link = $filteredinputs['t'];

			$tournamentBDD = new tournamentManager();

			//On vérifie que l'utilisateur est bien propriétaire du tournoi

			$tournament = $tournamentBDD->getTournamentWithLink($link);

			if(!!$link && is_bool(strpos($link, 'null')) && $tournament !== false 
					&& $tournament->getIdUserCreator()==$this->connectedUser->getId()){

				$v->assign("css", "gestiontournoi");
				$v->assign("js", "gestiontournoi");
				$v->assign("title", "Gestion de votre tournoi");
				$v->assign("tournoi",$tournament);
				$v->assign("content", "Gérer votre tournoi");

				if($tournament->getStatus()=="-1")
					$v->assign("verrouillage"," disabled ");
				else
					$v->assign("verrouillage"," ");

				/* MAJ effectuées auparavant */
				if(isset($_SESSION['referer_method'])){
					
					$e = new Exception();
					$trace = $e->getTrace();

					// Classe appelante
					$calling_class = (isset($trace[0]['class'])) ? $trace[0]['class'] : false;

					//Methode appelante
					$calling_method = $_SESSION['referer_method'];

					if($calling_class === "gestiontournoiController" && $calling_method === "update")
						$v->assign("MAJ","1");

					if($calling_class === "gestiontournoiController" && $calling_method === "errorExpiration")
						$v->assign("ErrorExpiration","1");

					if($calling_class === "gestiontournoiController" && $calling_method === "errorDatas")
						$v->assign("ErrorDatas","1");

					if($calling_class === "gestiontournoiController" && $calling_method === "errorDate")
						$v->assign("ErrorDate","1");

					unset($_SESSION['referer_method']);
				}

				// Recuperer tous les participants
				$rm = new registerManager();
				$allRegistered = $rm->getTournamentParticipants($tournament);
				// Ne les envoyer ds la vue que s'il y en a
				if(!!$allRegistered)
					$v->assign("allRegistered", $allRegistered);

				// Recuperer toutes les équipes avec le nombre de places prises
				$ttm = new teamtournamentManager();
				$allTournTeams = $ttm->getTournamentTeams($tournament);
				if(!!$allTournTeams){
					$freeTeams = [];
					$fullTeams = [];
					foreach ($allTournTeams as $key => $teamtournament) {
						$usersInTeam = $rm->getTeamTournamentUsers($teamtournament);
						if(is_array($usersInTeam))
							$teamtournament->addUsers($usersInTeam);
						if($teamtournament->getTakenPlaces() < $tournament->getMaxPlayerPerTeam())
							$freeTeams[] = $teamtournament;
						else
							$fullTeams[] = $teamtournament;
					}
					$v->assign("freeTeams", $freeTeams);
					$v->assign("fullTeams", $fullTeams);
				};

				$v->setView("gestiontournoi");
				return;
			};
			unset($tm);
			header('Location: '.WEBPATH.'/404');
		}
		// Pas de get connu reçu, on affiche la page par défaut des tournois
		else
			header('Location: '.WEBPATH.'/tournoi');		
	}
	
	public function updateAction(){
	    //  infos récuperées après filtre de sécurité de checkUpdateInputs()
	    $checkedDatas = $this->checkUpdateInputs();

		$filteredinputs = array_filter(filter_input_array(INPUT_POST, array('link' => FILTER_SANITIZE_STRING)));

		$tournamentBDD = new tournamentManager();

		//On vérifie que l'utilisateur est bien propriétaire du tournoi

		$tournament = $tournamentBDD->getTournamentWithLink($filteredinputs['link']);

		if(!!$filteredinputs['link'] && is_bool(strpos($filteredinputs['link'], 'null')) 
				&& $tournament !== false && $tournament->getIdUserCreator()==$this->connectedUser->getId() 
				&& date('Ymd')<date('Ymd',$tournament->getStartDate())){

		    $newtournament = new tournament($checkedDatas);

			//calcul du nombre de jours 

			$dateactuelle = date_create(date('Y-m-d'));
			$dateMAJ = date_create(date('Y-m-d',$newtournament->getStartDate()));

			$difference = date_diff($dateactuelle, $dateMAJ);

			$nbjours = $difference->format("%a");
			$ecartjour = $difference->format("%R");

			if($newtournament->getStartDate() != null && $tournament->getStartDate() != null){
				if($ecartjour==="+" && $nbjours>="2"){
					// On met à jour la date de début
			    	$newtournament->setStartDate($newtournament->getStartDate(), true);
			    	$tournamentBDD->setTournament($tournament, $newtournament);
				}
				else 
					$_SESSION['referer_method']="errorExpiration";
			}
			else
				$_SESSION['referer_method']="errorDatas";
		}
	
		header("Location: ".$_SERVER['HTTP_REFERER']."");
	}

	//Methode présente dans Controller et non template car on ne peut faire de MAJ qu'ici
	private function checkUpdateInputs(){

		//FILTER_SANITIZE_STRING Remove all HTML tags from a string
	    $args = array(
	      'name' => FILTER_SANITIZE_STRING,
	      'description'   => FILTER_SANITIZE_STRING,
	      'Dday'   => FILTER_SANITIZE_STRING,     
	      'Dmonth'   => FILTER_SANITIZE_STRING,     
	      'Dyear'   => FILTER_SANITIZE_STRING,
	      'link' => FILTER_SANITIZE_STRING
	    );

		$filteredinputs = filter_input_array(INPUT_POST, $args);

		$_SESSION['referer_method']="update";

		//Début tournoi
		if(isset($filteredinputs['Dday']) || isset($filteredinputs['Dmonth']) || isset($filteredinputs['Dyear'])){

			$filteredinputs['Dmonth'] = (isset($filteredinputs['Dmonth'])) ? (int) $filteredinputs['Dmonth'] : "";
		    $filteredinputs['Dday'] = (isset($filteredinputs['Dday'])) ? (int) $filteredinputs['Dday'] : "";
		    $filteredinputs['Dyear'] = (isset($filteredinputs['Dyear'])) ? (int) $filteredinputs['Dyear'] : "";

		    if(checkdate($filteredinputs['Dmonth'], $filteredinputs['Dday'], $filteredinputs['Dyear'])){
		    	$datedeb = DateTime::createFromFormat('j-n-Y',$filteredinputs['Dday'].'-'.$filteredinputs['Dmonth'].'-'.$filteredinputs['Dyear']);

		    	if($datedeb)
		      		$filteredinputs['startDate'] = date_timestamp_get($datedeb);
		      	else
		    		$_SESSION['referer_method']="errorDate";
		 		  	
		    	unset($filteredinputs['Dday']);
		      	unset($filteredinputs['Dmonth']);
		      	unset($filteredinputs['Dyear']);
		    }
		    else
		    	$_SESSION['referer_method']="errorDate";
		} 	
	    return array_filter($filteredinputs);
  	}


  	public function deleteTourAction(){

        $args = array(
            'link' => FILTER_SANITIZE_STRING
        );

        $filteredinputs = filter_input_array(INPUT_POST, $args);

        $tournamentBDD = new tournamentManager();
        $tournoi = $tournamentBDD->getTournamentWithLink($filteredinputs['link']);
    
        if(!!$tournoi && $tournoi->getIdUserCreator() == $this->getConnectedUser()->getId()){
            $tournamentBDD->deleteTour($tournoi);
        }
        else
            return null;

    }

    /*public function mailMember(){

    	//FILTER_SANITIZE_STRING Remove all HTML tags from a string
	    $args = array( 'message' => FILTER_SANITIZE_STRING, 
	    			   'link' => FILTER_SANITIZE_STRING
	    			);
		$filteredinputs = filter_input_array(INPUT_POST, $args);

   		$tournamentBDD = new tournamentManager();
       	$tournoi = $tournamentBDD->getTournamentWithLink($filteredinputs['link']);

		if($filteredinputs && $tournoi->getIdUserCreator() == $_id){

			$registerBDD = new registerManager();
			$listeuser = $registerBDD->getTournamentParticipants($tournoi);

			foreach ($listeuser as $user) {
				$this->envoiMail($user->getEmail(),'Un organisateur de tournoi vous a envoyé un message.',$filteredinputs['message']);
			}
 
    	}
    }*/

}
