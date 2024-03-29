<?php

include("sanitize.inc");
include("limit.inc");
include("authenticate.inc");

include("action_get.inc");
include("action_post.inc");
include("action_put.inc");
include("action_delete.inc");

// SELECT id, name, description, number FROM p_interrupt INNER JOIN pl_interrupt ON (pl_interrupt.irq_id = p_address_block.id) WHERE pl_interrupt.per_in_id = 1;

$req_type = $_POST['REQUEST_METHOD'];
header('Content-Type: application/json');
include ("../../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
switch ($req_type) {
    case 'GET':
        if(isset($_POST["per_in_id"]))
        {
            $_POST["pl_interrupt.per_in_id"] = $_POST["per_in_id"];
        }
        $opts = array(
        "sql" => "SELECT id, name, description, number FROM p_interrupt INNER JOIN pl_interrupt ON (pl_interrupt.irq_id = p_interrupt.id)",
        "filters" => array( "pl_interrupt.per_in_id" ),
        "allowUnfiltered" => false,
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "name",
        "table" => "p_interrupt",
        "columns" => array( "description", "number" ),
        "link_id" => "per_in_id",
        "lookup_table" => "pl_interrupt",
        "lookup_reference" => "per_in_id",
        "lookup_added_id" => "irq_id",
        );
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id" ),
        "sql" => "SELECT id, name, description, number FROM p_interrupt",
        "table" => "p_interrupt",
        "columns" => array( "name", "description", "number" ),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id"),
        "sql" => "SELECT id, name, description, number FROM p_interrupt",
        "table" => "p_interrupt",
        "columns" => array( "name", "description", "number" ),
        );
        handle_delete($pdo, $opts);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
} // end of switch

$pdo = null;
?>