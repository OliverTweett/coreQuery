<?php

class core_Query {

	private $PDOInstance = null;
	private static $instance = null;

	const DEFAULT_SQL_USER = QHM_USER;
	const DEFAULT_SQL_HOST = CONFIGDB_SERVER;
	const DEFAULT_SQL_PASS = QHM_PASSW;
	const DEFAULT_SQL_DTB = QHM_BASE;

	     function __construct() {
		$this->PDOInstance = new PDO('mysql:dbname=' . self::DEFAULT_SQL_DTB . ';charset=' . CHARSET_SQL . ';host=' . self::DEFAULT_SQL_HOST, self::DEFAULT_SQL_USER, self::DEFAULT_SQL_PASS);
	    }

	    public static function getInstance() {
		if (is_null(self::$instance)) {
		    self::$instance = new core_Query();
		}
		return self::$instance;
	    }

	    public function query($query) {
		return $this->PDOInstance->query($query);
	    }

	    public function exec($query) {
		return $this->PDOInstance->query($query);
	    }

	    public function prepare($query) {
		return $this->PDOInstance->prepare($query);
	    }

	    public function lastID($query) {
		return $this->PDOInstance->lastInsertId($query);
	    }
	    

	

	 public    function read_Query($tb, $select, $where) {
		if (empty($select)) {
		    $select = '*';
		}
		$result = core_Query::getInstance()->prepare('SELECT  ' . $select . '  FROM ' . $tb . ' WHERE ' . $where . ' ');
		$result->execute();
		
		return $result;		
	 }

	    public function insert_Query($tb, $data) {
		$insert = '';		
		$bindinsert = '';
		$val_array = '';
		$i = 0;
		while(list($indice,$valeur) = each($data)){ 
			
			$results = core_Query::getInstance()->query('SHOW COLUMNS   FROM ' . $tb . '  ');
			while ($row = $results->fetch(PDO::FETCH_NUM)) {
			    	  if($indice == $row[0]){
				      
						 if ( $i > 0) {
						    $insert .= ", ";						
						    $val_array .=", ";
						}
						$i++;
						$insert .= "$row[0]";
						$bindinsert[$row[0]] = $data[$row[0]];						
						$val_array .=":$row[0]";	
				  }
			}
		}
		
		$result = core_Query::getInstance()->prepare("INSERT INTO $tb  ($insert) VALUES ($val_array)");			
		while(list($indice,$valeur) = each($bindinsert)){ 
			$result->bindValue(':'.$indice.'', $valeur);			
		}					
		$result->execute();
		$id_tbKEY = core_Query::getInstance()->lastID();		
		
		return $id_tbKEY;
		
	    }

	    public function delete_Query($tb,$where) {    
		$result = core_Query::getInstance()->prepare("DELETE FROM $tb   WHERE ".$where." ");               
		$result->execute();	
	     } 

	    public function update_Query($tb, $where, $data) {
		$insert = '';
		$bindinsert = '';
		$i = 0;	
		while(list($indice,$valeur) = each($data)){ 
			$results = core_Query::getInstance()->query('SHOW COLUMNS   FROM ' . $tb . ' LIKE "'.$indice.'"  ');
				while ($row = $results->fetch(PDO::FETCH_NUM)) {
					  if($indice == $row[0]){	
					         if ( $i > 0) {
						$insert .= ", ";
					        } 					
					    
					      $insert .= "$indice = :$indice";
					      $bindinsert[$row[0]] = $data[$row[0]];	
					      $i++;
					 }
				}
		}

		$result = core_Query::getInstance()->prepare("UPDATE $tb  SET $insert  WHERE $where ");    
		while(list($indice,$valeur) = each($bindinsert)){ 
			$result->bindValue(':'.$indice.'', $valeur);
		}
		$result->execute();	
		
		
	    }
	    
	   public function read_countQuery($tb,$select,$where) {		
		if(!$select){ $select = '*';	 }		
		$result = core_Query::getInstance()->prepare("SELECT $select FROM $tb WHERE  $where ");               
		$result->execute();			
		$num_rows = $result->rowCount();
		return $num_rows;
	    }
	    
	        public function RetourneNbRequetes(){
		    return $this->NbRequetes;
		}

}



?>