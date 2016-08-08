<?php

class HalfAsyncTestCase extends UnitTestCase {
	private $half_asyncUrl;
	private $proxyUrl;
	private $backendUrl;
	private $backendUrl2;

	private $half_asyncMD5;
	private $proxyMD5;
	private $backendMD5;
	private $backend2MD5;

	function __construct() {
		output("Starting test half async<br/>");
		$this->half_asyncUrl = $GLOBALS["G_CONFIG"]["url"]["half_async"];
		$this->proxyUrl = $GLOBALS["G_CONFIG"]["url"]["proxy_half"];
		$this->backendUrl = $GLOBALS["G_CONFIG"]["url"]["backend1"];
		$this->backendUrl2 = $GLOBALS["G_CONFIG"]["url"]["backend1_2"];
	}
	
	function __destruct() {
		output("<br/>");
	}

	function testBackendUrl() {
		output("HalfAsynch:<br/>testBackendUrl<br/>");
		//echo "this is test";
		$this->backendMD5 = $this->execSuccess($this->backendUrl);
		$this->backend2MD5 = $this->execSuccess($this->backendUrl2);
		$this->assertEqual($this->backendMD5, $this->backend2MD5);
	}

	function testYunLianUrl() {
		output("testYunLianHalfAsyncUrl<br/>");
		$this->half_asyncMD5 = $this->execSuccess($this->half_asyncUrl,"Half-Async");
	}

	function testYunLianUrl2() {
		output("testYunLianProxyUrl<br/>");
		$this->proxyMD5 = $this->execSuccess($this->proxyUrl,"Half-Async");
	}

	function testEqualResult() {
		output("testEqualResult<br/>");
		$this->assertEqual($this->proxyMD5, $this->backendMD5);
		$this->assertEqual($this->half_asyncMD5, $this->backendMD5);
	}

	/*function testAsynchWork() {
		output("testAsynchWork<br/>");
		system("nohup ab -n 15 -c 15 " . $this->half_asyncUrl . " >/dev/null &");
		sleep(1);
		$this->execSuccess($this->backendUrl);

		system("nohup ab -n 15 -c 15 " . $this->proxyUrl . " >/dev/null &");
		sleep(1);
		$this->assertEqual($this->execFail($this->backendUrl2), 503);
	}
	*/

}

?>
