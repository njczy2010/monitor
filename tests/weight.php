<?php
class WeightTestCase extends UnitTestCase{
	private $website_backend2;
	private $website_backend3;
	private $website_proxy;

	function __construct() {
		output("Start weight test<br/>");
		$this->website_backend2 = $GLOBALS["G_CONFIG"]["url"]["backend2"];
		$this->website_backend3 = $GLOBALS["G_CONFIG"]["url"]["backend3"];
        $this->website_proxy = $GLOBALS["G_CONFIG"]["url"]["weight_test"];
	}

	function __destruct() {
		output("<br/>");
	}

	function testConnectBackendSuccess() {
		output('testConnectBackendSuccess <br/>');
		$md5_backend2 = $this->execSuccess($this->website_backend2);
		$md5_backend3 = $this->execSuccess($this->website_backend3);
		$this->assertEqual($md5_backend2,$GLOBALS["G_CONFIG"]["md5"]["backend2"]);
		$this->assertEqual($md5_backend3,$GLOBALS["G_CONFIG"]["md5"]["backend3"]);
	}

	function testWeigthSuccess() {
		output('testConnectAsyncSuccess <br/>');
		$count2 = 0;
		$count3 = 0;
		for($i = 0;$i < 20;$i++){
			$md5 = $this->execSuccess($this->website_proxy,"test_weigtht");
			if($md5 == $GLOBALS["G_CONFIG"]["md5"]["backend2"]){
				$count2++;
			}
			else{
				$count3++;
			}
		}
		$this->assertTrue($count2 >= $count3);
	}
}
?>
