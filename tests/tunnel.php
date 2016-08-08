<?php
class TunnelTestCase extends UnitTestCase{
	private $website_backend;
	private $md5_backend;

	private $website_tunnel;
	private $md5_proxy;
	
	function __construct() {
		output("Start test tunnel<br/>");
		$this->website_backend = $GLOBALS["G_CONFIG"]["url"]["backend2"];
		$this->website_tunnel = $GLOBALS["G_CONFIG"]["url"]["tunnel"];
	}

	function __destruct() {
		output("<br/>");
	}

	function testConnectBackendSuccess() {
		output('testConnectBackendSuccess <br/>');
		$this->md5_backend = $this->execSuccess($this->website_backend);
	}

	function testTunnelSuccess() {
		output('testTunnelSuccess <br/>');
		$this->md5_tunnel = $this->execSuccess($this->website_tunnel,"test_tunnel/yunlian-ops/monitor/backend/simple2.php");
	}

	function testCompareBackendWithProxy(){
		output('testCompareBackendWithProxy<br/>');
		$this->assertEqual($this->md5_backend,$this->md5_tunnel);
	}
}
?>
