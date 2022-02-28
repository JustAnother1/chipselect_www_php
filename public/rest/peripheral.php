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
        "sql" => "SELECT id, group_name FROM p_peripheral",
        "filters" => array( "id", "group_name"),
        "allowUnfiltered" => false,
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "group_name",
        "table" => "p_peripheral",
        "columns" => array(  ),
        );
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id" ),
        "sql" => "SELECT id, group_name FROM p_peripheral",
        "table" => "p_peripheral",
        "columns" => array( "name" ),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id", "group_name" ),
        "sql" => "SELECT id, group_name FROM p_peripheral",
        "table" => "p_peripheral",
        "columns" => array( "p_peripheral" ),
        );
        handle_delete($pdo, $opts);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
} // end of switch
?>