<?php

include("sanitize.inc");
include("limit.inc");
include("authenticate.inc");

include("action_get.inc");
include("action_post.inc");
include("action_put.inc");
include("action_delete.inc");

$req_type = $_POST['REQUEST_METHOD'];
header('Content-Type: application/json');
include ("../../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
switch ($req_type) {
    case 'GET':
        if(isset($_POST["enum_id"]))
        {
            $_POST["pl_enumeration_element.enum_id"] = $_POST["enum_id"];
        }
        $opts = array(
        "sql" => "SELECT id, name, description, value, isDefault"
              . " FROM p_enumeration_element INNER JOIN pl_enumeration_element ON (pl_enumeration_element.value_id = p_enumeration_element.id)",
        "filters" => array( "pl_enumeration_element.enum_id" ),
        "allowUnfiltered" => false,
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "name",
        "table" => "p_enumeration_element",
        "columns" => array( "description", "value" , "isDefault" ),
        "link_id" => "enum_id",
        "lookup_table" => "pl_enumeration_element",
        "lookup_reference" => "enum_id",
        "lookup_added_id" => "value_id",
        );
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id" ),
        "sql" => "SELECT id, name, description, value, isDefault FROM p_enumeration_element",
        "table" => "p_enumeration_element",
        "columns" => array( "name", "description", "value", "isDefault" ),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id"),
        "sql" => "SELECT id, name, description, value, isDefault FROM p_enumeration_element",
        "table" => "p_enumeration_element",
        "columns" => array( "name", "description", "value", "isDefault" ),
        );
        handle_delete($pdo, $opts);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
} // end of switch

$pdo = null;
?>