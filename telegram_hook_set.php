<?php
// Load composer
require_once 'vendor/autoload.php';
require_once 'config/telegram.php';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram(TELEGRAM_API_KEY, TELEGRAM_BOT_NAME);

    // Set webhook
    $result = $telegram->setWebHook(TELEGRAM_HOOK_URL);

    // Uncomment to use certificate
    //$result = $telegram->setWebHook($hook_url, $path_certificate);

    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}
