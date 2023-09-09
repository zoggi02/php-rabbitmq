<?php 

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);
// print_r($channel);
// while ($channel->is_open) {
    // $channel->wait();
// }
while(count($channel->callbacks)) {
    try {
        $channel->wait(null, false, 1);
    } catch (\PhpAmqpLib\Exception\AMQPTimeoutException $e) {
        echo json_encode($e);
    }
}
?>