<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Event;
	
class ExceptionCreate extends Event {
	
	function __construct($event_json) {
		parent::__construct($event_json);
		
		$this->project_name = $this->payload()->project->name;
	}
	
	public function formatted() {
		$str = 
			"<b>".$this->project_name.":</b> ".
			"<a href='".$this->payload()->url."'>Exception raised</a>: <pre>".$this->payload()->error_message."</pre><br>".
			"in <pre>".$this->payload()->error_class."</pre>";
		
		$str .= '
';
		
		$project_name = $this->projectNameFromUrl($this->payload()->project->url);
		
		$str .= 
			'#'.$project_name;
						
		return $str;
	}	
}