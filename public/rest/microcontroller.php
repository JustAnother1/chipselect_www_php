<?php

include("sanitize.inc");
include("limit.inc");
include("authenticate.inc");

function handle_get($pdo)
{
    $sql = "SELECT microcontroller.id, microcontroller.name, microcontroller.CPU_clock_max_MHz"
           . ", microcontroller.Flash_size_kB, microcontroller.RAM_size_kB"
           . ", microcontroller.Supply_Voltage_min_V, microcontroller.Supply_Voltage_max_V"
           . ", microcontroller.Operating_Temperature_min_degC, microcontroller.Operating_Temperature_max_degC"
           . ", microcontroller.svd_id, microcontroller.Addressable_unit_bit, microcontroller.bus_width_bit"
           . ", microcontroller.description"
           . ", p_vendor.name as vendor_name, p_vendor.url as vendor_url"
           . ", p_architecture.name as architecture_name, p_architecture.svd_name as architecture_svd_name"
           . ", p_architecture.revision as architecture_revision, p_architecture.endian as architecture_endian"
           . ", p_architecture.hasMPU as architecture_hasMPU, p_architecture.hasFPU as architecture_hasFPU"
           . ", p_architecture.interrupt_prio_bits as architecture_int_prio_bits, p_architecture.ARM_Vendor_systick as architecture_vend_systick"
           . ", p_market_state.name as maket_state"
           . ", p_package.name as package"
           . " FROM microcontroller"
           . " INNER JOIN pl_vendor ON (pl_vendor.dev_id = microcontroller.id)"
           . " INNER JOIN p_vendor ON (pl_vendor.vendor_id = p_vendor.id)"
           . " INNER JOIN pl_architecture ON (pl_architecture.dev_id = microcontroller.id)"
           . " INNER JOIN p_architecture ON (pl_architecture.arch_id = p_architecture.id)"
           . " INNER JOIN pl_market_state ON (pl_market_state.dev_id = microcontroller.id)"
           . " INNER JOIN p_market_state ON (pl_market_state.market_state_id = p_market_state.id)"
           . " INNER JOIN pl_package ON (pl_package.dev_id = microcontroller.id)"
           . " INNER JOIN p_package ON (pl_package.package_id = p_package.id)"
               ;
    if(isset($_GET["id"]))
    {
        $sql = $sql . " WHERE microcontroller.id = \"" . $_GET["id"] . "\"";
    }
    if(isset($_GET["name"]))
    {
        $sql = $sql . " WHERE microcontroller.name = \"" . $_GET["name"] . "\"";
    }
    else
    {
        $left_off_id = 0;
        $limit = $GLOBALS['DEFAULT_LIMIT'];
        if(isset($_GET["left_off_id"]))
        {
            $left_off_id = $_GET["left_off_id"];
        }
        if(isset($_GET["limit"]))
        {
            $limit = $_GET["limit"];
            if($limit > $GLOBALS['MAX_LIMIT'])
            {
                $limit = 30;
            }
        }
        $sql = $sql
            . " WHERE (microcontroller.id > " . $left_off_id . ")"
            . " ORDER BY microcontroller.id ASC"
            . " LIMIT " . $limit;
    }
    // header('X-SQL: ' . $sql);
    try
    {
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    }
    catch (PDOException $e)
    {
        header('HTTP/1.0 500 Internal Server Error');
    }
}

function handle_post($pdo)
{
    $user_data = authenticate($pdo);
    if(isset($_GET["name"]))
    {
        try
        {
            $sql = "INSERT INTO p_log (action, on_table, on_column, new_value, user)"
                . ' VALUES ("INSERT", "microcontroller", ?, ?, ?)';
            $statement = $pdo->prepare($sql);

            $statement->execute(array("name", sanitize_string($_GET["name"]), $user_data["name"]));
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

            if(isset($_GET["url"]))
            {
                $statement->execute(array("url", sanitize_string($_GET["url"]), $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                // print_r($data);
                // echo("now inserting");
                $sql = "INSERT INTO p_vendor (name, url) VALUES (?, ?)";
                $statement = $pdo->prepare($sql);
                $statement->execute(array(sanitize_string($_GET["name"]), sanitize_string($_GET["url"])));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            }
            else
            {
                // print_r($data);
                // echo("now inserting");
                $sql = "INSERT INTO p_vendor (name) VALUES (?)";
                $statement = $pdo->prepare($sql);
                $statement->execute(array(sanitize_string($_GET["name"])));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            }

            // echo("now requesting id");
            $sql = "SELECT LAST_INSERT_ID()";
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            // print_r($data);
            $array = array(
                "id" => $data[0]["LAST_INSERT_ID()"],
            );
            echo(json_encode(array($array)));
        }
        catch (PDOException $e)
        {
            header('X-Debug: ' . $e);
            header('HTTP/1.0 500 Internal Server Error');
        }
    }
    else
    {
        // no vendor name specified
        header('HTTP/1.0 400 Bad Request');
    }
}

$req_type = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
include ("../../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
switch ($req_type) {
    case 'GET':
        handle_get($pdo);
        break;

    case 'POST':
        handle_post($pdo);
        break;

    case 'PUT':
        authenticate($pdo);
        break;

    case 'DELETE':
        authenticate($pdo);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
}
?>