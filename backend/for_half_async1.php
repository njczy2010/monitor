<?php
$file = "temp.txt";    
$max_concurrent = 10;
$sleep_time = 3 ;
$fp = fopen($file , 'r+');
if(flock($fp , LOCK_EX)){
	fseek($fp,0) ;
	$concurrency = fread($fp , 100);
	$concurrency = (int)$concurrency;
	if($concurrency >= $max_concurrent){
		flock($fp , LOCK_UN);
		fclose($fp);
		//sleep(2);
		//usleep(mt_rand(100000,1500000));
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		exit(0) ;
	}
	ftruncate($fp,0);
	fseek($fp,0) ;
	$concurrency = $concurrency+1 ;
	fwrite($fp,sprintf("%d",$concurrency));
	flock($fp , LOCK_UN);
}else{
	echo "lock failed" ;
	exit(0) ;
}
fclose($fp);

//sleep($sleep_time) ;

$fp = fopen($file , 'r+');
if(flock($fp , LOCK_EX)){
	fseek($fp,0) ;
	$concurrency = fread($fp , 100);
	ftruncate($fp,0);
	fseek($fp,0) ;
	$concurrency = (int)$concurrency;
	$concurrency = $concurrency-1 ;
	if($concurrency<0){
		$concurrency = 0 ;
	}
	fwrite($fp,sprintf("%d",$concurrency));
	flock($fp , LOCK_UN);
}else{
	echo "lock failed" ;
	exit(0) ;
}
fclose($fp);
echo "test page" ;
?>
