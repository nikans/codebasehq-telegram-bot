<?php
	
namespace CodebasehqTelegramBot\Codebasehq\Model;

use \CodebasehqTelegramBot\Database\Model;
use \CodebasehqTelegramBot\Database\Connection;
	
require_once 'vendor/autoload.php';
	
class CodebasehqProject extends Model {
	
	public $id;
	public $name;
	public $permalink;
	public $status;
	
	protected static $table_name = 'codebasehq_projects';
	protected static $persist_properties = ['id', 'name', 'permalink'];
	protected static $fuzzy_search_properties = ['name', 'permalink'];
}