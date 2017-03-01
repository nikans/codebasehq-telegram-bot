<?php
	
namespace CodebasehqTelegramBot\Telegram\Model;

use \CodebasehqTelegramBot\Database\Model;
use \CodebasehqTelegramBot\Database\Connection;
use \CodebasehqTelegramBot\Telegram\Model\TelegramUser;
use \CodebasehqTelegramBot\Telegram\TelegramChatHandler;
	
require_once 'vendor/autoload.php';
	
class TelegramChat extends Model {
	
	public $id;
	public $username;
	public $date_added;
	protected $telegram_user_id;
	
	public $telegram_user;
	
	protected static $table_name = 'telegram_chats';
	protected static $persist_properties = ['id', 'username', 'telegram_user_id'];
	
	
	public function __construct($properties = array(), $fetch = true) {
        
        if(isset($properties['telegram_user']))
        	$properties['telegram_user_id'] = $properties['telegram_user']->id;
        	        	
        parent::__construct($properties, $fetch);
    }
    
    public function getTelegramUser() {
	    return TelegramUser::fetch(['id' => $this->telegram_user_id]);
    }
    
    public function getChatHandler() {
	    if(isset($this->id))
	    	return new TelegramChatHandler($this->id, $this->username);
	    return false;
    }
	
}