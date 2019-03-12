<?php
header('Access-Control-Allow-Origin: *');
require_once("reqFilter.php");
require_once 'cache.class.php';

if (isset($_GET["url"]) && !empty($_GET["url"])) {

    $url = trim($_GET["url"]);

    // get hash of the url
    $url_hash = md5($url);

    // check if video info is cached
    $videoInfo = getVideoInfoFromCache($url_hash);
    if ($videoInfo == false) {
        $videoInfo = getVideoInfo($url);
        if ($videoInfo["title"] !== null) {
            createCache($url_hash, $videoInfo);
        }
    }

    echo json_encode($videoInfo);

} else {
    echo "-1";
}

// handle cache
function createCache($hash, $videoInfo)
{
    // setup 'default' cache
    $c = new Cache();
    $c->setCache('newcache');
    $c->store($hash, $videoInfo);
}

function getVideoInfoFromCache($hash)
{
    $c = new Cache();
    $c->setCache('newcache');
    if ($c->isCached($hash)) {
        $c = new Cache();
        $c->setCache('newcache');
        $result = $c->retrieve($hash);
        return $result;
    } else {
        return false;
    }
}

function getVideoInfo($url)
{
    $output = shell_exec("./info.sh $url");

    $all_details = json_decode($output);
    $details = array();

    if (!isset($all_details->title)) {
        return (array(
            "title" => null,
            "thumbnail" => null,
            "duration" => null,
            "formats" => null,
        ));
    }

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
                "tbr" => $f->tbr,
            );
        }
    }

    $details["formats"] = $formats;

    return ($details);
}
