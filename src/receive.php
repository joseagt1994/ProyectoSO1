<?php
/**
 * Created by PhpStorm.
 * User: bizmate
 * Date: 10/02/2018
 * Time: 20:59
 */

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, true, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

// recibe msg como {"canal": 1000,"msg":"mensaje"}
$callback = function($msg) {
    $waitSeconds = rand(15,30);
    echo " [x] Host: " . getenv('HOSTNAME') ." Waiting: " . $waitSeconds . " seconds. Received msg: ", $msg->body, "\n";
    //sleep(substr_count($msg->body, '.'));
    sleep($waitSeconds);
    // Convertir a array json
    $valores = json_decode($msg->body,true);
    // Conectar a redis
    $redis = new Redis(); 
    $redis->connect('192.168.1.7', 6379); // se cambia la IP del servidor local
    if($valores["canal"] != "1000"){
	$canal = "usr:".$valores["canal"];
    	$redis->lpush($canal, $valores["msg"]);
    }
    $redis->lpush("principal:1000", $valores["msg"]); 
    echo "Guardado en Redis!", "\n";
    // Conectar a mongo
    $m = new MongoDB\Driver\Manager("mongodb://192.168.1.7:27017"); // se cambia la IP del servidor local
    $command = new MongoDB\Driver\Command(array('eval' => "db.feeds.insert(".$msg->body.")"));
    $cursor = $m->executeCommand('Proyecto', $command);
    echo "Guardado en MongoDB!", "\n";
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('hello', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}
