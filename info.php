<?php
header('Access-Control-Allow-Origin: *');
if (isset($_GET["url"]) && !empty($_GET["url"])) {
    $url = trim($_GET["url"]);
    $output = shell_exec("./info.sh $url");

    $all_details = json_decode($output);
    $details = array();

    $details["title"] = $all_details->title;
    $details["thumbnail"] = $all_details->thumbnail;
    $details["duration"] = $all_details->duration;
    $all_formats = $all_details->formats;

    // extract formats
    $formats = array();
    foreach ($all_formats as $f) {
        if ($f->acodec !== "none" || $f->format_note == "DASH audio") {
			if (!$f->filesize) {
				$filesize = "best";
			} else {
				$filesize = $f->filesize;
			}
            $formats[$f->format_id] = array(
                "format" => $f->format,
                "ext" => $f->ext,
                "filesize" => $filesize,
                "url" => $f->url,
                "tbr" => $f->tbr
            );
        }
    }

    $details["formats"] = $formats;

    echo json_encode($details);
} else {
    echo "-1";
}
