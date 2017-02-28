<?php
	
namespace CodebasehqTelegramBot\Codebasehq;

use CodebasehqTelegramBot\Codebasehq\Event;
	
class CodebasehqEventFactory {
	
	public static function getEvent($event_json) {
				
		if($event_json->type == null) { return null; }
		
		switch ($event_json->type) {
			case 'ticket_update': return new Event\TicketUpdate($event_json);
			case 'ticket_creation': return new Event\TicketCreation($event_json);
			case 'exception_create': return new Event\ExceptionCreate($event_json);
			case 'push': return new Event\Push($event_json);
			case 'deployment': return new Event\Deployment($event_json);
			default: return new Event\Event($event_json);
		}
	}
	
}