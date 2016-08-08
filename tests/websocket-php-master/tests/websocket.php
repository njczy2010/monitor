<?php

require(dirname(dirname(__FILE__)) . '/vendor/autoload.php');

use WebSocket\Client;

class WebSocketTestCase extends UnitTestCase {

	private $websocketUrl;
	private $backendUrl;
	private $redis;
	private $client;

	function __construct() {
		output("Start test Websocket~<br/>");
		$this->redis = new Redis();
		$this->redis->connect('127.0.0.1', 6379);
		$this->redis->set("last_connected", 0);
		$this->websocketUrl = $GLOBALS["G_CONFIG"]["url"]["websocket"];
		$this->backendUrl = $GLOBALS["G_CONFIG"]["url"]["backend4"];
		if (@$_GET['nginx_ip']) { 
			$array = array(
				"headers" => array(
					"host" => "2ab862678.yunlian.io",
				)
			);
			$url = 'ws://' . $_GET['nginx_ip'] . '/websocket';
			$this->client = new Client($url, $array);
		}
		else {
			$this->client = new Client($this->websocketUrl);
		}
	}

	function __destruct() {
		output("<br/>");
	}	

	function testEqualContent() {
		output("testEqualContent<br/>");
		$backendMD5 = $this->execSuccess($this->backendUrl);
		//echo $backendMD5 . "<br/>";
		$message = $this->client->receive();
		output($message . "<br/>");
		$this->assertEqual($backendMD5, md5($message));
	}

	function testCloseClient() {
		output("testCloseClient<br/>");
		$this->client->close();
		sleep(2);
		$old = $this->redis->get("last_connected");
		//echo $old . "<br />";
		$now = time();
		//echo $now . "<br/>";
		if ($now - 1 >= $old) {
			$this->assertTrue(1);
		}
		else {
			$this->assertTrue(0);
		}
	}

}

?>
