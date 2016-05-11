<?php 

class signalmentsuserManager extends basesql{
	public function __construct(){
		parent::__construct();
	}

	public function create(signalmentsuser $signalmentsuser){
		// Check afin de savoir qui appelle cette méthode
		$e = new Exception();
		$trace = $e->getTrace();

		// get calling class:
		$calling_class = (isset($trace[1]['class'])) ? $trace[1]['class'] : false;
		// get calling method
		$calling_method = (isset($trace[1]['function'])) ? $trace[1]['function'] : false;


		if(!$calling_class || !$calling_method)
			die("Pas de methode appelée pour la plainte !");

		// Si appelée depuis la page profil
		if($calling_class === "profilController" && $calling_method === "reportAction"){
			$this->columns = [];
			$report_methods = get_class_methods($signalmentsuser);

			foreach ($report_methods as $key => $method) {
				if(strpos($method, 'get') !== FALSE){
					$col = lcfirst(str_replace('get', '', $method));
					$this->columns[$col] = $signalmentsuser->$method();
				};
			}
			$this->columns = array_filter($this->columns);
			$this->save();
		}
		else
			die("Tentative d'enregistrement depuis une autre methode que reportAction de la classe profilController!");
	}

}