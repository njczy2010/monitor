<?php
class ProxyTestCase extends UnitTestCase{
	private $website_backend;
	private $md5_backend;

	private $website_proxy;
	private $md5_proxy;
	
	function __construct() {
		output("Start test proxy<br/>");
		$this->website_backend = $GLOBALS["G_CONFIG"]["url"]["backend2"];
		$this->website_proxy = $GLOBALS["G_CONFIG"]["url"]["proxy"];
	}

	function __destruct() {
		output("<br/>");
	}

	function testConnectBackendSuccess() {
		output('testConnectBackendSuccess <br/>');
		$this->assertEqual(1 + 1, 2);
		$this->md5_backend = $this->execSuccess($this->website_backend);
	}

	function testProxySuccess() {
		output('testProxySuccess <br/>');
		$this->md5_proxy = $this->execSuccess($this->website_proxy,"test_proxy");
	}

	function testCompareBackendWithProxy(){
		output('testCompareBackendWithProxy<br/>');
		$this->assertEqual($this->md5_backend,$this->md5_proxy);
	}
}
?>
