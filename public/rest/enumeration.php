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
        if(isset($_GET["field_id"]))
        {
            $_GET["pl_enumeration.field_id"] = $_GET["field_id"];
        }
        $opts = array(
        "sql" => "SELECT id, name, usage_right"
              . " FROM p_enumeration INNER JOIN pl_enumeration ON (pl_enumeration.enum_id = p_enumeration.id)",
        "filters" => array( "pl_enumeration.field_id" ),
        "allowUnfiltered" => false,
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "name",
        "table" => "p_enumeration",
        "columns" => array( "usage_right" ),
        "link_id" => "field_id",
        "lookup_table" => "pl_enumeration",
        "lookup_reference" => "field_id",
        "lookup_added_id" => "enum_id",
        );
        if(!isset($_GET["name"]))
        {
            $_GET["name"] = "NULL";
        }
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id" ),
        "sql" => "SELECT id, name, usage_right FROM p_enumeration",
        "table" => "p_enumeration",
        "columns" => array( "name", "usage_right" ),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id"),
        "sql" => "SELECT id, name, usage_right FROM p_enumeration",
        "table" => "p_enumeration",
        "columns" => array( "name", "usage_right" ),
        );
        handle_delete($pdo, $opts);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
} // end of switch
?>