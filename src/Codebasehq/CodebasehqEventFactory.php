<?php
	
namespace CodebasehqTelegramBot\Codebasehq;

use CodebasehqTelegramBot\Codebasehq\Event;
	
class CodebasehqEventFactory {

	private static function decodeEvent($event_raw) {
		$event_json = json_decode($event_raw);
		$event_json->payload = json_decode(utf8_encode($event_json->payload));
		return $event_json;
	}
	
	public static function getEvent($event_raw) {
		
		$event_json = static::decodeEvent($event_raw);
		if($event_json->type == null) { return null; }
		
		switch ($event_json->type) {
			case 'ticket_update': return new Event\TicketUpdate($event_json, $event_raw);
			case 'ticket_creation': return new Event\TicketCreation($event_json, $event_raw);
			case 'exception_create': return new Event\ExceptionCreate($event_json, $event_raw);
			case 'push': return new Event\Push($event_json, $event_raw);
			case 'deployment': return new Event\Deployment($event_json, $event_raw);
			default: return new Event\Event($event_json, $event_raw);
		}
	}
	
}