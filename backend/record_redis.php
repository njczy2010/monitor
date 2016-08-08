<?php
echo 'hello<br/>';
//echo date('Y-m-d H:i:s',time())."<br/>";
echo "PHP版本:".phpversion() .' <br/>';
$redis = new Redis();
$redis->connect('127.0.0.1',6379);
if (!$redis->exists("last_connected")) {
	$redis->set("last_connected", 0);
}
$redis->set("last_connected", time());
if(!$redis->exists('backend_count')){
	$redis->set('backend_count',0);
}
$backend_count = $redis->get('backend_count');
//echo 'backend_count : ' . $backend_count . '<br/>';
$backend_count++;
$redis->set('backend_count',$backend_count);
if(isset($_GET['visit'])){
	echo 'para : ' . $_GET['visit'] . '<br/>';
	$visit_string = 'backend_' . $_GET['visit'];
	//$redis->delete($visit_string);
	if($redis->exists($visit_string)){
		echo $visit_string . ' already exists<br/>';
	}
	else{
		echo $visit_string . ' not exist<br/>';
	}
	$redis->set($visit_string,1);
}
//echo 'para : ' . $_GET['visit'] . '<br/>';
//sleep(1);
?>
