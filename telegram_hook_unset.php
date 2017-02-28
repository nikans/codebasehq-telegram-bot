<?php

require_once 'vendor/autoload.php';
require_once 'config/telegram.php';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram(TELEGRAM_API_KEY, TELEGRAM_BOT_NAME);

    // Unset webhook
    $result = $telegram->unsetWebHook();

    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}
