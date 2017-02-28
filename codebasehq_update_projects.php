<?php

use \CodebasehqTelegramBot\Codebasehq\CodebasehqProjectsUpdater;

require_once 'vendor/autoload.php';

$u = new CodebasehqProjectsUpdater();
$projects = $u->updateProjects();
$users = $u->updateUsers();
$assignments = $u->updateAssignments($projects);

if($assignments !== false && is_array($assignments)) 
	echo "Success";

// var_dump($u->requestUsers(true));