<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

// Настройки Telegram
$telegramBotToken = 'telegramBotToken';
$telegramChatID = 'telegramChatID';

// Функция для отправки сообщения в Telegram
function sendToTelegram($message, $botToken, $chatID) {
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $data = [
        'chat_id' => $chatID,
        'text' => $message,
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

// Подключение к RabbitMQ
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Объявление очереди
$queueName = 'My_telegram_queue';
$channel->queue_declare($queueName, false, false, false, false);

echo " [*] Ожидание сообщений. Для выхода нажмите CTRL+C\n";

$callback = function ($msg) use ($telegramBotToken, $telegramChatID) {
    echo ' [x] Получено ', $msg->body, "\n";
    sendToTelegram($msg->body, $telegramBotToken, $telegramChatID);
    echo " [x] Сообщение отправлено в Telegram\n";
};

// Получение сообщений из очереди
$channel->basic_consume($queueName, '', false, true, false, false, $callback);

// Бесконечный цикл для ожидания сообщений
while (count($channel->callbacks)) {
    $channel->wait();
}

// Закрытие соединения
$channel->close();
$connection->close();