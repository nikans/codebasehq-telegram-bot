<?php
	
namespace CodebasehqTelegramBot\Telegram;

use \Longman\TelegramBot\Telegram;
use \Longman\TelegramBot;
use \Longman\TelegramBot\Entities\Keyboard;
use \Longman\TelegramBot\Entities\KeyboardButton;
use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqUser;
use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqProject;
use \CodebasehqTelegramBot\Telegram\Model\TelegramUser;
use \CodebasehqTelegramBot\Telegram\Model\TelegramChat;
use \CodebasehqTelegramBot\Codebasehq\Structure\Ticket;
use CodebasehqTelegramBot\Codebasehq\CodebasehqTicketRequest;

require_once 'config/codebasehq.php';
require_once 'config/telegram.php';
require_once 'vendor/autoload.php';

	
class TelegramCommandHandler {
	
	protected $telegram;
	public $chat_id;
	public $username;
	protected $input;
	
// 	protected $step = 0; 
	
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
		$this->username = $this->input->message->chat->username;
		$message = $this->input->message->text;
		
		preg_match('/^\/(\w+)(\s(.+))?/', $message, $matches);

		$command = @$matches[1];
		$parameter = @$matches[3];
		
		if(!isset($command)) return;
		
		switch ($command) {
			case 'start': case 'help':
				$this->welcomeMessage();
				break;
			case 'echo':
				$this->echoMessage($parameter);
				break;
			case 'register':
				$this->registerCodebaseUserMessage($parameter);
				break;
			case 'tickets': case 't':
				$this->getCodebaseTicketsMessage($parameter);
				break;
			case 'my_tickets': case 'mt':
				$this->getMyCodebaseTicketsMessage($parameter);
				break;
		}
	}
	
	private function welcomeMessage() {
		$keyboard = new Keyboard();
		$keyboard->hide();
		
		$telegram_user = $this->getTelegramUser();
		$codebasehq_user_id = $telegram_user->getCodebasehqUserId();
		
		$welcome_message = 
			'Welcome to the <b>'.CODEBASE_ACCOUNT_NAME.'</b> CodebaseHQ Telegram Bot.
';
		if(!isset($codebasehq_user_id)) {
			$welcome_message .= 
			'You may use this command to <b>automatically register</b> your CodebaseHQ account in this bot:

'.
			'/register APIKEY

'.
			'You can find the api key on <a href="https://'.CODEBASE_ACCOUNT_NAME.'.codebasehq.com/settings/profile">your profile page</a> under "API Credentials" section.
'.
			'Alternatively, you may manually forward your chat id to the admin of this bot ('.TELEGRAM_ADMIN_USERNAME.'): ';
		}
		else {
			$codebase_user = $telegram_user->getCodebasehqUser();
			
			$welcome_message .= 
			'Your CodebaseHQ account <b>'.$codebase_user->username.'</b> is already registered.
'.
			'In case of any trouble you may write to the admin of this bot ('.TELEGRAM_ADMIN_USERNAME.').';
		}
		
		$welcome_message .= 
		
		$this->sendMessage($welcome_message, $keyboard);
		
		if(!isset($codebasehq_user_id)) {
			$this->sendMessage('Please add my codebase account to this chat id: '.$this->chat_id);
		}
		
		$help_message = 
		'Another commands to use:

'.
		'<b>Search tickets:</b>
'.
		'/tickets project_name [search_options]
'.
		'- <pre>project_name</pre>: fuzzy search for match in project name or permalink
'.
		'- <pre>search_options</pre>: <a href="https://support.codebasehq.com/articles/tickets/quick-search">quick search options</a> from CodebaseHQ (optional, though, recommended)

'.
		'<b>Your assigned unresolved tickets:</b>
'.
		'/my_tickets [project_name search_options]
'.
		'- <pre>project_name</pre> and <pre>search_options</pre> are optional

'.
		'You may use shortcuts: <pre>\t</pre> and <pre>\mt</pre> respectively.';
		
		$this->sendMessage($help_message);
	}
	
	private function echoMessage($text) {
		$this->sendMessage($text);
	}
	
	private function registerCodebaseUserMessage($api_key) {
		
		$codebasehq_user = CodebasehqUser::fetch(['api_key' => $api_key]);
		if($codebasehq_user && isset($codebasehq_user->id)) {
			$telegram_user = $this->getTelegramUser();
			$telegram_user->setCodebasehqUserId($codebasehq_user->id);
			$telegram_user->persist();
			
			$this->sendMessage('Successfully registered account <b>'.$codebasehq_user->username.'</b>');
		}
		else {
			$this->sendMessage("Sorry, can't find matching account. Check your api key or notify your bot admin (".TELEGRAM_ADMIN_USERNAME.")");
		}
	}
	
	private function getTelegramUser() {
		$telegram_chat = TelegramChat::fetch(['id' => $this->chat_id]);
		$telegram_user = $telegram_chat->getTelegramUser();
		return $telegram_user;
	}
	
	private function getCodebaseTicketsMessage($parameters) {
		
		if(strlen($parameters) == 0) {
			$this->sendMessage("Invalid parameters");
			return;
		}
		
		$search_options = null;
		
		if(strpos($parameters, " ")) {
			$parameters = explode(" ", $parameters);
			$project_search_string = array_shift($parameters);
			$search_options = $parameters;
		}
		else {
			$project_search_string = $parameters;
		}
		
		$telegram_chat = TelegramChat::fetch(['id' => $this->chat_id]);
		$telegram_user = $telegram_chat->getTelegramUser();
		$codebasehq_user = $telegram_user->getCodebasehqUser();
		
		$projects = CodebasehqProject::fetchFuzzyMultiple($project_search_string);
		$tickets = [];
		
		$tr = new CodebasehqTicketRequest($codebasehq_user);
		
		foreach($projects as $project) {
			$t = $tr->requestTickets($project->permalink, $search_options);
			$tickets = array_merge($tickets, $t);
		}
		$tickets = array_map(
			function($ticket) {
				return $this->formatTicket($ticket, false);
			},
			$tickets
		);
							
		if(count($tickets) == 0) {
			$this->sendMessage("No tickets found");
			return;
		}
					
		foreach($tickets as $ticket)
			$this->sendMessage($ticket);
	}
	
	private function getMyCodebaseTicketsMessage($parameters) {
		$project_search_string = null;
		$search_options = null;
		
		if(strpos($parameters, " ")) {
			$parameters = split(" ", $parameters);
			$project_search_string = array_shift($parameters);
			$search_options = $parameters;
		}
		else {
			$project_search_string = $parameters;
		}
				
		$telegram_chat = TelegramChat::fetch(['id' => $this->chat_id]);
		$telegram_user = $telegram_chat->getTelegramUser();
		$codebasehq_user = $telegram_user->getCodebasehqUser();
		
		$tr = new CodebasehqTicketRequest($codebasehq_user);
		$tickets = $tr->requestMyTickets($project_search_string, $search_options);
		$tickets = array_map(
			function($ticket) {
				return $this->formatTicket($ticket, true);
			},
			$tickets
		);
		
		if(count($tickets) == 0) {
			$this->sendMessage("No tickets found");
			return;
		}
		
		foreach($tickets as $ticket)
			$this->sendMessage($ticket);
	}
	
	private function formatTicket($ticket_json, $my = false) {
		$t = new Ticket($ticket_json);
		$str = $t->formatted($my);
		return $str;
	}
	
		
	public function sendMessage($message, $keyboard = null) {
		$parameters = ['chat_id' => $this->chat_id, 'text' => $message, 'parse_mode' => 'HTML'];
		if(isset($keyboard))
			$parameters = array_merge($parameters, ['reply_markup' => $keyboard]);
		$result = TelegramBot\Request::sendMessage(['chat_id' => $this->chat_id, 'text' => $message, 'parse_mode' => 'HTML']);
		return $result;
	}
	
}