<?php

require(dirname(dirname(__FILE__)) . '/vendor/autoload.php');

use WebSocket\Client;

$client = new Client("ws://2ab862678.yunlian.io/websocket");

echo $client->receive();

//$client2 = new Client("ws://2ab862678.yunlian.io/websocket");

//echo $client2->receive();


?>
