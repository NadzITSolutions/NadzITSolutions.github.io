<?php 

//Database Configs;

$sql_host = "mysql7.000webhost.com";
$sql_db = "a7156588_main";
$sql_user = "a7156588_antony";
$sql_password = "Das@007";

//Path Configs;

$root = "/home/a7156588/public_html/";
$api_path = $root."api/";
$content_path = $api_path."content/";
$sessions_path = $api_path."sessions";
$tmp_path = $root.'tmp/';

//API config
$apiurl="/api/";

//Include paths

ini_set("include_path",$api_path.ini_get("include_path"));
ini_set("upload_tmp_dir", $upload_path);

//Session Path
session_save_path($sessions_path);
//HTTP Return Codes;
$success = "HTTP/1.0 200 OK";
$notfound = "HTTP/1.0 404 Not Found";
$methodnotallowed = "HTTP/1.0 405 Method Not Allowed";


?>