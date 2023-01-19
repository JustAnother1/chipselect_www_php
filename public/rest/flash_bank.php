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
        if(isset($_POST["dev_id"]))
        {
            $_POST["pl_flash_bank.dev_id"] = $_POST["dev_id"];
        }
        $opts = array(
        "sql" => "SELECT id, start_address, size"
            . " FROM p_flash_bank"
            . " INNER JOIN pl_flash_bank ON pl_flash_bank.flash_id = p_flash_bank.id",
        "filters" => array( "pl_flash_bank.dev_id"),
        "allowUnfiltered" => false,
        "hexColumns" => array( "start_address", "size" ),
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "start_address",
        "table" => "p_flash_bank",
        "columns" => array( "size" ),
        "link_id" => "dev_id",
        "lookup_table" => "pl_flash_bank",
        "lookup_reference" => "dev_id",
        "lookup_added_id" => "flash_id",
        );
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id" ),
        "sql" => "SELECT id, start_address, size FROM p_flash_bank",
        "table" => "p_flash_bank",
        "columns" => array( "start_address", "size"),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id" ),
        "sql" => "SELECT id, start_address, size FROM p_flash_bank",
        "table" => "p_flash_bank",
        "columns" => array( "start_address", "size" ),
        );
        handle_delete($pdo, $opts);
        break;
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
} // end of switch

$pdo = null;
?>