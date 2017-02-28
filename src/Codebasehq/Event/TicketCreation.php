<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Event;
	
class TicketCreation extends Event {
	
	private $ticket;
	
	function __construct($event_json) {
		parent::__construct($event_json);
		
		$this->ticket = $this->payload();
		$this->project_name = $this->ticket->project->name;
		
		if(isset($this->ticket->assignee) && isset($this->ticket->assignee->username))
			$this->codebasehq_subject_username = $this->ticket->assignee->username;
		if(isset($this->ticket->reporter) && isset($this->ticket->reporter->username))
			$this->codebasehq_actor_username = $this->ticket->reporter->username;
	}
	
	public function formatted() {
		$str = 
			"<b>".$this->project_name.":</b> ".
			"<a href='".$this->ticket->url."'>Ticket #".$this->ticket->id." (".$this->ticket->summary.")</a> ".
			"assigned to <b>".(isset($this->ticket->assignee->name) ? $this->ticket->assignee->name : "nobody")."</b> ".
			"has been <b>created</b> by ".$this->ticket->reporter->name.". 
";
		if(isset($this->ticket->assignee)) {
			$str .= "<i>Assigned to:</i> ".$this->ticket->assignee->name;
			$str .= "
";
		}
		
		if(isset($this->ticket->priority)) {
			$str .= "<i>Priority:</i> ".$this->ticket->priority->name;
			$str .= "
";
		}
		
		if(isset($this->ticket->description) && strlen($this->ticket->description) > 0) {
			$str .= "<i>".$this->ticket->description."</i>";
			$str .= "
";
		}
		
		$project_name = $this->projectNameFromUrl($this->ticket->project->url);
		
		$str .= 
			'#'.$project_name.' '.
			'#'.$project_name.'_'.$this->ticket->id;
		
		return $str;
	}	
}