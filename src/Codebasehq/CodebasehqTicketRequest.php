<?php
	
namespace CodebasehqTelegramBot\Codebasehq;

use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqUser;
use \CodebasehqTelegramBot\Telegram\Model\TelegramUser;
use \CodebasehqTelegramBot\Telegram\Model\TelegramChat;
	
use \Rmccue\Requests;
	
require_once 'vendor/autoload.php';
require_once 'config/codebasehq.php';
	
	
class CodebasehqTicketRequest {
	
	protected $codebasehq_user;
	
	function __construct($codebasehq_user) {
		$this->codebasehq_user = $codebasehq_user;
	}
	
	public function requestTickets($project_permalink, $parameters = null) {
		
		if(!isset($project_permalink))
			return false;
		
		$data = [];
		if(isset($parameters) && is_array($parameters)) {
			$parameters_string = join(' ', $parameters);
			$data = array('query' => $parameters_string);
		}
								
		$headers = $this->constructHeaders();
		$request = \Requests::request('https://api3.codebasehq.com/'.$project_permalink.'/tickets', $headers, $data, \Requests::GET);
	
		$tickets = json_decode($request->body);
		return 	
			array_map(function($ticket) { 
					return $ticket->ticket; 
				},
				$tickets
			);
	}
	
	private function constructHeaders() {
		
		$auth_basic = base64_encode(CODEBASE_ACCOUNT_NAME.'/'.$this->codebasehq_user->username.':'.$this->codebasehq_user->api_key);
		$headers = array(
			'Accept' => 'application/json',
			'Content-type' => 'application/json', 
			'Authorization' => 'Basic '.$auth_basic
		);
		return $headers;
	}
	
	
	public function requestAllMyTickets() {
		$assignments = $this->codebasehq_user->getAssignments();
		
		$tickets = [];
		foreach($assignments as $assignment) {
			$project = $assignment->getCodebasehqProject();
			$tickets = 
				array_merge($tickets, 
					$this->requestTickets($project->permalink, ['assignee:me', 'resolution:open', 'sort:priority', 'sort:desc'])
				);
		}
		
		usort($tickets, function($a, $b) {
			if ($a->priority->position == $b->priority->position)
		        return 0;
		    return ($a->priority->position < $b->priority->position) ? -1 : 1;
		});
		
		return $tickets;
	} 
	
/*
	private function requestProjects($active_only = true) {
		
		$headers = $this->constructHeaders();
		$request = \Requests::get('https://api3.codebasehq.com/projects', $headers);
	
		$projects = json_decode($request->body);
		
		$projects = array_map(
			function($project) { 
				return 
					new CodebasehqProject(
						array(
							'id' => $project->project->project_id,
							'name' => $project->project->name,
							'permalink' => $project->project->permalink,
							'status' => $project->project->status
						)
					); 
			}, 
			$projects
		);
		
		if($active_only) {
			$projects = array_filter( 
				$projects,
				function($project) { 
					return $project->status == 'active';
				}
			);
		}
		
		return $projects;
	}
*/
	
}