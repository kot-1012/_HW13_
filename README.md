composer require php-amqplib/php-amqplib
composer install
В consumer.php амените YOUR_TELEGRAM_BOT_TOKEN и YOUR_TELEGRAM_CHAT_ID на реальные значения.
отправить сообщение в очередь - php producer.php
получить сообщение из очереди и отправить его в Telegram - php consumer.php
