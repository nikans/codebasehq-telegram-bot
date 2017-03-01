<?php
	
namespace CodebasehqTelegramBot\Codebasehq;

use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqUser;
use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqProject;
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
			if(count(array_filter($parameters, function($p) {
				return strpos($p, 'resolution:') === 0 || strpos($p, 'not-resolution:') === 0;
				})) == 0) 
			{
				$parameters[] = 'resolution:open';
			}
			
			$parameters_string = join(' ', $parameters);
			$data = array('query' => $parameters_string);
		}
								
		$headers = $this->constructHeaders();
		$request = \Requests::request('https://api3.codebasehq.com/'.$project_permalink.'/tickets', $headers, $data, \Requests::GET);
	
		$tickets = json_decode($request->body);
		 	
		$tickets = array_map(function($ticket) { 
					return $ticket->ticket; 
				},
				$tickets
			);
		usort($tickets, function($a, $b) {
			if ($a->priority->position == $b->priority->position)
		        return ($a->status->order <= $b->status->order) ? -1 : 1;
		    return ($a->priority->position > $b->priority->position) ? -1 : 1;
		});
		
		return $tickets;
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
	
	public function requestTicketsSearch($project_search_string = null, $search_options = null, $filter_projects = null) {
		
		if(isset($project_search_string) && strlen($project_search_string) > 0)
			$projects = CodebasehqProject::fetchFuzzyMultiple($project_search_string);
		else 
			$projects = CodebasehqProject::fetchMultiple();
			
		var_dump($project_search_string, $search_options, $projects, $filter_projects);
					
		if(isset($filter_projects) && is_array($filter_projects)) {
			$projects = array_filter($projects, function($project) use ($filter_projects) {
				return in_array($project->permalink, $filter_projects);
			});
		}
		
		var_dump($projects);
		
		$tickets = [];
		
		$tr = new CodebasehqTicketRequest($this->codebasehq_user);
		
		foreach($projects as $project) {
			$t = $tr->requestTickets($project->permalink, $search_options);
			$tickets = array_merge($tickets, $t);
		}
		return $tickets;
	}
	
	
	public function requestMyTickets($project_search_string = null, $search_options = null) {
		$assignments = $this->codebasehq_user->getAssignments();
		
		$projects = array_map(function($assignment) {
			return $assignment->getCodebasehqProject()->permalink;
		}, $assignments);
		
		$options = ['assignee:me', 'sort:priority', 'sort:desc'];
		if(isset($search_options) && is_array($search_options)) {
			array_merge($options, $search_options);
		}
		$tickets = $this->requestTicketsSearch($project_search_string, $options, $projects);
					
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