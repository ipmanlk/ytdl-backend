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
        // check if video permissions are good

        $videoInfo = getVideoInfo($url);

        $downloadable = $videoInfo["checkurl"];
        
        unset($videoInfo["checkurl"]);

        if ($downloadable) {
            $videoInfo["status"] = true;
        } else {
            $videoInfo["status"] = false;
        }
        
        // create cache
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
    // store weather video is downlodable or not
    $details["checkurl"]  = true;

    // extract formats
    $formats = array();


    foreach ($all_formats as $f) {
        if (!$f->filesize) {
            $filesize = "best";
        } else {
            $filesize = $f->filesize;
        }

        if (($f->acodec == "none" || $f->format_note !== "DASH audio")) {
            $f->format_id = $f->format_id . "~251";
        }    

        $formats[$f->format_id] = array(
            "format" => $f->format,
            "ext" => $f->ext,
            "filesize" => $filesize,
            "url" => $f->url,
            "tbr" => $f->tbr,
        );

        // url for checking remote file exists / permissions
        $details["checkurl"] = $details["checkurl"]  && remoteFileExists($f->url);
    }

    $details["formats"] = $formats;

    return ($details);
}


function remoteFileExists($url) {
    $curl = curl_init($url);

    //don't fetch the actual page, you only want to check the connection is ok
    curl_setopt($curl, CURLOPT_NOBODY, true);

    //do request
    $result = curl_exec($curl);

    $ret = false;

    //if request did not fail
    if ($result !== false) {
        //if request was ok, check response code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  

        if ($statusCode == 200) {
            $ret = true;   
        }
    }

    curl_close($curl);

    return $ret;
}

