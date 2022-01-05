<?php
$req_type = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
include("limit.inc");
include ("../../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
include("authenticate.inc");

switch ($req_type) {
    case 'GET':
        if(isset($_GET["id"]))
        {
            $sql = "SELECT id, name, CPU_clock_max_MHz, Flash_size_kB, RAM_size_kB, Supply_Voltage_min_V, Supply_Voltage_max_V, Operating_Temperature_min_degC, Operating_Temperature_max_degC, svd_id, Addressable_unit_bit, bus_width_bit, description"
                . " FROM microcontroller WHERE id = \"" . $_GET["id"] . "\"";
        }
        if(isset($_GET["name"]))
        {
            $sql = "SELECT id, name, CPU_clock_max_MHz, Flash_size_kB, RAM_size_kB, Supply_Voltage_min_V, Supply_Voltage_max_V, Operating_Temperature_min_degC, Operating_Temperature_max_degC, svd_id, Addressable_unit_bit, bus_width_bit, description"
                . " FROM microcontroller WHERE name = \"" . $_GET["name"] . "\"";
        }
        else
        {
            $left_off_id = 0;
            $limit = $DEFAULT_LIMIT;
            if(isset($_GET["left_off_id"]))
            {
                $left_off_id = $_GET["left_off_id"];
            }
            if(isset($_GET["limit"]))
            {
                $limit = $_GET["limit"];
                if($limit > $MAX_LIMIT)
                {
                    $limit = 30;
                }
            }
            $sql = "SELECT id, name, CPU_clock_max_MHz, Flash_size_kB, RAM_size_kB, Supply_Voltage_min_V, Supply_Voltage_max_V, Operating_Temperature_min_degC, Operating_Temperature_max_degC, svd_id, Addressable_unit_bit, bus_width_bit, description"
                    . " FROM microcontroller"
                    . " WHERE (id > " . $left_off_id . ")"
                    . " ORDER BY id ASC"
                    . " LIMIT " . $limit;
        }
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
        break;

    case 'POST':
        authenticate($pdo);
        break;

    case 'PUT':
        authenticate($pdo);
        break;

    case 'DELETE':
        authenticate($pdo);
        break;
}
?>