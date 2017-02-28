<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Event;

use CodebasehqTelegramBot\Codebasehq\Dictionary\TicketField;
	
class TicketUpdate extends Event {
	
	private $ticket;
	
	function __construct($event_json) {
		parent::__construct($event_json);
		
		$this->ticket = $this->payload()->ticket;
		$this->project_name = $this->ticket->project->name;
		
		if(isset($this->ticket->assignee) && isset($this->ticket->assignee->username))
			$this->codebasehq_subject_username = $this->ticket->assignee->username;
		if(isset($this->payload()->user) && isset($this->payload()->user->username))
			$this->codebasehq_actor_username = $this->payload()->user->username;
	}
	
	public function formatted() {
		$str = 
			"<b>".$this->project_name.":</b> ".
			"<a href='".$this->ticket->url."'>Ticket #".$this->ticket->id." (".$this->ticket->summary.")</a> assigned to <b>".(isset($this->ticket->assignee->name) ? $this->ticket->assignee->name : "nobody")."</b> ".
			"has been <b>updated</b> by ".$this->payload()->user->name.".
";
		
		$changes = [];
		foreach($this->payload()->changes as $field => $values) {
			$values[0] = $values[0] == null ? "nothing" : $values[0];
			$values[1] = $values[1] == null ? "nothing" : $values[1];
			$field = TicketField::changeLabel($field);
			$changes[] = "<i>".$field.":</i> ".$values[0]." → ".$values[1];
		}
			
		$str .= join($changes, "
");
		if(isset($this->payload()->content) && strlen($this->payload()->content) > 0) { 
			if(count($changes) > 0)
				$str .= "
"; 
			$str .= "<i>".$this->payload()->content."</i>";
		}
				
		$project_name = $this->projectNameFromUrl($this->ticket->project->url);
		
		$str .= "
";
		$str .= 
			'#'.$project_name.' '.
			'#'.$project_name.'_'.$this->ticket->id;
		
// 		echo $str;
		
		return $str;
	}
	
}