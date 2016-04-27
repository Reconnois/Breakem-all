<?php 
class teamController extends template{
	public function teamAction(){
		$v = new View();
        $this->assignConnectedProperties($v);
		$v->assign("css", "team");
		$v->assign("js", "team");
		$v->assign("title", "Team");
		$v->assign("content", "Liste des Teams");
		$v->setView("team");
	}

    private function isFormStringValid($string, $minLen = 4, $maxLen = 25, $optionnalCharsAuthorized = ""){
        $string = trim($string);
        $string = str_replace('  ', ' ', $string);
        if(strlen($string) < $minLen || strlen($string) > $maxLen)
            return false;
        $regex = '/[^a-z_\-0-9 '. $optionnalCharsAuthorized .']/i';
        if(preg_match($regex, $string))
            return false;
        return true;
    }

    private function echoJSONerror($name, $msg){
        $data['errors'][$name] = $msg;
        echo json_encode($data);
        flush();
        exit;
    }
	public function verifyAction(){
        if(!($this->connectedUser instanceof user)){
            $this->echoJSONerror("connection", "vous n'etes pas authentifie !");
        }
		$args = array(
            'name'     => FILTER_SANITIZE_STRING,    
            'slogan'     => FILTER_SANITIZE_STRING,   
    	    'description'     => FILTER_SANITIZE_STRING    
		);
		$filteredinputs = filter_input_array(INPUT_POST, $args);
        // $data = [];
		foreach ($args as $key => $value) {
			if(!isset($filteredinputs[$key]))
				$this->echoJSONerror("inputs", "manque: ".$key);
		}
        if(!($this->isFormStringValid($filteredinputs['name'])))
            $this->echoJSONerror("name", "le nom ne respecte pas les regles");
         
        if(!($this->isFormStringValid($filteredinputs['slogan'], 10, 50, ',\?!\.\+')))
            $this->echoJSONerror("slogan", "le slogan ne respecte pas les règles");
        if(!($this->isFormStringValid($filteredinputs['description'], 10, 250, ',\?!\.\+')))
            $this->echoJSONerror("description", "la description ne respecte pas les règles");
    
        $team = new team($filteredinputs);
        $dbTeam = new teamManager();

        //Presence du nom en BDD
        if($dbTeam->isNameUsed($team))
            $this->echoJSONerror("nameused", "nom déjà utilisé");
            
        //L'user a-t-il déjà une team
        if(is_numeric($this->connectedUser->getIdTeam()))
            $this->echoJSONerror("userhasteam", "vous avez déjà une team!");
            
        //Créaton team
        $dbTeam->create($team);
        // Récupération team (avec nouvel id)
        $team = $dbTeam->getTeamFromName($team);
        unset($dbTeam);

        if(!$team)
            $this->echoJSONerror("creation", "La création de votre team a echoué");

        // MàJ de l'idTeam du user connecté
        $dbUser = new userManager();
        $dbUser->setNewTeam($this->connectedUser, $team);
        unset($dbUser);
 
        // Creation de la ligne rightsTeam
        $riTeam = new rightsteam([
            'id'=>0,
            'idUser'=>$this->connectedUser->getId(),
            'idTeam'=>$team->getId(),
            'right'=>1,
            'title'=>'maitre',
            'description'=>'Un pour les controler tous'
        ]);
        $dbRightsTeam = new rightsteamManager();
        $dbRightsTeam->create($riTeam);            
        unset($dbRightsTeam);

        echo json_encode(["success" => true]);
	}
}