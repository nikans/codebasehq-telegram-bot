<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Structure;

use CodebasehqTelegramBot\Codebasehq\Model\CodebasehqProject;

require_once 'vendor/autoload.php';
require_once 'config/codebasehq.php';

class Ticket {
	
	private $json;
	private $ticket;
	private $project;
	private $url;
	
	function __construct($json) {
		$this->json = $json;
		$this->ticket = $json;
		$this->project = CodebasehqProject::fetch(['id' => $this->ticket->project_id]);
		$this->url = "https://".CODEBASE_ACCOUNT_NAME.".codebasehq.com/projects/".$this->project->permalink."/tickets/".$this->ticket->ticket_id;
	}
	
	public function formatted($me = false) {
		$str = 
			"<b>".$this->project->name.":</b> ".
			"<a href='".$this->url."'>Ticket #".$this->ticket->ticket_id." (".$this->ticket->summary.")</a> "."
";		
		if(isset($this->ticket->priority)) {
			$str .= "<i>Priority:</i> ".$this->ticket->priority->name;
			$str .= "
";
		}
		
		if(!$my && isset($this->ticket->assignee)) {
			$str .= "<i>Assigned to:</i> ".$this->ticket->assignee;
			$str .= "
";
		}
		
		$project_name = str_replace("-", "_", $this->project->permalink);
		
		$str .= 
			'#'.$project_name.' '.
			'#'.$project_name.'_'.$this->ticket->ticket_id;
				
		if($my)
			$str = "‼️ ".$str." #my_assigned";
		
		return $str;
	}
	
	public function prettyJson() {
		return json_encode($this->json, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
	}
	
}