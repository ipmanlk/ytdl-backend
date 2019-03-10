<?php
$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$down_link = "https://ytdl.navinda.xyz?url=" . str_replace("get.ml",".com", $link);
header("Location: $down_link");
?>