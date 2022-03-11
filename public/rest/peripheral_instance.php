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
        if(isset($_GET["dev_id"]))
        {
            $_GET["pl_peripheral_instance.dev_id"] = $_GET["dev_id"];
        }
        $opts = array(
        "sql" => "SELECT id, name, description, base_address, peripheral_id, disable_Condition, dev_id, per_in_id"
            . " FROM p_peripheral_instance"
            . " INNER JOIN pl_peripheral_instance ON pl_peripheral_instance.per_in_id = p_peripheral_instance.id",
        "filters" => array( "pl_peripheral_instance.dev_id"),
        "allowUnfiltered" => false,
        "hexColumns" => array( "base_address" ),
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "name",
        "table" => "p_peripheral_instance",
        "columns" => array( "description", "base_address", "peripheral_id", "disable_Condition" ),
        "link_id" => "dev_id",
        "lookup_table" => "pl_peripheral_instance",
        "lookup_reference" => "dev_id",
        "lookup_added_id" => "per_in_id",
        );
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id" ),
        "sql" => "SELECT id, name, description, base_address, peripheral_id, disable_Condition FROM p_peripheral",
        "table" => "p_peripheral_instance",
        "columns" => array( "name", "description", "base_address", "peripheral_id", "disable_Condition"),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id", "group_name" ),
        "sql" => "SELECT id, name, description, base_address, peripheral_id, disable_Condition FROM p_peripherall",
        "table" => "p_peripheral_instance",
        "columns" => array( "name", "description", "base_address", "peripheral_id", "disable_Condition" ),
        );
        handle_delete($pdo, $opts);
        break;
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
} // end of switch
?>