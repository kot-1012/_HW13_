<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Подключение к RabbitMQ
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Объявление очереди
$queueName = 'My_telegram_queue';
$channel->queue_declare($queueName, false, false, false, false);

// Сообщение для отправки
$messageBody = 'Привет, это тестовое сообщение!';
$msg = new AMQPMessage($messageBody);

// Отправка сообщения в очередь
$channel->basic_publish($msg, '', $queueName);

echo " [x] Отправлено '$messageBody'\n";

// Закрытие соединения
$channel->close();
$connection->close();