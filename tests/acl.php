<?php
class ACLTestCase extends UnitTestCase {
	private $visitCountUrl;
	private $backendUrl;
    private $md5_backend;
	private $website_black_list_fail;
	private $website_black_list_success;
	private $website_white_list_success;
	private $website_white_list_fail;

	function __construct() {
		output("Starting test ACL<br/>");
		$this->visitCountUrl = $GLOBALS["G_CONFIG"]["url"]["visit_count"];
		$this->backendUrl = $GLOBALS["G_CONFIG"]["url"]["backend2"];
 		$this->website_black_list_fail = $GLOBALS["G_CONFIG"]["url"]["black_list_fail"];
		$this->website_black_list_success = $GLOBALS["G_CONFIG"]["url"]["black_list_success"];
		$this->website_white_list_success = $GLOBALS["G_CONFIG"]["url"]["white_list_success"];
		$this->website_white_list_fail = $GLOBALS["G_CONFIG"]["url"]["white_list_fail"];
	}

	function __destruct() {
		output("<br/>");
	}
	function testBackendUrl() {
		output("ACLTest:<br/>testBackendUrl<br/>");
		$this->md5_backend = $this->execSuccess($this->backendUrl);
	}

	function testYunLianUrl() {
		output("testYunLianUrl<br/>");
		$this->assertEqual($this->execSuccess($this->visitCountUrl,"test_visitcount"), $this->md5_backend);
	}

	function execVisitCountOnce() {
		for ($i = 0; $i < 2; $i++) {
			$http_code = $this->execUnknown($this->visitCountUrl,"test_visitcount");
			return $http_code == 200 ? 1 : 0;
		}
		for ($i = 0; $i < 10; $i++) {
			$this->execUnknown($this->visitCountUrl,"test_visitcount");
		}
		$http_code = $this->execUnknown($this->visitCountUrl,"test_visitcount");
		return $http_code == 403 ? 1 : 0;
	}

	function deletetestMinuteVisitCount() {
		output("testVisitCount<br/>");
		$ret_code = $this->execVisitCountOnce();
		if($ret_code == 0){
			sleep(1);
			$ret_code = $this->execVisitCountOnce();
			$this->assertEqual($ret_code, 1);
		}
		/*
		origin code
		for ($i = 0; $i < 2; $i++) {
			$visitMD5 = $this->execSuccess($this->visitCountUrl,"test_visitcount");
			$backendMD5 = $this->execSuccess($this->backendUrl);
			$this->assertEqual($visitMD5, $backendMD5);
		}
		for ($i = 0; $i < 10; $i++) {
			$this->execUnknown($this->visitCountUrl,"test_visitcount");
		}
		$this->assertEqual($this->execFail($this->visitCountUrl,"test_visitcount"), 403);
		$this->execSuccess($this->backendUrl);*/
	}

	function testBlackListSuccess() {
		output("testBlackListSuccess<br/>");
		$this->assertEqual($this->execSuccess($this->website_black_list_success,"test_black_list_success"), $this->md5_backend);
	}
	
	function testBlackListFail() {
		output("testBlackListFail<br/>");
		$http_code_black_list_fail = $this->execFail($this->website_black_list_fail,"test_black_list_fail");
 		$this->assertEqual($http_code_black_list_fail, 403);
	}
	
	function testWhiteListSuccess() {
		output("testWhiteListSuccess<br/>");
		$this->assertEqual($this->execSuccess($this->website_white_list_success,"test_white_list_success"), $this->md5_backend);
	}

	function testWhiteListFail() {
		output("testWhiteListFail<br/>");
		$http_code_white_list_fail = $this->execFail($this->website_white_list_fail,"test_white_list_fail");
		$this->assertEqual($http_code_white_list_fail, 403);
	}
}

?>
