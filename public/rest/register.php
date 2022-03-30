<?php

include("sanitize.inc");
include("limit.inc");
include("authenticate.inc");

include("action_get.inc");
include("action_post.inc");
include("action_put.inc");
include("action_delete.inc");

// SELECT id, name, display_name, description, address_offset, size, access, reset_value, alternate_register, reset_mask, read_action, modified_write_values, data_type
// FROM p_register INNER JOIN pl_register ON (pl_register.reg_id = p_register.id) WHERE pl_register.per_id = 1;



$req_type = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
include ("../../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
switch ($req_type) {
    case 'GET':
        if(isset($_GET["per_id"]))
        {
            $_GET["pl_register.per_id"] = $_GET["per_id"];
        }
        $opts = array(
        "sql" => "SELECT id, name, display_name, description, address_offset, size, access, reset_value, alternate_register, reset_mask, read_action, modified_write_values, data_type, alternate_group"
              . " FROM p_register INNER JOIN pl_register ON (pl_register.reg_id = p_register.id)",
        "filters" => array( "pl_register.per_id" ),
        "allowUnfiltered" => false,
        "hexColumns" => array( "address_offset", "reset_value", "reset_mask" ),
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "name",
        "table" => "p_register",
        "columns" => array( "display_name", "description", "address_offset", "size", "access", "reset_value", "alternate_register", "reset_mask", "read_action", "modified_write_values", "data_type", "alternate_group" ),
        "link_id" => "per_id",
        "lookup_table" => "pl_register",
        "lookup_reference" => "per_id",
        "lookup_added_id" => "reg_id",
        );
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id" ),
        "sql" => "SELECT id, name, display_name, description, address_offset, size, access, reset_value, alternate_register, reset_mask, read_action, modified_write_values, data_type, alternate_group FROM p_register",
        "table" => "p_register",
        "columns" => array( "name", "display_name", "description", "address_offset", "size", "access", "reset_value", "alternate_register", "reset_mask", "read_action", "modified_write_values", "data_type", "alternate_group" ),
        "int_columns" => array( "size" ),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id"),
        "sql" => "SELECT id, name, display_name, description, address_offset, size, access, reset_value, alternate_register, reset_mask, read_action, modified_write_values, data_type, alternate_group FROM p_register",
        "table" => "p_register",
        "columns" => array( "name", "display_name", "description", "address_offset", "size", "access, reset_value", "alternate_register", "reset_mask", "read_action", "modified_write_values", "data_type", "alternate_group" ),
        );
        handle_delete($pdo, $opts);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
} // end of switch
?>