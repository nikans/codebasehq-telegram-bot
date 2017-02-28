<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Model;

use \CodebasehqTelegramBot\Database\Model;
use \CodebasehqTelegramBot\Database\Connection;
use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqAssignment;
	
require_once 'vendor/autoload.php';
	
class CodebasehqUser extends Model {
	
	public $id;
	public $username;
	public $first_name;
	public $last_name;
	public $email_address;
	public $date_added;
	public $api_key;
	public $enabled;
	public $company;
		
	protected static $table_name = 'codebasehq_users';
	protected static $persist_properties = ['id', 'username', 'first_name', 'last_name', 'email_address', 'api_key', 'enabled', 'company'];
	
	
	public function __construct($properties = array(), $fetch = true) {
        
        if(isset($properties['user']))
        	$properties['user_id'] = $properties['user']->id;
        	        	
        parent::__construct($properties, $fetch);
    }
    
    public function getAssignments() {
	    return CodebasehqAssignment::fetchMultiple(['codebasehq_user_id' => $this->id]);
    }
	
}