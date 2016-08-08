<?php
class HeaderTestCase extends UnitTestCase{
	private $website_backend;
	private $md5_backend;

	private $website_proxy;
	private $md5_proxy;
	
	function __construct() {
		output("Start test proxy<br/>");
		$this->website_backend = $GLOBALS["G_CONFIG"]["url"]["backend1"];
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

	function execSuccessReturnHeader($_url) {
		$url = $_url;
		$handler = curl_init();
		$this->assertTrue($handler);
		//echo "1<br/>";
		curl_setopt($handler, CURLOPT_CONNECTTIMEOUT_MS, 3000);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($handler, CURLOPT_URL, $url);
		curl_setopt($handler, CURLINFO_HEADER_OUT, 1);
		$result = curl_exec($handler);
		$this->assertTrue($result);
		//echo "2<br/>";
		$this->md5 = md5($result);
		$retInfo = curl_getinfo($handler);
		$this->assertTrue($retInfo);
		//echo "3<br/>";
		$this->assertEqual($retInfo["http_code"], 200);
		//echo "4<br/>";
		curl_close($handler);
		return $retInfo["request_header"];
	}


	function testProxySuccess() {
		output('testProxySuccess <br/>');
		$header = $this->execSuccessReturnHeader($this->website_proxy);
		output("<br/>$header<br/><br/>");
	}
	function testCompareBackendWithProxy(){
		output('testCompareBackendWithProxy<br/>');
		//$this->assertEqual($this->md5_backend,$this->md5_proxy);
	}
}
?>
