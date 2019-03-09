<?php
header('Access-Control-Allow-Origin: *');
require_once("reqFilter.php");
if (isset($_GET["url"]) && !empty($_GET["url"]) && isset($_GET["code"]) && !empty($_GET["code"])) {
    $url = trim($_GET["url"]);
    $code = trim($_GET["code"]);

    // check file already exist
    $md5 = md5($url . $code);

    $dir = './down';
    $files = scandir($dir);

    foreach ($files as $filename) {
        if (strpos($filename, $md5) !== false) {
			echo "https://s1.navinda.xyz/youtube/down/" . $filename;
			exit();
        }
    }

	// otherwise download file
    $output = shell_exec("./download.sh $url $code $md5");
    $arr = explode("Destination:", $output);
    echo "https://s1.navinda.xyz/youtube" . substr($arr[1], 2);
} else {
    echo "-1";
}
