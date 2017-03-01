<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Event;
	
class Push extends Event {
	
	function __construct($event_json) {
		parent::__construct($event_json);
		
		$this->project_name = $this->payload()->repository->project->name;
		
		if(isset($this->payload()->user) && isset($this->payload()->user->username))
			$this->codebasehq_actor_username = $this->payload()->user->username;
	}
	
	public function formatted() {
		$commits_count = count($this->payload()->commits);
		
		$str = 
			"<b>".$this->project_name.":</b> ".
			$this->payload()->user->name." ".
			($commits_count > 0 ? "pushed ".$commits_count." commit".($commits_count > 1 ? "s" : "")." to " : "merged something in ").
			"<a href='".$this->payload()->repository->url."'>".$this->payload()->repository->name."</a> (".$this->payload()->ref.").";
		
		$str .= '
';
		
		$project_name = $this->projectNameFromUrl($this->payload()->repository->project->url);
		$repo_name = str_replace("-", "_", $this->payload()->repository->name);
		$branch_name = str_replace("-", "_", $this->payload()->ref);
		
		$str .= 
			'#'.$project_name.' '.
			'#'.$project_name.'_'.$repo_name.'_'.$branch_name;
					
		return $str;
	}	
}