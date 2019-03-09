<?php
$required = array("url", "code");
$inputs = array();
foreach ($required as $input) {
	if (!isset($_GET[$input]) || empty($_GET[$input])) {
		exit();
	}
	$inputs[$input] = trim($_GET[$input]);
}

$url = 'https://s1.navinda.xyz/youtube/download.php?url=' . $inputs["url"] . "&code=" . $inputs["code"];
 
//Use file_get_contents to GET the URL in question.
$response = file_get_contents($url);
 
//If $contents is not a boolean FALSE value.
if($response !== false){
    //Print out the contents.
    echo $response;
}
?>