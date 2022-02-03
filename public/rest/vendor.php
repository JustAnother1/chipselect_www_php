<?php

include("sanitize.inc");
include("limit.inc");
include("authenticate.inc");

include("action_get.inc");
include("action_post.inc");
include("action_put.inc");
include("action_delete.inc");

$req_type = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
include ("../../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
switch ($req_type) {
    case 'GET':
        $opts = array(
        "sql" => "SELECT id, name, url, alternative FROM p_vendor",
        "filters" => array( "id", "name"),
        "allowUnfiltered" => true,
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "name",
        "table" => "p_vendor",
        "columns" => array( "url" ),
        );
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id", "name" ),
        "sql" => "SELECT id, name, alternative, url FROM p_vendor",
        "table" => "p_vendor",
        "columns" => array( "name", "url", "alternative" ),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id", "name" ),
        "sql" => "SELECT id, name, alternative, url FROM p_vendor",
        "table" => "p_vendor",
        "columns" => array( "name", "alternative", "url" ),
        );
        handle_delete($pdo, $opts);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
} // end of switch
?>