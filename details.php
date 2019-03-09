<?php
    header('Access-Control-Allow-Origin: *');
	if (isset($_GET["url"]) && !empty($_GET["url"])) {
		$url = trim($_GET["url"]);
		$output = shell_exec("./details.sh $url");
		$matches = explode("|", $output); 
		unset($matches[(sizeof($matches) - 1)]);
		
		$details = array();
		foreach($matches as $line) {
			$formats = explode("+", $line);
			$detail = array();
			if (trim($formats[2]) == "audio") {
				$detail["type"] = trim($formats[2]);
				$detail["format"] = trim($formats[1]);
				$detail["quality"] = trim($formats[3]);
				$detail["size"] = trim($formats[4]);
				$detail["code"] = trim($formats[0]);
			} else {
				$detail["type"] = "video";
				$detail["format"] = trim($formats[1]);
				$detail["quality"] = trim($formats[2]);
				$detail["size"] = trim($formats[4]);
				$detail["code"] = trim($formats[0]);
			}
			$details[] = $detail;
		}
		echo json_encode($details);
	} else {
		echo "-1";
	}
?>
