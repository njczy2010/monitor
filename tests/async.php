<?php
class AsyncTestCase extends UnitTestCase{
	private $redis;
	private $callback_count;
	private $backend_count;

	private $website_backend;
	private $website_async;
	private $md5_async;

	function __construct() {
		output("Start test async<br/>");
		$this->website_backend = $GLOBALS["G_CONFIG"]["url"]["backend4"];
        $this->website_async = $GLOBALS["G_CONFIG"]["url"]["async"];
		$this->redis = new Redis();
		$this->redis->connect('127.0.0.1',6379);
		$this->redis->set("callback_count",0);
		$this->redis->set("backend_count",0);
		$this->md5_async = $GLOBALS["G_CONFIG"]["md5"]["async"];
	}

	function __destruct(){
		output("<br/>");
	}

	function testConnectBackendSuccess() {
		output('testConnectBackendSuccess <br/>');
		$this->execSuccess($this->website_backend);
	}

	function testConnectAsyncSuccess() {
		output("testConnectAsyncSuccess <br/>");
		$md5 = $this->execSuccess($this->website_async,"test_async");
		$this->assertEqual($md5,$this->md5_async);
	}

	function testCallbackSuccess(){
		output("testCallbackSuccess <br/>");
		for ($i = 0;$i < 5;$i++){
			$visit_string = 'backend_' . $i;
			$this->redis->delete($visit_string);
			$this->execSuccess($this->website_async . '?visit=' . $i,"test_async","?visit=$i");
		}
		sleep(3);

		for($i = 0;$i < 5;$i++){
			$visit_string = 'backend_' . $i;
			$this->assertTrue($this->redis->exists($visit_string));
		}
	}
}
?>
