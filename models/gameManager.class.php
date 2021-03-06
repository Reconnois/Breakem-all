<?php
/*
*
*/
class gameManager extends basesql{

	public function getAllGamesName(){
		$sql = "SELECT name FROM " . $this->table." WHERE status>0 ";
		$sth = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute();

		$r = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(isset($r[0])){
			$data = [];
			foreach ($r as $key => $dataArr) {
				$data[] = new game($dataArr);
			}
			return $data;
		}
		return false;
	}
	
	public function getBestGames(){        
         $sql = "SELECT G.name, COUNT(DISTINCT(T.idGameVersion)) as nb_util_jeu, G.img
                 FROM tournament T, gameversion GV, game G
                 WHERE G.id = GV.idGame AND GV.id = T.idGameVersion AND G.id>0 AND G.status>0
                 LIMIT 0,1";
        $sth = $this->pdo->query($sql);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

	public function getGames(typegame $tg){		
		$sql = "SELECT name, description, img, status 
				FROM " . $this->table . " 
				WHERE idType= (SELECT id FROM typegame WHERE typegame.name = :name) 
					AND id>0 AND status>0
				ORDER BY name";
		$sth = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute([
			':name' => $tg->getName()
		]);
		$r = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(isset($r[0])){
			$data = [];
			foreach ($r as $key => $dataArr) {
				$data[] = new game($dataArr);
			}
			return $data;
		}
		return false;
	}

	public function getGameById($id){		
		$sql = "SELECT * FROM " . $this->table . " WHERE id>0 AND id=:id";
		$sth = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute([
			':id' => $id
		]);

		$r = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(isset($r[0]))
			return new game($r[0]);
		else
			return null;
	}

	/*public function isGame($name){
		$sql = "SELECT COUNT(*) as nb FROM game WHERE name = '" . $name['delname']."'";

		$sth = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute();
		$r = $sth->fetch(PDO::FETCH_ASSOC);
		if(isset($r['nb']))
			return (bool) (int) $r['nb'];
		return false;
	}*/

	public function deleteGames(game $game){
		$sql1 = "UPDATE gameversion SET idGame=-1 WHERE idGame=:id";
		$sth1 = $this->pdo->prepare($sql1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));		
		$sth1->bindValue(':id', $game->getId());
		$sth1->execute();

		$sql = "DELETE FROM " .$this->table . " WHERE id=:id";
		$sth = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));		
		$sth->bindValue(':id', $game->getId());
		$sth->execute();
	}

	public function getAllGamesAdmin(){
		$sql = "SELECT *  
			FROM game
			WHERE id>0
			ORDER BY name";
		$sth = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute();
		$r = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(isset($r[0])){
			$data = [];
			foreach ($r as $key => $dataArr) {
				$data[] = new game($dataArr);
			}
			return $data;
		}
		return false;	
	}

	public function getAllGames(){
		$sql = "SELECT g.id, g.name, g.description, g.year, g.img, g.idType, g.status, t.name as nameType 
				FROM game g 
				INNER JOIN typegame t 
				ON g.idType = t.id
				WHERE g.id>0 AND status>0
				ORDER BY name";
		$sth = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute();
		$r = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(isset($r[0])){
			$data = [];
			foreach ($r as $key => $dataArr) {
				$data[] = new game($dataArr);
			}
			return $data;
		}
		return false;
	}
	public function getAdminAllGames(){
		$sql = "SELECT g.id, g.name, g.description, g.year, g.img, g.idType, g.status, t.name as nameType 
				FROM game g 
				INNER JOIN typegame t 
				ON g.idType = t.id
				WHERE g.id>0
				ORDER BY name";
		$sth = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute();
		$r = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(isset($r[0])){
			$data = [];
			foreach ($r as $key => $dataArr) {
				$data[] = new game($dataArr);
			}
			return $data;
		}
		return false;
	}
	public function setGame(game $ancien, game $nouveau){

		$data = [];

		foreach (get_class_methods($nouveau) as $key => $method_name) {
			if(is_numeric(strpos($method_name, "get"))){
				$prop = strtolower(str_replace("get","",$method_name));
				$data[$prop] = ($prop==="img") ? $nouveau->$method_name(true) : $nouveau->$method_name(); 
			}
		}

		$data = array_filter($data);

		$compteur=0;

		$sql = "UPDATE ".$this->table." SET ";
			foreach ($data as $key => $value) {
				if($compteur!=0) 
					$sql.=", ";
				$sql.=" ".$key."=:".$key."";
				$compteur++;
			}
		$sql.=" WHERE id=:id";

		$query = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

		//ATTENTION: on précise la référence de $value avec &
		foreach ($data as $key => &$value)
			$query->bindParam(':'.$key, $value);
	
		$id = $ancien->getId();
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->execute();
	}
	public function gameByName(game $u){
		$sql = "SELECT name FROM " .$this->table . " WHERE name LIKE ?";
		$sth = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute(["%".$u->getName()."%"]);
		$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	
		return $r;
	}
	/*
	 **@params optionnal (instanceof user) (int)
	 **@returns (array)_of_games || empty (array)
	 ** ### Récupère les jeux les plus utilisés dans les tournois terminés 
	 	### Ou récupère les jeux les plus joués par un user si $u instanceof user && is_numeric($u->getId())
	*/
	public function getMostPlayedGames($u = false, $limit = 3){
		$limit = (int) $limit;
		if($limit < 1)
			$limit = 3;

		$sql = "SELECT g.id, g.name, g.description, g.img, g.year, g.idType, g.status, count(g.id) as timesPlayed ";
		$sql .= " FROM game g";
		$sql .= " INNER JOIN gameversion gv
				ON gv.idGame = g.id
				AND gv.id IS NOT NULL";
		$sql .= " INNER JOIN tournament t 
				ON t.idGameVersion = gv.id
				AND t.id IS NOT NULL
				AND t.idGameVersion IS NOT NULL
				AND t.idWinningTeam IS NOT NULL
				AND t.status > 0";
		if($u instanceof user && is_numeric($u->getId())){
			$sql .= " INNER JOIN register r
				ON r.idUser = :idUser
				AND t.id = r.idTournament";
		}
		$sql .= " WHERE g.status > -1
				GROUP BY g.id
				ORDER BY timesPlayed DESC
				LIMIT 0, ".$limit;
		$sth = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		if($u instanceof user && is_numeric($u->getId())){
			$sth->execute([
				':idUser' => $u->getId()
			]);
		}
		else
			$sth->execute();
		$r = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(empty($r))
			return [];
		if(isset($r[0])){
			$data = [];
			foreach ($r as $key => $dataArr) {
				$data[] = new game($dataArr);
			}
			return $data;
		}
		return false;
	}

}