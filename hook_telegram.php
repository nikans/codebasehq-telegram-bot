<?php

use CodebasehqTelegramBot\Telegram\TelegramChatHandler;
use CodebasehqTelegramBot\Telegram\TelegramCommandHandler;

require_once 'vendor/autoload.php';

$chat_handler = new TelegramChatHandler();
$chat_handler->handleInput();

$commandHandler = new TelegramCommandHandler();
$commandHandler->handleInput();

// $chat_handler->logInput();