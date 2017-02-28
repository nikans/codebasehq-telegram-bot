<?php

namespace CodebasehqTelegramBot\Vscale;

use \Rmccue\Requests;
use \CodebasehqTelegramBot\Telegram\TelegramChatHandler;
use \CodebasehqTelegramBot\Telegram\Model\TelegramChat;
use \CodebasehqTelegramBot\Telegram\Model\TelegramUser;

require_once 'vendor/autoload.php';
require_once 'config/telegram.php';
require_once 'config/vscale.php';


class VscaleStatusUpdater {


	private static function constructHeaders($vscale_account) {
		
		$headers = array(
			'Accept' => 'application/json',
			'Content-type' => 'application/json', 
			'X-Token' => $vscale_account['token']
		);
		return $headers;
	}
	
	
	private static function requestBalance($vscale_account) {
		
		$headers = static::constructHeaders($vscale_account);
		$request = \Requests::get('https://api.vscale.io/v1/billing/balance', $headers);
		$data = json_decode($request->body);
		return $data;
	}
	
	private static function notifyOnLowBalanceIfNeededForAccount($vscale_account) {
		
		$balance = static::requestBalance($vscale_account);
		$funds = floor($balance->balance / 100);
				
		if($funds < $vscale_account['min_balance']) {
			
			$chats = [];
			foreach($vscale_account['notify_telegram_usernames'] as $telegram_username) {
				$user_chats = TelegramChat::fetchMultiple(['username' => $telegram_username]);
				$chats = array_merge($chats, $user_chats);
			}
			
			$message = '‼️ '.'VSCALE PROJECT "'.$vscale_account['name'].'" LOW ON FUNDS!'.'
'.
			$funds.' RUB of minimum '.$vscale_account['min_balance'].' RUB'.'
'.
			'Notified '.
			join(' ',
				array_map(
					function($e) {
						return '@'.$e;
					}, $vscale_account['notify_telegram_usernames']
				)
			);
			
			if(!empty($vscale_account['admin_url'])) {
				$message .= '
'.
				'<a href="'.$vscale_account['admin_url'].'">Admin panel</a>';
			}
					
			foreach($chats as $chat) {
				$chat_handler = $chat->getChatHandler();
				if($chat_handler)				
					$chat_handler->sendMessage($message);
			}	
			
			echo $message;		
		}
	}
	
	public static function notifyOnLowBalanceIfNeeded() {
		global $VSCALE_ACCOUNTS;
		foreach($VSCALE_ACCOUNTS as $vscale_account) {
			static::notifyOnLowBalanceIfNeededForAccount($vscale_account);
		}
	}
	
}