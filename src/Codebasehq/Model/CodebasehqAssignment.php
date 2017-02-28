<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Model;

use \CodebasehqTelegramBot\Database\Model;
use \CodebasehqTelegramBot\Database\Connection;
use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqProject;
	
require_once 'vendor/autoload.php';
	
class CodebasehqAssignment extends Model {
	
	public $id;
	protected $codebasehq_project_id;
	protected $codebasehq_user_id;
	
	public $codebasehq_project;
	public $codebasehq_user;
	
	protected static $table_name = 'codebasehq_assignments';
	protected static $persist_properties = ['id', 'codebasehq_project_id', 'codebasehq_user_id'];
	
	
	public function __construct($properties = array(), $fetch = true) {
        
        if(isset($properties['codebasehq_project']))
        	$properties['codebasehq_project_id'] = $properties['codebasehq_project']->id;
        	
        if(isset($properties['codebasehq_user']))
        	$properties['codebasehq_user_id'] = $properties['codebasehq_user']->id;
                
        parent::__construct($properties, $fetch);
    }
    
    public function getCodebasehqProjectId() {
	    return $this->codebasehq_project_id;
    }
    
    public function getCodebasehqProject() {
	    return CodebasehqProject::fetch(['id' => $this->codebasehq_project_id]);
    }

}