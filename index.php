<?php 
	
// echo "OK";

use CodebasehqTelegramBot\Codebasehq\CodebasehqTicketRequest;
use \CodebasehqTelegramBot\Codebasehq\Model\CodebasehqUser;
use \CodebasehqTelegramBot\Telegram\Model\TelegramUser;

require_once 'vendor/autoload.php';

// used for test
// $telegram_user = TelegramUser::fetch(['username' => 'inikans']);
// $codebasehq_user = $telegram_user->getCodebasehqUser();

// $tr = new CodebasehqTicketRequest($codebasehq_user);
// $t = $tr->requestAllMyTickets();
// print_r($t);