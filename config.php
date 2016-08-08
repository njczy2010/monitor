<?php

$G_ROOT = dirname(__FILE__);

$G_CONFIG["yunlian"] = array (
	"domain" => "2ab862678.yunlian.io",
	"domain2" => "753b57866.bsclink.com",
);

$G_CONFIG["backend"] = array (
	"root" => "http://139.196.141.219/yunlian-ops/monitor/backend",
);

$G_CONFIG["url"] = array (
	//Half_Async
	"backend1" => $G_CONFIG["backend"]["root"] . "/for_half_async1.php",
	//test_proxy_for_half
	"backend1_2" => $G_CONFIG["backend"]["root"] . "/for_half_async2.php",
	//test_ip test_ip2 test_black_list_fail test_black_list_success 
	//test_basic test_apikey test_visitcount test_weight authentication_test.php
	//test_proxy
	"backend2" => $G_CONFIG["backend"]["root"] . "/simple2.php",
	//test_weight
	"backend3" => $G_CONFIG["backend"]["root"] . "/simple3.php",
	//websocket test_async 
	"backend4" => $G_CONFIG["backend"]["root"] . "/record_redis.php",
	
	//yunlian api
	"proxy" => $G_CONFIG["yunlian"]["domain"] . "/test_proxy",
	"proxy_half" => $G_CONFIG["yunlian"]["domain"] . "/test_proxy_for_half",
	"async"	=> $G_CONFIG["yunlian"]["domain"] . "/test_async",
	"half_async" => $G_CONFIG["yunlian"]["domain"] . "/Half-Async",
	"websocket" => "ws://" . $G_CONFIG["yunlian"]["domain"] . "/websocket",
	"weight_test" => $G_CONFIG["yunlian"]["domain"] . "/test_weigtht",
	"black_list_fail" => $G_CONFIG["yunlian"]["domain"] . "/test_black_list_fail",
	"black_list_success" => $G_CONFIG["yunlian"]["domain"] . "/test_black_list_success", 
	"white_list_success" => $G_CONFIG["yunlian"]["domain"] . "/test_white_list_success",
	"white_list_fail" => $G_CONFIG["yunlian"]["domain"] . "/test_white_list_fail",
	"apikey" => $G_CONFIG["yunlian"]["domain"] . "/test_apikey",
	"basic" => $G_CONFIG["yunlian"]["domain"] . "/test_basic",
	"ipsuccess" => $G_CONFIG["yunlian"]["domain"] . "/test_ip",
	"ipfail" => $G_CONFIG["yunlian"]["domain"] . "/test_ip2",
	"visit_count" => $G_CONFIG["yunlian"]["domain"] . "/test_visitcount",
	"tunnel" => $G_CONFIG["yunlian"]["domain2"] . "/test_tunnel" . "/yunlian-ops/monitor/backend/simple2.php",
);

$G_CONFIG["md5"] = array (
	"backend2" => 'cd1e6cc0c6357a264740dda1d5b43a96',
	"backend3" => 'b3b2472d7a7d3e97a3bced1b0b1c5a45',
	"async" => 'dc464f2d92c7806d8280dd3e6c10c037',
);

$G_CONFIG["para"] = array (
	"apikey" => "akhwrovuc2r987bypa2gbnnks40g2ssdvgfiwxg9",
	"apikey_wrong" => "akhwrovuc2r987bypa2gbnnks40g2ssdvgfiwxg9_wrong",
);

$G_CONFIG["basic"] = array (
	"username" => "njczy2010",
	"password" => "baishan123",
	"password_wrong" => "baishan456",
);

$G_CONFIG["tunnel"] = array (
	"cmd_install" => "curl -sSL http://get.hehecloud.com/yunlian/install.sh | sudo sh -s 177bfe50-3-5772600f-478-c001d8abb5 80",
	"cmd_uninstall" => "curl -sSL http://get.hehecloud.com/yunlian/uninstall.sh | sudo sh",
);
//echo $G_CONFIG["url"]["proxy"] . "\n";
?>
