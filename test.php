<?php
$dir    = './down';
$files1 = scandir($dir);

foreach($files1 as $filename) {
	if (strpos($filename, 'INTERVIEWS') !== false) {
		echo "./down/" . $filename;
	}	
}
?>