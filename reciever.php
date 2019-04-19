<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('new', false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (PhpAmqpLib\Message\AMQPMessage $msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
    $msg->get('channel')->basic_ack($msg->get('delivery_tag'));
};

$tag = $channel->basic_consume('hello', '', false, false, false, false, $callback);

$channel->basic_get();

while (count($channel->callbacks)) {
    $channel->wait();
}
