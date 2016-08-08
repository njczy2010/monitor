<?php
class AuthenticateTestCase extends UnitTestCase {
	//ip
	private $IPsuccessUrl;
	private $IPfailUrl;
	private $backendUrl;
	private $backendMD5;
	
	//apikey
	private $website_apikey;
	private $url_apikey1;
	private $url_apikey2;
	private $url_apikey1_wrong;
	
	private $md5_apikey1;
	private $md5_apikey2;
	private $apikey;
	
	private $apikey_wrong;
	private $http_code_apikey;
	private $http_code_apikey_wrong1;
	private $http_code_apikey_wrong2;
	
	//basic
	private $website_basic;
	private $md5_basic;
 	private $username;
	private $password;
	private $password_wrong;
 	private $http_code_basic_wrong;

	function __construct() {
		output("Start test authenticate<br/>");
		$this->IPsuccessUrl = $GLOBALS["G_CONFIG"]["url"]["ipsuccess"];
		$this->IPfailUrl = $GLOBALS["G_CONFIG"]["url"]["ipfail"];
		$this->backendUrl = $GLOBALS["G_CONFIG"]["url"]["backend2"];

		$this->website_apikey = $GLOBALS["G_CONFIG"]["url"]["apikey"];
		$this->apikey = $GLOBALS["G_CONFIG"]["para"]["apikey"];
		$this->apikey_wrong = $GLOBALS["G_CONFIG"]["para"]["apikey_wrong"];
	
		//apikey
		$this->url_apikey1 = $this->website_apikey . "?apikey=$this->apikey";
		$this->url_apikey1_wrong = $this->website_apikey . "?apikey=$this->apikey_wrong";
	
		//basic
		$this->website_basic = $GLOBALS["G_CONFIG"]["url"]["basic"];
		$this->username = $GLOBALS["G_CONFIG"]["basic"]["username"];
		$this->password = $GLOBALS["G_CONFIG"]["basic"]["password"];
		$this->password_wrong = $GLOBALS["G_CONFIG"]["basic"]["password_wrong"];
	}

	function __destruct() {
		output("<br/>");
	}

	function testBackendUrl() {
		output("testBackendUrl<br/>");
		$this->backendMD5 = $this->execSuccess($this->backendUrl);
	}

	//APIKey
	function testConnectAPIKeySuccess() {
		output("testConnectAPIKeySuccess<br/>");
		$this->md5_apikey1 = $this->execSuccess($this->url_apikey1,"test_apikey","?apikey=$this->apikey");
		output("aaaa<br/>");
		$this->md5_apikey2 = $this->execSuccess($this->website_apikey,"test_apikey",false,array ("x-auth-apikey: $this->apikey"));
	}
	
	function testConnectAPIKeyFail() {
		output("testConnectAPIKeyFail<br/>");
		$this->http_code_apikey = $this->execFail($this->website_apikey,"test_apikey");
		$this->http_code_apikey_wrong1 = $this->execFail($this->url_apikey1_wrong,"test_apikey","?apikey=$this->apikey_wrong");
		$this->http_code_apikey_wrong2 = $this->execFail($this->website_apikey,"test_apikey",false,array ("x-auth-apikey: $this->apikey_wrong"));
		$this->assertEqual($this->http_code_apikey,401);
		$this->assertEqual($this->http_code_apikey_wrong1,401);
		$this->assertEqual($this->http_code_apikey_wrong2,401);
	}
	
	//Basic
	function execBasicSuccess($url,$path = false,$username,$password) {
		$handler = curl_init();
		$this->assertTrue($handler);
		curl_setopt($handler, CURLOPT_CONNECTTIMEOUT_MS, 3000);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
		if($path) {
			if (@$_GET['nginx_ip']) {
				curl_setopt($handler, CURLOPT_HTTPHEADER, array("Host: " . $GLOBALS["G_CONFIG"]["yunlian"]["domain"]));
				$url = $_GET['nginx_ip'] . '/' . $path;
			}
		}
		curl_setopt($handler, CURLOPT_URL, $url);
		curl_setopt($handler, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
		curl_setopt($handler, CURLOPT_USERPWD, "$username:$password");
		$result = curl_exec($handler);
		$this->assertTrue($result);
		$this->md5 = md5($result);
 		$retInfo = curl_getinfo($handler);
		$this->assertTrue($retInfo);
		$this->assertEqual($retInfo["http_code"], 200);
		curl_close($handler);
		return $this->md5;
	}

	function execBasicFail($url,$path = false,$username,$password) {
		$handler = curl_init();
		$this->assertTrue($handler);
		curl_setopt($handler, CURLOPT_CONNECTTIMEOUT_MS, 3000);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
		if($path) {
			if (@$_GET['nginx_ip']) {
				curl_setopt($handler, CURLOPT_HTTPHEADER, array("Host: " . $GLOBALS["G_CONFIG"]["yunlian"]["domain"]));
				$url = $_GET['nginx_ip'] . '/' . $path;
			}
		}
		curl_setopt($handler, CURLOPT_URL, $url);
		curl_setopt($handler, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
		curl_setopt($handler, CURLOPT_USERPWD, "$username:$password");
		$result = curl_exec($handler);
		$retInfo = curl_getinfo($handler);
		curl_close($handler);
		return $retInfo["http_code"];
	}

	function testBasicSuccess() {
		output("testBasicSuccess<br/>");
		$this->md5_basic = $this->execBasicSuccess($this->website_basic,"test_basic",$this->username,$this->password);
	}
	
	function testBasicFail() {
		output("testBasicFail<br/>");
		$this->http_code_basic_wrong = $this->execBasicFail($this->website_basic,"test_basic",$this->username,$this->password_wrong);
		$this->assertEqual($this->http_code_basic_wrong,401);
	}
	
	function testCompareWirhBackend(){
		output("testCompareWirhBackend<br/>");
		$this->assertEqual($this->backendMD5,$this->md5_apikey1);
		$this->assertEqual($this->backendMD5,$this->md5_apikey2);
		$this->assertEqual($this->backendMD5,$this->md5_basic);
	}
	
	//ip
	function testSuccessUrl() {
		output("testIPSuccessUrl<br/>");
		$this->assertEqual($this->execSuccess($this->IPsuccessUrl,"test_ip"), $this->backendMD5);
	}

	function testFailUrl() {
		output("testIPFailUrl<br/>");
		$this->assertEqual($this->execFail($this->IPfailUrl,"test_ip2"), 401);
	}

	function execPost($url,$path = false, $para = false) {
		$handler = curl_init();
		$this->assertTrue($handler);
		$data = "test date";
		curl_setopt($handler, CURLOPT_CONNECTTIMEOUT_MS, 3000);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
		if($path) {
			if (@$_GET['nginx_ip']) {
				curl_setopt($handler, CURLOPT_HTTPHEADER, array("Host: " . $GLOBALS["G_CONFIG"]["yunlian"]["domain"]));
				$url = $_GET['nginx_ip'] . '/' . $path;
				if($para) {
					$url .= $para;
				}
			}
		}
		output("$url<br/>");
		curl_setopt($handler, CURLOPT_URL, $url);
		curl_setopt($handler, CURLOPT_POST, 1);
		curl_setopt($handler, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($handler);
		//$this->assertTrue($result);
		$retInfo = curl_getInfo($handler);
		$this->assertTrue($retInfo);
		return $retInfo["http_code"];
	}
	//readOnly
	function testReadOnly() {
		output("testReadOnly<br/>");
		$this->assertEqual($this->execPost($this->IPsuccessUrl,"test_ip"), 405);
	}

	function  testReadAndWrite() {
		output("testReadAndWrite<br/>");
		$this->assertEqual($this->execPost($this->url_apikey1,"test_apikey","?apikey=$this->apikey"), 200);
	}
}

?>
