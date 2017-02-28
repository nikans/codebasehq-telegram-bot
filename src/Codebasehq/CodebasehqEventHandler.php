<?php
	
namespace CodebasehqTelegramBot\Codebasehq;

// use \Longman\TelegramBot\Telegram;
use CodebasehqTelegramBot\Codebasehq\CodebasehqEventFactory;
use CodebasehqTelegramBot\Codebasehq\Event;

require_once 'config/telegram.php';
require_once 'config/codebasehq.php';
require_once 'vendor/autoload.php';

	
class CodebasehqEventHandler {
	
	public $event;
	
	function __construct($event_raw) {
		$event_json = json_decode($event_raw);
		$event_json->payload = json_decode(utf8_encode($event_json->payload));
		
		$this->event = CodebasehqEventFactory::getEvent($event_json);
	}
	
	public function logEvent() {
		$filename = "test/codebasehq-".time().".json";
		file_put_contents($filename, $this->event->prettyJson());
	}
	
}