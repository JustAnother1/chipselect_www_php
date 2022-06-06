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
        "sql" => "SELECT id, name, CPU_clock_max_MHz"
               . ", Flash_size_kB, RAM_size_kB"
               . ", Supply_Voltage_min_V, Supply_Voltage_max_V"
               . ", Operating_Temperature_min_degC, Operating_Temperature_max_degC"
               . ", svd_id, Addressable_unit_bit, bus_width_bit"
               . ", description"
               . ", architecture_id, market_state_id, package_id, vendor_id"
               . ", RAM_size_byte, RAM_start_address"
               . " FROM microcontroller",
        "filters" => array( "id", "name"),
        "allowUnfiltered" => true,
        );
        handle_get($pdo, $opts);
        break;

    case 'POST':
        $opts = array(
        "entryId" => "name",
        "table" => "microcontroller",
        "columns" => array( "CPU_clock_max_MHz", "Flash_size_kB", "RAM_size_kB", "Supply_Voltage_min_V", "Supply_Voltage_max_V",
        "Operating_Temperature_min_degC", "Operating_Temperature_max_degC", "svd_id", "Addressable_unit_bit", "bus_width_bit",
        "description", "architecture_id", "market_state_id", "package_id", "vendor_id", "RAM_size_byte", "RAM_start_address"),
        );
        handle_post($pdo, $opts);
        break;

    case 'PUT':
        $opts = array(
        "filters" => array( "id", "name" ),
        "sql" => "SELECT id, name, CPU_clock_max_MHz"
               . ", Flash_size_kB, RAM_size_kB"
               . ", Supply_Voltage_min_V, Supply_Voltage_max_V"
               . ", Operating_Temperature_min_degC, Operating_Temperature_max_degC"
               . ", svd_id, Addressable_unit_bit, bus_width_bit"
               . ", description"
               . ", architecture_id, market_state_id, package_id, vendor_id"
               . ", RAM_size_byte, RAM_start_address"
               . " FROM microcontroller",
        "table" => "microcontroller",
        "columns" => array( "name", "CPU_clock_max_MHz", "Flash_size_kB", "RAM_size_kB", "Supply_Voltage_min_V",
        "Supply_Voltage_max_V", "Operating_Temperature_min_degC", "Operating_Temperature_max_degC", "svd_id",
        "Addressable_unit_bit", "bus_width_bit", "description", "architecture_id", "market_state_id", "package_id", "vendor_id",
        "RAM_size_byte", "RAM_start_address"),
        );
        handle_put($pdo, $opts);
        break;

    case 'DELETE':
        $opts = array(
        "filters" => array( "id", "name" ),
        "sql" => "SELECT id, name, CPU_clock_max_MHz"
               . ", Flash_size_kB, RAM_size_kB"
               . ", Supply_Voltage_min_V, Supply_Voltage_max_V"
               . ", Operating_Temperature_min_degC, Operating_Temperature_max_degC"
               . ", svd_id, Addressable_unit_bit, bus_width_bit"
               . ", description"
               . ", architecture_id, market_state_id, package_id, vendor_id"
               . ", RAM_size_byte, RAM_start_address"
               . " FROM microcontroller",
        "table" => "microcontroller",
        "columns" => array( "name", "CPU_clock_max_MHz", "Flash_size_kB", "RAM_size_kB", "Supply_Voltage_min_V",
        "Supply_Voltage_max_V", "Operating_Temperature_min_degC", "Operating_Temperature_max_degC", "svd_id",
        "Addressable_unit_bit", "bus_width_bit", "description", "architecture_id", "market_state_id", "package_id", "vendor_id",
        "RAM_size_byte", "RAM_start_address"),
        );
        handle_delete($pdo, $opts);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
}

$pdo = null;
?>