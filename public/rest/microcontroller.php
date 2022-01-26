<?php

include("sanitize.inc");
include("limit.inc");
include("authenticate.inc");

function handle_get($pdo)
{
    $sql = "SELECT id, name, CPU_clock_max_MHz"
           . ", Flash_size_kB, RAM_size_kB"
           . ", Supply_Voltage_min_V, Supply_Voltage_max_V"
           . ", Operating_Temperature_min_degC, Operating_Temperature_max_degC"
           . ", svd_id, Addressable_unit_bit, bus_width_bit"
           . ", description"
           . ", architecture_id, market_state_id, package_id, vendor_id"
           . " FROM microcontroller"
               ;
    if(isset($_GET["id"]))
    {
        $sql = $sql . " WHERE id = \"" . $_GET["id"] . "\"";
    }
    if(isset($_GET["name"]))
    {
        $sql = $sql . " WHERE name = \"" . $_GET["name"] . "\"";
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
            . " WHERE (id > " . $left_off_id . ")"
            . " ORDER BY id ASC"
            . " LIMIT " . $limit;
    }
    header('X-SQL: ' . $sql);
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
{ // create new microcontroller
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

            $fields = array('name');
            $values = array(sanitize_string($_GET["name"]));

            // CPU_clock_max_MHz
            if(isset($_GET["CPU_clock_max_MHz"]))
            {
                $val = sanitize_string($_GET["CPU_clock_max_MHz"]);
                $statement->execute(array("CPU_clock_max_MHz", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "CPU_clock_max_MHz";
                $values[] = $val;
            }
            // Flash_size_kB
            if(isset($_GET["Flash_size_kB"]))
            {
                $val = sanitize_string($_GET["Flash_size_kB"]);
                $statement->execute(array("Flash_size_kB", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "Flash_size_kB";
                $values[] = $val;
            }
            // RAM_size_kB
            if(isset($_GET["RAM_size_kB"]))
            {
                $val = sanitize_string($_GET["RAM_size_kB"]);
                $statement->execute(array("RAM_size_kB", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "RAM_size_kB";
                $values[] = $val;
            }
            // Supply_Voltage_min_V
            if(isset($_GET["Supply_Voltage_min_V"]))
            {
                $val = sanitize_string($_GET["Supply_Voltage_min_V"]);
                $statement->execute(array("Supply_Voltage_min_V", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "Supply_Voltage_min_V";
                $values[] = $val;
            }
            // Supply_Voltage_max_V
            if(isset($_GET["Supply_Voltage_max_V"]))
            {
                $val = sanitize_string($_GET["Supply_Voltage_max_V"]);
                $statement->execute(array("Supply_Voltage_max_V", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "Supply_Voltage_max_V";
                $values[] = $val;
            }
            // Operating_Temperature_min_degC
            if(isset($_GET["Operating_Temperature_min_degC"]))
            {
                $val = sanitize_string($_GET["Operating_Temperature_min_degC"]);
                $statement->execute(array("Operating_Temperature_min_degC", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "Operating_Temperature_min_degC";
                $values[] = $val;
            }
            // Operating_Temperature_max_degC
            if(isset($_GET["Operating_Temperature_max_degC"]))
            {
                $val = sanitize_string($_GET["Operating_Temperature_max_degC"]);
                $statement->execute(array("Operating_Temperature_max_degC", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "Operating_Temperature_max_degC";
                $values[] = $val;
            }
            // svd_id
            if(isset($_GET["svd_id"]))
            {
                $val = sanitize_string($_GET["svd_id"]);
                $statement->execute(array("svd_id", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "svd_id";
                $values[] = $val;
            }
            // Addressable_unit_bit
            if(isset($_GET["Addressable_unit_bit"]))
            {
                $val = sanitize_string($_GET["Addressable_unit_bit"]);
                $statement->execute(array("Addressable_unit_bit", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "Addressable_unit_bit";
                $values[] = $val;
            }
            // bus_width_bit
            if(isset($_GET["bus_width_bit"]))
            {
                $val = sanitize_string($_GET["bus_width_bit"]);
                $statement->execute(array("bus_width_bit", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "bus_width_bit";
                $values[] = $val;
            }
            // description
            if(isset($_GET["description"]))
            {
                $val = sanitize_string($_GET["description"]);
                $statement->execute(array("description", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "description";
                $values[] = $val;
            }
            // architecture_id
            if(isset($_GET["architecture_id"]))
            {
                $val = sanitize_string($_GET["architecture_id"]);
                $statement->execute(array("architecture_id", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "architecture_id";
                $values[] = $val;
            }
            // market_state_id
            if(isset($_GET["market_state_id"]))
            {
                $val = sanitize_string($_GET["market_state_id"]);
                $statement->execute(array("market_state_id", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "market_state_id";
                $values[] = $val;
            }
            // package_id
            if(isset($_GET["package_id"]))
            {
                $val = sanitize_string($_GET["package_id"]);
                $statement->execute(array("package_id", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "package_id";
                $values[] = $val;
            }
            // vendor_id
            if(isset($_GET["vendor_id"]))
            {
                $val = sanitize_string($_GET["vendor_id"]);
                $statement->execute(array("vendor_id", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "vendor_id";
                $values[] = $val;
            }

            $sql = "INSERT INTO microcontroller (" . $fields . ") VALUES (" . $values . ")";
            header('X-SQL: ' . $sql);
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

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

function handle_put($pdo)
{
    $user_data = authenticate($pdo);
    if(isset($_GET["id"]))
    {
        $sql = "SELECT id, name, CPU_clock_max_MHz"
            . ", Flash_size_kB, RAM_size_kB"
            . ", Supply_Voltage_min_V, Supply_Voltage_max_V"
            . ", Operating_Temperature_min_degC, Operating_Temperature_max_degC"
            . ", svd_id, Addressable_unit_bit, bus_width_bit"
            . ", description"
            . ", architecture_id, market_state_id, package_id, vendor_id"
            . " FROM microcontroller"
            . " WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    else if(isset($_GET["name"]))
    {
        $sql = "SELECT id, name, CPU_clock_max_MHz"
            . ", Flash_size_kB, RAM_size_kB"
            . ", Supply_Voltage_min_V, Supply_Voltage_max_V"
            . ", Operating_Temperature_min_degC, Operating_Temperature_max_degC"
            . ", svd_id, Addressable_unit_bit, bus_width_bit"
            . ", description"
            . ", architecture_id, market_state_id, package_id, vendor_id"
            . " FROM microcontroller"
            . " WHERE name = \"" . sanitize_string($_GET["name"]) . "\"";
    }
    if(isset($sql))
    {
        try
        {
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = $data[0];
            if(isset($data['id']))
            {
                $changed = false;
                $sql = "INSERT INTO p_log (action, on_table, on_id, on_column, old_value, new_value, user)"
                    . ' VALUES ("PUT", "microcontroller", ' . $data['id']. ', ?, ?, ?, ?)';
                    $statement = $pdo->prepare($sql);
                    // name
                    if(isset($_GET["name"]))
                    {
                        $newName = sanitize_string($_GET["name"]);
                        if($newName != $data['name'])
                        {
                            $changed = True;
                            $statement->execute(array("name", $data['name'], $newName, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['name'] = $newName;
                        }
                    }
                    // CPU_clock_max_MHz
                    if(isset($_GET["CPU_clock_max_MHz"]))
                    {
                        $newValue = sanitize_string($_GET["CPU_clock_max_MHz"]);
                        if($newValue != $data['CPU_clock_max_MHz'])
                        {
                            $changed = True;
                            $statement->execute(array("CPU_clock_max_MHz", $data['CPU_clock_max_MHz'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['CPU_clock_max_MHz'] = $newValue;
                        }
                    }
                    // Flash_size_kB
                    if(isset($_GET["Flash_size_kB"]))
                    {
                        $newValue = sanitize_string($_GET["Flash_size_kB"]);
                        if($newValue != $data['Flash_size_kB'])
                        {
                            $changed = True;
                            $statement->execute(array("Flash_size_kB", $data['Flash_size_kB'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['Flash_size_kB'] = $newValue;
                        }
                    }
                    // RAM_size_kB
                    if(isset($_GET["RAM_size_kB"]))
                    {
                        $newValue = sanitize_string($_GET["RAM_size_kB"]);
                        if($newValue != $data['RAM_size_kB'])
                        {
                            $changed = True;
                            $statement->execute(array("RAM_size_kB", $data['RAM_size_kB'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['RAM_size_kB'] = $newValue;
                        }
                    }
                    // Supply_Voltage_min_V
                    if(isset($_GET["Supply_Voltage_min_V"]))
                    {
                        $newValue = sanitize_string($_GET["Supply_Voltage_min_V"]);
                        if($newValue != $data['Supply_Voltage_min_V'])
                        {
                            $changed = True;
                            $statement->execute(array("Supply_Voltage_min_V", $data['Supply_Voltage_min_V'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['Supply_Voltage_min_V'] = $newValue;
                        }
                    }
                    // Supply_Voltage_max_V
                    if(isset($_GET["Supply_Voltage_max_V"]))
                    {
                        $newValue = sanitize_string($_GET["Supply_Voltage_max_V"]);
                        if($newValue != $data['Supply_Voltage_max_V'])
                        {
                            $changed = True;
                            $statement->execute(array("Supply_Voltage_max_V", $data['Supply_Voltage_max_V'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['Supply_Voltage_max_V'] = $newValue;
                        }
                    }
                    // Operating_Temperature_min_degC
                    if(isset($_GET["Operating_Temperature_min_degC"]))
                    {
                        $newValue = sanitize_string($_GET["Operating_Temperature_min_degC"]);
                        if($newValue != $data['Operating_Temperature_min_degC'])
                        {
                            $changed = True;
                            $statement->execute(array("Operating_Temperature_min_degC", $data['Operating_Temperature_min_degC'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['Operating_Temperature_min_degC'] = $newValue;
                        }
                    }
                    // Operating_Temperature_max_degC
                    if(isset($_GET["Operating_Temperature_max_degC"]))
                    {
                        $newValue = sanitize_string($_GET["Operating_Temperature_max_degC"]);
                        if($newValue != $data['Operating_Temperature_max_degC'])
                        {
                            $changed = True;
                            $statement->execute(array("Operating_Temperature_max_degC", $data['Operating_Temperature_max_degC'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['Operating_Temperature_max_degC'] = $newValue;
                        }
                    }
                    // svd_id
                    if(isset($_GET["svd_id"]))
                    {
                        $newValue = sanitize_string($_GET["svd_id"]);
                        if($newValue != $data['svd_id'])
                        {
                            $changed = True;
                            $statement->execute(array("svd_id", $data['svd_id'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['svd_id'] = $newValue;
                        }
                    }
                    // Addressable_unit_bit
                    if(isset($_GET["Addressable_unit_bit"]))
                    {
                        $newValue = sanitize_string($_GET["Addressable_unit_bit"]);
                        if($newValue != $data['Addressable_unit_bit'])
                        {
                            $changed = True;
                            $statement->execute(array("Addressable_unit_bit", $data['Addressable_unit_bit'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['Addressable_unit_bit'] = $newValue;
                        }
                    }
                    // bus_width_bit
                    if(isset($_GET["bus_width_bit"]))
                    {
                        $newValue = sanitize_string($_GET["bus_width_bit"]);
                        if($newValue != $data['bus_width_bit'])
                        {
                            $changed = True;
                            $statement->execute(array("bus_width_bit", $data['bus_width_bit'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['bus_width_bit'] = $newValue;
                        }
                    }
                    // description
                    if(isset($_GET["description"]))
                    {
                        $newValue = sanitize_string($_GET["description"]);
                        if($newValue != $data['description'])
                        {
                            $changed = True;
                            $statement->execute(array("description", $data['description'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['description'] = $newValue;
                        }
                    }
                    // architecture_id
                    if(isset($_GET["architecture_id"]))
                    {
                        $newValue = sanitize_string($_GET["architecture_id"]);
                        if($newValue != $data['architecture_id'])
                        {
                            $changed = True;
                            $statement->execute(array("architecture_id", $data['architecture_id'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['architecture_id'] = $newValue;
                        }
                    }
                    // market_state_id
                    if(isset($_GET["market_state_id"]))
                    {
                        $newValue = sanitize_string($_GET["market_state_id"]);
                        if($newValue != $data['market_state_id'])
                        {
                            $changed = True;
                            $statement->execute(array("market_state_id", $data['market_state_id'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['market_state_id'] = $newValue;
                        }
                    }
                    // package_id
                    if(isset($_GET["package_id"]))
                    {
                        $newValue = sanitize_string($_GET["package_id"]);
                        if($newValue != $data['package_id'])
                        {
                            $changed = True;
                            $statement->execute(array("package_id", $data['package_id'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['package_id'] = $newValue;
                        }
                    }
                    // vendor_id
                    if(isset($_GET["vendor_id"]))
                    {
                        $newValue = sanitize_string($_GET["vendor_id"]);
                        if($newValue != $data['vendor_id'])
                        {
                            $changed = True;
                            $statement->execute(array("vendor_id", $data['vendor_id'], $newValue, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data['vendor_id'] = $newValue;
                        }
                    }

                    if(True == $changed)
                    {
                        // echo("now updating");
                        $sql = "UPDATE microcontroller SET name = ?, CPU_clock_max_MHz = ?"
                            . ", Flash_size_kB = ?, RAM_size_kB = ?"
                            . ", Supply_Voltage_min_V = ?, Supply_Voltage_max_V = ?"
                            . ", Operating_Temperature_min_degC = ?, Operating_Temperature_max_degC = ?"
                            . ", svd_id = ?, Addressable_unit_bit = ?, bus_width_bit = ?"
                            . ", description = ?"
                            . ", architecture_id = ?, market_state_id = ?, package_id = ?, vendor_id = ?"
                            . "WHERE id =?";
                        $statement = $pdo->prepare($sql);
                        $statement->execute(array($data["name"], $data["CPU_clock_max_MHz"],
                            $data["Flash_size_kB"], $data["RAM_size_kB"],
                            $data["Supply_Voltage_min_V"], $data["Supply_Voltage_max_V"],
                            $data["Operating_Temperature_min_degC"], $data["Operating_Temperature_max_degC"],
                            $data["svd_id"], $data["Addressable_unit_bit"], $data["bus_width_bit"],
                            $data["description"],
                            $data["architecture_id"], $data["market_state_id"], $data["package_id"], $data["vendor_id"],
                            $data["id"]));
                        $statement->fetchAll(PDO::FETCH_ASSOC);

                        echo(json_encode(array($data)));
                    }
                    else
                    {
                        // no change
                        header("X-debug: no change");
                        header('HTTP/1.0 400 Bad Request');
                    }
            }
            else
            {
                // no such vendor -> can not update
                header("X-debug: no such vendor -> can not update : " . $sql);
                header('HTTP/1.0 400 Bad Request');
            }
        }
        catch (PDOException $e)
        {
            header("X-debug: no such vendor -> can not update : " . $sql);
            header("X-EXCEPTION: " . $e);
            header('HTTP/1.0 500 Internal Server Error');
        }
    }
    else
    {
        // no name or id  specified
        header("X-debug: no name or id specified");
        header('HTTP/1.0 400 Bad Request');
    }
}

function handle_delete($pdo)
{
    $user_data = authenticate($pdo);
    if(isset($_GET["id"]))
    {
        $sql = "SELECT id, name, CPU_clock_max_MHz"
            . ", Flash_size_kB, RAM_size_kB"
            . ", Supply_Voltage_min_V, Supply_Voltage_max_V"
            . ", Operating_Temperature_min_degC, Operating_Temperature_max_degC"
            . ", svd_id, Addressable_unit_bit, bus_width_bit"
            . ", description"
            . ", architecture_id, market_state_id, package_id, vendor_id"
            . " FROM microcontroller"
            . " WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    else if(isset($_GET["name"]))
    {
        $sql = "SELECT id, name, CPU_clock_max_MHz"
            . ", Flash_size_kB, RAM_size_kB"
            . ", Supply_Voltage_min_V, Supply_Voltage_max_V"
            . ", Operating_Temperature_min_degC, Operating_Temperature_max_degC"
            . ", svd_id, Addressable_unit_bit, bus_width_bit"
            . ", description"
            . ", architecture_id, market_state_id, package_id, vendor_id"
            . " FROM microcontroller"
            . " WHERE name = \"" . sanitize_string($_GET["name"]) . "\"";
    }
    if(isset($sql))
    {
        try
        {
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = $data[0];
            if(isset($data['id']))
            {
                $sql = "INSERT INTO p_log (action, on_table, on_id, on_column, old_value, user)"
                    . ' VALUES ("DELETE", "microcontroller", ?, ?, ?, ?)';
                    $statement = $pdo->prepare($sql);
                    $statement->execute(array($data['id'], "name", $data['name'], $user_data["name"]));
                    $statement->execute(array($data['id'], "CPU_clock_max_MHz", $data['CPU_clock_max_MHz'], $user_data["name"]));
                    $statement->execute(array($data['id'], "Flash_size_kB", $data['Flash_size_kB'], $user_data["name"]));
                    $statement->execute(array($data['id'], "RAM_size_kB", $data['RAM_size_kB'], $user_data["name"]));
                    $statement->execute(array($data['id'], "Supply_Voltage_min_V", $data['Supply_Voltage_min_V'], $user_data["name"]));
                    $statement->execute(array($data['id'], "Supply_Voltage_max_V", $data['Supply_Voltage_max_V'], $user_data["name"]));
                    $statement->execute(array($data['id'], "Operating_Temperature_min_degC", $data['Operating_Temperature_min_degC'], $user_data["name"]));
                    $statement->execute(array($data['id'], "Operating_Temperature_max_degC", $data['Operating_Temperature_max_degC'], $user_data["name"]));
                    $statement->execute(array($data['id'], "svd_id", $data['svd_id'], $user_data["name"]));
                    $statement->execute(array($data['id'], "Addressable_unit_bit", $data['Addressable_unit_bit'], $user_data["name"]));
                    $statement->execute(array($data['id'], "bus_width_bit", $data['bus_width_bit'], $user_data["name"]));
                    $statement->execute(array($data['id'], "description", $data['description'], $user_data["name"]));
                    $statement->execute(array($data['id'], "architecture_id", $data['architecture_id'], $user_data["name"]));
                    $statement->execute(array($data['id'], "market_state_id", $data['market_state_id'], $user_data["name"]));
                    $statement->execute(array($data['id'], "package_id", $data['package_id'], $user_data["name"]));
                    $statement->execute(array($data['id'], "vendor_id", $data['vendor_id'], $user_data["name"]));

                    $sql = "DELETE FROM microcontroller WHERE id =?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute(array($data["id"]));
                    $statement->fetchAll(PDO::FETCH_ASSOC);

                    echo(json_encode(array($data)));
            }
            else
            {
                // no such micro controller -> can not delete
                // header("X-debug: no such microcontroller -> can not delete : " . $sql);
                header('HTTP/1.0 400 Bad Request');
            }
        }
        catch (PDOException $e)
        {
            header('HTTP/1.0 500 Internal Server Error');
        }
    }
    else
    {
        // no  name or  id  specified
        // header("X-debug: no name or id specified");
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
        handle_put($pdo);
        break;

    case 'DELETE':
        handle_delete($pdo);
        break;

    default:
        header('HTTP/1.0 501 Not Implemented');
        break;
}
?>