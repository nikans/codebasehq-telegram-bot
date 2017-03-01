<?php
	
namespace CodebasehqTelegramBot\Telegram;

use \Longman\TelegramBot\Telegram;
use \Longman\TelegramBot;
use \CodebasehqTelegramBot\Telegram\TelegramSubscriptionsHandler;

require_once 'config/telegram.php';
require_once 'vendor/autoload.php';

	
class TelegramChatHandler {
	
	private $telegram;
	public $chat_id;
	public $username;
	private $input;
	
	function __construct($chat_id = null, $username = null) {
		$this->telegram = new Telegram(TELEGRAM_API_KEY, TELEGRAM_BOT_NAME);
				
		$this->chat_id = $chat_id;
		$this->username = $username;
	}
	
	public function handleInput() {
		$input = TelegramBot\Request::getInput();
		if($input == null || strlen($input) == 0) { 
			return; 
		}
		
		$this->input = json_decode($input);
		$this->chat_id = $this->input->message->chat->id;
		$this->username = $this->input->message->from->username;
				
		$registered = TelegramSubscriptionsHandler::registerUserIfNeeded(
			$this->chat_id, 
			$this->input->message->from->id,
			$this->username, 
			$this->input->message->from->first_name,
			$this->input->message->from->last_name
		);
		
// 		if($registered)
// 			$this->sendMessage("You have successfully subscribed, @".$this->username." (".$this->chat_id.")");
		
	}
	
	public function prettyInput() {
		return json_encode($this->input, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
	}
	
	public function sendMessage($message) {
		$result = TelegramBot\Request::sendMessage(['chat_id' => $this->chat_id, 'text' => $message, 'parse_mode' => 'HTML']);
		return $result;
	}
	
	public function logInput() {
		$filename = "test/telegram-".time().".json";
		file_put_contents($filename, $this->prettyInput());
	}
	
}