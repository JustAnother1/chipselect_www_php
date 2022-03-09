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
        if(isset($_GET["reg_id"]))
        {
            $_GET["pl_field.reg_id"] = $_GET["reg_id"];
        }
        $opts = array(
        "sql" => "SELECT id, name, description, bit_offset, size_bit, access, modified_write_values, read_action, reset_value"
              . " FROM p_field INNER JOIN pl_field ON (pl_field.field_id = p_field.id)",
        "filters" => array( "pl_field.reg_id" ),
        "allowUnfiltered" => false,
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "name",
        "table" => "p_field",
        "columns" => array( "description", "bit_offset", "size_bit", "access", "modified_write_values", "read_action", "reset_value" ),
        "link_id" => "reg_id",
        "lookup_table" => "pl_field",
        "lookup_reference" => "reg_id",
        "lookup_added_id" => "field_id",
        );
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id" ),
        "sql" => "SELECT id, name, description, bit_offset, size_bit, access, modified_write_values, read_action, reset_value FROM p_field",
        "table" => "p_field",
        "columns" => array( "name", "description", "bit_offset", "size_bit", "access", "modified_write_values", "read_action", "reset_value" ),
        "int_columns" => array( "bit_offset", "size_bit", "reset_value" ),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id"),
        "sql" => "SELECT id, name, description, bit_offset, size_bit, access, modified_write_values, read_action, reset_value FROM p_field",
        "table" => "p_field",
        "columns" => array( "name", "description", "bit_offset", "size_bit", "access", "modified_write_values", "read_action", "reset_value" ),
        );
        handle_delete($pdo, $opts);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
} // end of switch
?>