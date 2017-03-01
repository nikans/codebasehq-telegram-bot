<?php
	
namespace CodebasehqTelegramBot\Database;

use \CodebasehqTelegramBot\Database\Connection;

require_once 'vendor/autoload.php';
require_once 'config/codebasehq.php';


abstract class Model {
	
    protected static $connection;
    protected static $table_name = null; // override if needed
    protected static $persist_properties = ['id'];
    protected static $fuzzy_search_properties = ['id'];

	public static function init() {
		static::$connection = Connection::getInstance();
	}

    public function __construct($properties = array()) {

        if(isset($properties) && is_array($properties)) {
            foreach($properties as $key => $val) {
                $this->{$key} = $val;
            }
        }
    }
    
    protected static function getTableName() {
	    return static::$table_name == null ? get_called_class() : static::$table_name;
    }

//    public function getEntities($id = null) {
//
//        $queryString = 'select * from ?';
//
//        if(is_numeric($id) && $id > 0) {
//            $queryString = str_replace('?', '`'.get_called_class().'`', $queryString);
//            $queryString .= ' where id = '.$id;
//
//            $r = $this->connection->query($queryString);
//
//            while($row = $r->fetch_assoc()) {
//                foreach($row as $key => $val) {
//                    $this->{$key} = $val;
//                }
//            }
//
//            return $this;
//        }
//            echo get_called_class();
//        $mm = new ModelManager(get_called_class());
//        return $mm->entityArray;
//    }

    public function insert() {
	    
	    $queryString = "
			INSERT INTO ".static::getTableName()." (".join(',', static::$persist_properties).")
			VALUES (".
				join(',', 
					array_map(
						function($prop) { 
							return 
								"'".$this->{$prop}."'";
						}, 
						static::$persist_properties
					)
				).
				")
			ON DUPLICATE KEY UPDATE ".
				join(',', 
					array_map(
						function($prop) { 
							return 
								$prop."='".$this->{$prop}."'";
						}, 
						static::$persist_properties
					)
				);
	    
	    $r = static::$connection->query($queryString) 
	    	or die(static::getTableName().": ".$queryString." — ".mysqli_error(static::$connection));
		
		$insert_id = static::$connection->insert_id;
		
		if(!isset($this->id) && isset($insert_id))
			$this->id = $insert_id;
			
		return $insert_id;
    }
    
    public function persist() {
	    
	    if(!isset($this->id)) 
	    	return false;
	    
	    static::$connection->query("
			UPDATE ".static::getTableName()." SET 
			".
				join(',', 
					array_map(
						function($prop) { 
							return 
								$prop."='".$this->{$prop}."'";
						}, 
						static::$persist_properties
					)
				).
				"
			WHERE id='".$this->id."'"
		) or die(static::getTableName().": ".mysqli_error(static::$connection));
			
		return $this->id;
    }
    
    public function delete() {
	    static::$connection->query("
	    	DELETE FROM ".static::getTableName()." 
	    	WHERE id='".$this->id."'
	    ") or die(static::getTableName().": ".mysqli_error(static::$connection));
    }
    
    
    public static function fetch($properties = null) {
	    
	    if(!is_array($properties) || count($properties) < 1) {
		    return false;
	    }
	    
		$queryString = "SELECT * FROM ".static::getTableName();
		$queryString .= " WHERE ".
    		join(" AND ",
            	array_map(function ($key, $val) {
				    return $key." = '".$val."'";
				}, array_keys($properties), $properties)
			).
			" LIMIT 1";
		
		$r = static::$connection->query($queryString) 
			or die(static::getTableName().": ".$queryString." — ".mysqli_error(static::$connection));
		
		$row = $r->fetch_assoc();
		if(!isset($row['id']))
			return false;
			
		$myClass = get_called_class();
		$refl = new \ReflectionClass($myClass);
					
		$model = $refl->newInstanceArgs(array($row));
	    
	    return $model;
    }
    
    
	public static function fetchMultiple($properties = null) {
	    		
		$queryString = "SELECT * FROM ".static::getTableName();
		if(is_array($properties) && count($properties) > 0) {
			$queryString .= " WHERE ".
	    		join(' AND ', 
	    			array_map(function ($key, $val) {
					    return $key." = '".$val."'";
					}, array_keys($properties), $properties)
				);
		}
		
		$r = static::$connection->query($queryString) 
			or die(static::getTableName().": ".$queryString." — ".mysqli_error(static::$connection));
		
		$models = [];
		while($row = $r->fetch_assoc()) {
			$myClass = get_called_class();
			$refl = new \ReflectionClass($myClass);
						
			$m = $refl->newInstanceArgs(array($row));
            $models[] = $m;
        }
	    
	    return $models;
    }
    
    public static function fetchFuzzyMultiple($search) {
	    		
		$queryString = "SELECT * FROM ".static::getTableName();
		if(isset($search) && strlen($search) > 0) {
			$queryString .= " WHERE ".
	    		join(' OR ', 
	    			array_map(function ($prop) use ($search) {
					    return $prop." LIKE '%".$search."%'";
					}, static::$fuzzy_search_properties)
				);
		}
		
		$r = static::$connection->query($queryString) 
			or die(static::getTableName().": ".$queryString." — ".mysqli_error(static::$connection));
		
		$models = [];
		while($row = $r->fetch_assoc()) {
			$myClass = get_called_class();
			$refl = new \ReflectionClass($myClass);
						
			$m = $refl->newInstanceArgs(array($row));
            $models[] = $m;
        }
	    	    
	    return $models;
    }


	public static function persistMultiple($models) {
				
		static::$connection->query("BEGIN") 
			or die(static::getTableName().": ".mysqli_error(static::$connection));
		
		foreach($models as $model) {	
			$model->persist();
		}
		
		static::$connection->query("COMMIT") 
			or die(static::getTableName().": ".mysqli_error(static::$connection));
	}
	
	public static function insertMultiple($models) {
				
		static::$connection->query("BEGIN") 
			or die(static::getTableName().": ".mysqli_error(static::$connection));
		
		foreach($models as $model) {	
			$model->insert();
		}
		
		static::$connection->query("COMMIT") 
			or die(static::getTableName().": ".mysqli_error(static::$connection));
	}
	
	public static function deleteMultiple($properties = null) {
				
		if(is_a($properties[0], get_called_class())) {
			$models = $properties;
			
			static::$connection->query("BEGIN") 
				or die(static::getTableName().": ".mysqli_error(static::$connection));
		
			foreach($models as $model) {	
				$model->delete();
			}
			
			static::$connection->query("COMMIT") 
				or die(static::getTableName().": ".mysqli_error(static::$connection));
			
			return;
		}
		
		$queryString = "DELETE FROM ".static::getTableName();
		if(is_array($properties) && count($properties) > 0) {
			$queryString .= " WHERE ".
				join(' and ', 
	    			array_map(function ($key, $val) {
					    return $key." = '".$val."'";
					}, array_keys($properties), $properties)
				);
		}

		static::$connection->query($queryString) 
			or die(static::getTableName().": ".$queryString." — ".mysqli_error(static::$connection));	
	}

}

Model::init();