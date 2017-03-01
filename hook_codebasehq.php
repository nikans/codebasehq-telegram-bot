<?php

use CodebasehqTelegramBot\Codebasehq\CodebasehqEventHandler;
use CodebasehqTelegramBot\Telegram\TelegramSubscriptionsHandler;

require_once 'vendor/autoload.php';

$event_raw = file_get_contents("php://input");

$event_handler = new CodebasehqEventHandler($event_raw);

// $event_handler->logRaw();
// $event_handler->logEvent();

TelegramSubscriptionsHandler::sendMessagesToProjectAssignees($event_handler->event->formatted(), $event_handler->event->project_name, [$event_handler->event->codebasehq_subject_username], [$event_handler->event->codebasehq_actor_username]);

