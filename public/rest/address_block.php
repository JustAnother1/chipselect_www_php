<?php

include("sanitize.inc");
include("limit.inc");
include("authenticate.inc");

include("action_get.inc");
include("action_post.inc");
include("action_put.inc");
include("action_delete.inc");

// SELECT id, address_offset, size, mem_usage protection FROM p_address_block INNER JOIN pl_address_block ON (pl_address_block.addr_id = p_address_block.id) WHERE pl_address_block.per_id = 1;

$req_type = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
include ("../../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
switch ($req_type) {
    case 'GET':
        if(isset($_GET["per_id"]))
        {
            $_GET["pl_address_block.per_id"] = $_GET["per_id"];
        }
        $opts = array(
        "sql" => "SELECT id, address_offset, size, mem_usage, protection FROM p_address_block INNER JOIN pl_address_block ON (pl_address_block.addr_id = p_address_block.id)",
        "filters" => array( "pl_address_block.per_id" ),
        "allowUnfiltered" => false,
        "hexColumns" => array( "address_offset", "size" ),
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "address_offset",
        "table" => "p_address_block",
        "columns" => array( "size", "mem_usage", "protection" ),
        "link_id" => "per_id",
        "lookup_table" => "pl_address_block",
        "lookup_reference" => "per_id",
        "lookup_added_id" => "addr_id",
        );
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id" ),
        "sql" => "SELECT id, address_offset, size, mem_usage, protection FROM p_address_block",
        "table" => "p_address_block",
        "columns" => array( "address_offset", "size", "mem_usage", "protection" ),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id"),
        "sql" => "SELECT id, address_offset, size, mem_usage, protection FROM p_address_block",
        "table" => "p_address_block",
        "columns" => array( "address_offset", "size", "mem_usage", "protection" ),
        );
        handle_delete($pdo, $opts);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
} // end of switch

$pdo = null;
?>