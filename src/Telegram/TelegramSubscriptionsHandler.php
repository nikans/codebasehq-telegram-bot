<?php
	
namespace CodebasehqTelegramBot\Telegram;

use \CodebasehqTelegramBot\Database\Connection;
use \CodebasehqTelegramBot\Telegram\Model\TelegramUser;
use \CodebasehqTelegramBot\Telegram\Model\TelegramChat;
// use \Longman\TelegramBot\Telegram;

require_once 'config/telegram.php';
require_once 'vendor/autoload.php';

	
class TelegramSubscriptionsHandler {
	
// 	private $telegram;
	
// 	function __construct() {
// 	}
	
	private static function allChatsIds() {
		
		$connection = Connection::getInstance();
		
		$q = $connection->query("
		SELECT id FROM telegram_chats
		");
		
		$ids = [];
		while($row = $q->fetch_array()) {
			$ids[] = $row[0];
		}
		
		return $ids;
	}
	
	private static function chatsForProjectNotifications($project_name) {
		
		$connection = Connection::getInstance();
		
		$q = $connection->query("
			SELECT tc.id FROM telegram_chats tc
			JOIN codebasehq_projects cp ON cp.name = '".$project_name."'
			JOIN codebasehq_assignments ca ON ca.codebasehq_project_id = cp.id
			JOIN telegram_users tu ON tu.codebasehq_user_id = ca.codebasehq_user_id
			WHERE tc.username = tu.username
		");
		
		$ids = [];
		while($row = $q->fetch_array()) {
			$ids[] = $row[0];
		}
		
		return $ids;
	}
	
	public static function sendMessagesToProjectAssignees($message, $project_name, $mark_for_codebasehq_users = null, $ignore_codebasehq_users = null) {
		$chat_ids = self::chatsForProjectNotifications($project_name);
		self::broadcastMessage($message, $chat_ids, $mark_for_codebasehq_users, $ignore_codebasehq_users);
	}
	
	public static function broadcastMessage($message, $chat_ids = null, $mark_for_codebasehq_users = null, $ignore_codebasehq_users = null) {
		if($chat_ids == null)
			$chat_ids = self::allChatsIds();

		foreach ($chat_ids as $chat_id) {
			$telegram_chat = TelegramChat::fetch(['id' => $chat_id]);
			$telegram_user = $telegram_chat->getTelegramUser();
			$codebasehq_user = $telegram_user->getCodebasehqUser();
						
			if(in_array($codebasehq_user->username, $ignore_codebasehq_users))
				continue;
			
			$chat = new \CodebasehqTelegramBot\Telegram\TelegramChatHandler($chat_id);

			if(in_array($codebasehq_user->username, $mark_for_codebasehq_users))
				$message = "‼️ ".$message." #my_assigned";
			
			$chat->sendMessage($message);
		}
	}
	
	public static function registerUserIfNeeded($chat_id, $username, $first_name = null, $last_name = null) {
				
		$connection = Connection::getInstance();
		
		$user = null;
		
		if(isset($username) && strlen($username) > 0) {
			$user = TelegramUser::fetch(['username' => $username]);
			
			if($user) {
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				
				$user->persist();
			}
		}
		
		$q = $connection->query("
			SELECT COUNT(id) FROM telegram_chats
			WHERE id = '".$chat_id."'
		");
		
		if($q->fetch_row()[0] == 0) {
						
			if(!isset($user->id)) {
				$user = new TelegramUser(
					['username' => $username, 'first_name' => $first_name, 'last_name' => $last_name]
				);
				$user->insert();
			}
			
			$q = $connection->query("
				INSERT INTO telegram_chats (id, username, telegram_user_id)
				VALUES ('".$chat_id."', '".$username."', '".$user->id."')
			") or die($connection->error);
			
			static::sendMessageToAccountAdmin(
				"New user registered. Username: ".(isset($username) ? $username : "undefined").", Name: ".$first_name." ".$last_name.", Chat id: ".$chat_id
			);
			
			return true;
		}
		return false;
	}
	
	private static function sendMessageToAccountAdmin($message) {
		$telegram_admin = TelegramUser::fetch(['username' => TELEGRAM_ADMIN_USERNAME]);

		if(!$telegram_admin || $telegram_admin->getTelegramChats() == null)
			return;
			
		foreach($telegram_admin->getTelegramChats() as $chat) {
			$chat_handler = new \CodebasehqTelegramBot\Telegram\TelegramChatHandler($chat->id);
			$chat_handler->sendMessage($message);
		}
	}
	
}