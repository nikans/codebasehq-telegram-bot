<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Event;
	
class Deployment extends Event {
	
	function __construct($event_json) {
		parent::__construct($event_json);
		
		$this->project_name = $this->payload()->repository->project->name;
	}
	
	public function formatted() {
		$str = 
			"<b>".$this->project_name.":</b> ".
			"deployment from repo <i>".$this->payload()->repository->name."</i> ".
			"branch <i>".$this->payload()->branch."</i> ".
			"<b>".$this->payload()->status."</b>";
		
		$str .= '
';
		
		$project_name = $this->projectNameFromUrl($this->payload()->repository->project->url);
		$repo_name = str_replace("-", "_", $this->payload()->repository->name);
		$branch_name = str_replace("-", "_", $this->payload()->branch);
		
		$str .= 
			'#'.$project_name.' '.
			'#'.$project_name.'_'.$repo_name.'_'.$branch_name;
				
		return $str;
	}	
}