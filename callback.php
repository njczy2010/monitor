<?php
date_default_timezone_set('prc');
$root = dirname(__file__);
$log_path = $root . '/logs';
$log_file = $log_path . '/callback.log';

$my_file = fopen($log_file, 'r+');
$txt_hello = "hello czy\n";
fwrite($my_file,$txt_hello);
echo date('Y-m-d h:i:s',time()) . "<br/>"; 
fwrite($my_file,date('Y-m-d h:i:s',time()) );
fclose($my_file);

echo $root . "\n";
?>
