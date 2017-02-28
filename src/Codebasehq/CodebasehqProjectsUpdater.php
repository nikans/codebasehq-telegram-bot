<?php

namespace CodebasehqTelegramBot\Codebasehq;

use \CodebasehqTelegramBot\Database\Connection;
use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqProject;
use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqAssignment;
use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqUser;
use \Rmccue\Requests;

require_once 'vendor/autoload.php';
require_once 'config/codebasehq.php';


class CodebasehqProjectsUpdater {

	private $connection;


	function __construct() {
		$connection = Connection::getInstance();
	}

	private function constructHeaders() {
		
		$auth_basic = base64_encode(CODEBASE_ACCOUNT_NAME.'/'.CODEBASE_USER_NAME.':'.CODEBASE_API_KEY);
		$headers = array(
			'Accept' => 'application/json',
			'Content-type' => 'application/json', 
			'Authorization' => 'Basic '.$auth_basic
		);
		return $headers;
	}
	
	
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
	
	
	function updateProjects() {
		$projects = $this->requestProjects();
		CodebasehqProject::deleteMultiple();
		CodebasehqProject::insertMultiple($projects);
		
		return $projects;
	}
	
	function updateAssignments($projects = null) {
				
		if(!isset($projects)) {
			$projects = CodebasehqProject::fetchMultiple();
		}
				
		$headers = $this->constructHeaders();
		
		$assignments = [];
		$users = CodebasehqUser::fetchMultiple();
		
		foreach($projects as $project) {
			$request = \Requests::get('https://api3.codebasehq.com/'.$project->permalink.'/assignments', $headers);
			
			$assignments_json = json_decode($request->body, true);
						
			if(!is_array($assignments_json))
				continue;

			foreach($assignments_json as $user_json) {
								
				$user = new CodebasehqUser($user_json['user']);
				
				$searchedUserId = $user->id;
				$foundUser = array_filter(
				    $users,
				    function ($u) use (&$searchedUserId) {
				        return $u->id == $searchedUserId;
				    }
				);
				
				if(count($foundUser) == 0)
					continue;
								
				$project_assignments[] = 
					new CodebasehqAssignment(
						['codebasehq_project' => $project, 'codebasehq_user' => $user]
					);
			}
						
			$assignments[$project->id] = $project_assignments;
		}
		
		CodebasehqAssignment::deleteMultiple();
				
		if(!is_array($assignments)) 
			return false;
			
		$assignments = call_user_func_array('array_merge', $assignments);
		CodebasehqAssignment::insertMultiple($assignments);
		
		return $assignments;
		
	}
	
	
	private function requestUsers($active_only = true) {
		
		$headers = $this->constructHeaders();
		$request = \Requests::get('https://api3.codebasehq.com/users', $headers);
	
		$users = json_decode($request->body);
				
// 				var_dump($users);

				
		$users = array_map(
			function($user) { 
				return 
					new CodebasehqUser(
						array(
							'id' => $user->user->id,
							'username' => $user->user->username,
							'first_name' => $user->user->first_name,
							'last_name' => $user->user->last_name,
							'email_address' => $user->user->email_address,
							'api_key' => $user->user->api_key,
							'enabled' => $user->user->enabled,
							'company' => $user->user->company
						)
					); 
			}, 
			$users
		);
		
		if($active_only) {
			$users = array_filter( 
				$users,
				function($user) { 
					return $user->enabled == true;
				}
			);
		}
		
		return $users;
	}
	
	function updateUsers() {
		$users = $this->requestUsers();
		CodebasehqUser::deleteMultiple();
		CodebasehqUser::insertMultiple($users);
		
		return $users;
	}

}