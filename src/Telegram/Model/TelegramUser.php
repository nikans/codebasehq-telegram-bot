<?php
	
namespace CodebasehqTelegramBot\Telegram\Model;

use \CodebasehqTelegramBot\Database\Model;
use \CodebasehqTelegramBot\Database\Connection;
use \CodebasehqTelegramBot\Telegram\Model\TelegramChat;
use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqUser;
	
require_once 'vendor/autoload.php';
	
class TelegramUser extends Model {
	
	public $id;
	public $username;
	public $first_name;
	public $last_name;
	public $date_added;
	protected $codebasehq_user_id;
	
	public $codebasehq_user;
	
	protected static $table_name = 'telegram_users';
	protected static $persist_properties = ['id', 'username', 'first_name', 'last_name', 'codebasehq_user_id'];
	
	
	public function __construct($properties = array(), $fetch = true) {
        
        if(isset($properties['codebasehq_user']))
        	$properties['codebasehq_user_id'] = $properties['codebasehq_user']->id;
        	        	
        parent::__construct($properties, $fetch);
    }
    
    public function setCodebasehqUserId($id) {
	    $this->codebasehq_user_id = $id;
    }
    
    public function getCodebasehqUserId() {
	    return $this->codebasehq_user_id == 0 ? null : $this->codebasehq_user_id;
    }
    
    public function getCodebasehqUser() {
	    if($this->getCodebasehqUserId() != null)
	    	return CodebasehqUser::fetch(['id' => $this->getCodebasehqUserId()]);
	    return null;
    }
    
    public function getTelegramChats() {
	    return TelegramChat::fetchMultiple(['telegram_user_id' => $this->id]);
    }
	
}