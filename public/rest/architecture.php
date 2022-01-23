<?php

include("sanitize.inc");
include("limit.inc");
include("authenticate.inc");

function handle_get($pdo)
{
    // print_r($_GET);
    if(isset($_GET["id"]))
    {
        $sql = "SELECT id, name, svd_name, revision, endian, hasMPU, hasFPU, interrupt_prio_bits, ARM_Vendor_systick FROM p_architecture WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    else if(isset($_GET["name"]))
    {
        $sql = "SELECT id, name, svd_name, revision, endian, hasMPU, hasFPU, interrupt_prio_bits, ARM_Vendor_systick FROM p_architecture WHERE name = \"" . sanitize_string($_GET["name"]) . "\"";
    }
    else if(isset($_GET["svd_name"]))
    {
        $sql = "SELECT id, name, svd_name, revision, endian, hasMPU, hasFPU, interrupt_prio_bits, ARM_Vendor_systick FROM p_architecture WHERE svd_name = \"" . sanitize_string($_GET["svd_name"]) . "\"";
    }
    else
    {
        $sql = "SELECT id, name, svd_name, revision, endian, hasMPU, hasFPU, interrupt_prio_bits, ARM_Vendor_systick FROM p_architecture WHERE alternative = 0 ORDER BY name";
    }
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

            $fields = array('name');
            $values = array(sanitize_string($_GET["name"]));

            // svd_name
            if(isset($_GET["svd_name"]))
            {
                $val = sanitize_string($_GET["svd_name"]);
                $statement->execute(array("svd_name", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "svd_name";
                $values[] = $val;
            }
            // revision
            if(isset($_GET["revision"]))
            {
                $val = sanitize_string($_GET["revision"]);
                $statement->execute(array("revision", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "revision";
                $values[] = $val;
            }
            // endian
            if(isset($_GET["endian"]))
            {
                $val = sanitize_string($_GET["endian"]);
                $statement->execute(array("endian", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "endian";
                $values[] = $val;
            }
            // hasMPU
            if(isset($_GET["hasMPU"]))
            {
                $val = sanitize_string($_GET["hasMPU"]);
                $statement->execute(array("hasMPU", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "hasMPU";
                $values[] = $val;
            }
            // hasFPU
            if(isset($_GET["hasFPU"]))
            {
                $val = sanitize_string($_GET["hasFPU"]);
                $statement->execute(array("hasFPU", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "hasFPU";
                $values[] = $val;
            }
            // interrupt_prio_bits
            if(isset($_GET["interrupt_prio_bits"]))
            {
                $val = sanitize_string($_GET["interrupt_prio_bits"]);
                $statement->execute(array("interrupt_prio_bits", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "interrupt_prio_bits";
                $values[] = $val;
            }
            // ARM_Vendor_systick
            if(isset($_GET["ARM_Vendor_systick"]))
            {
                $val = sanitize_string($_GET["ARM_Vendor_systick"]);
                $statement->execute(array("ARM_Vendor_systick", $val, $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $fields[] = "ARM_Vendor_systick";
                $values[] = $val;
            }

            $sql = "INSERT INTO p_architecture (" . $fields . ") VALUES (" . $values . ")";
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
        $sql = "SELECT id, name, svd_name, revision, endian, hasMPU, hasFPU, interrupt_prio_bits, ARM_Vendor_systick FROM p_architecture WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    else if(isset($_GET["name"]))
    {
        $sql = "SELECT id, name, svd_name, revision, endian, hasMPU, hasFPU, interrupt_prio_bits, ARM_Vendor_systick FROM p_architecture WHERE name = \"" . sanitize_string($_GET["name"]) . "\"";
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
                    . ' VALUES ("PUT", "p_vendor", ' . $data['id']. ', ?, ?, ?, ?)';
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
                // svd_name
                if(isset($_GET["svd_name"]))
                {
                    $newValue = sanitize_string($_GET["svd_name"]);
                    if($newValue != $data['svd_name'])
                    {
                        $changed = True;
                        $statement->execute(array("svd_name", $data['svd_name'], $newValue, $user_data["name"]));
                        $statement->fetchAll(PDO::FETCH_ASSOC);
                        $data['svd_name'] = $newValue;
                    }
                }
                // revision
                if(isset($_GET["revision"]))
                {
                    $newValue = sanitize_string($_GET["revision"]);
                    if($newValue != $data['revision'])
                    {
                        $changed = True;
                        $statement->execute(array("revision", $data['revision'], $newValue, $user_data["name"]));
                        $statement->fetchAll(PDO::FETCH_ASSOC);
                        $data['revision'] = $newValue;
                    }
                }
                // endian
                if(isset($_GET["endian"]))
                {
                    $newValue = sanitize_string($_GET["endian"]);
                    if($newValue != $data['endian'])
                    {
                        $changed = True;
                        $statement->execute(array("endian", $data['endian'], $newValue, $user_data["name"]));
                        $statement->fetchAll(PDO::FETCH_ASSOC);
                        $data['endian'] = $newValue;
                    }
                }
                // hasMPU
                if(isset($_GET["hasMPU"]))
                {
                    $newValue = sanitize_string($_GET["hasMPU"]);
                    if($newValue != $data['hasMPU'])
                    {
                        $changed = True;
                        $statement->execute(array("hasMPU", $data['hasMPU'], $newValue, $user_data["name"]));
                        $statement->fetchAll(PDO::FETCH_ASSOC);
                        $data['hasMPU'] = $newValue;
                    }
                }
                // hasFPU
                if(isset($_GET["hasFPU"]))
                {
                    $newValue = sanitize_string($_GET["hasFPU"]);
                    if($newValue != $data['hasFPU'])
                    {
                        $changed = True;
                        $statement->execute(array("hasFPU", $data['hasFPU'], $newValue, $user_data["name"]));
                        $statement->fetchAll(PDO::FETCH_ASSOC);
                        $data['hasFPU'] = $newValue;
                    }
                }
                // interrupt_prio_bits
                if(isset($_GET["interrupt_prio_bits"]))
                {
                    $newValue = sanitize_string($_GET["interrupt_prio_bits"]);
                    if($newValue != $data['interrupt_prio_bits'])
                    {
                        $changed = True;
                        $statement->execute(array("interrupt_prio_bits", $data['interrupt_prio_bits'], $newValue, $user_data["name"]));
                        $statement->fetchAll(PDO::FETCH_ASSOC);
                        $data['interrupt_prio_bits'] = $newValue;
                    }
                }
                // ARM_Vendor_systick
                if(isset($_GET["ARM_Vendor_systick"]))
                {
                    $newValue = sanitize_string($_GET["ARM_Vendor_systick"]);
                    if($newValue != $data['ARM_Vendor_systick'])
                    {
                        $changed = True;
                        $statement->execute(array("ARM_Vendor_systick", $data['ARM_Vendor_systick'], $newValue, $user_data["name"]));
                        $statement->fetchAll(PDO::FETCH_ASSOC);
                        $data['ARM_Vendor_systick'] = $newValue;
                    }
                }

                if(True == $changed)
                {
                    // echo("now updating");
                    $sql = "UPDATE p_architecture SET name = ?, svd_name = ?, revision = ?, endian = ?, hasMPU = ?, hasFPU = ?, interrupt_prio_bits = ?, ARM_Vendor_systick = ? WHERE id =?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute(array($data["name"], $data["svd_name"], $data["revision"], $data["endian"], $data["hasMPU"], $data["hasFPU"], $data["interrupt_prio_bits"], $data["ARM_Vendor_systick"], $data["id"]));
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
        $sql = "SELECT id, name, alternative, svd_name, revision, endian, hasMPU, hasFPU, interrupt_prio_bits, ARM_Vendor_systick FROM p_architecture WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    else if(isset($_GET["name"]))
    {
        $sql = "SELECT id, name, alternative, svd_name, revision, endian, hasMPU, hasFPU, interrupt_prio_bits, ARM_Vendor_systick FROM p_architecture WHERE name = \"" . sanitize_string($_GET["name"]) . "\"";
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
                    . ' VALUES ("DELETE", "p_vendor", ?, ?, ?, ?)';
                    $statement = $pdo->prepare($sql);
                    $statement->execute(array($data['id'], "name", $data['name'], $user_data["name"]));
                    $statement->execute(array($data['id'], "alternative", $data['alternative'], $user_data["name"]));
                    $statement->execute(array($data['id'], "svd_name", $data['svd_name'], $user_data["name"]));
                    $statement->execute(array($data['id'], "revision", $data['revision'], $user_data["name"]));
                    $statement->execute(array($data['id'], "endian", $data['endian'], $user_data["name"]));
                    $statement->execute(array($data['id'], "hasMPU", $data['hasMPU'], $user_data["name"]));
                    $statement->execute(array($data['id'], "hasFPU", $data['hasFPU'], $user_data["name"]));
                    $statement->execute(array($data['id'], "interrupt_prio_bits", $data['interrupt_prio_bits'], $user_data["name"]));
                    $statement->execute(array($data['id'], "ARM_Vendor_systick", $data['ARM_Vendor_systick'], $user_data["name"]));

                    $sql = "DELETE FROM p_architecture WHERE id =?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute(array($data["id"]));
                    $statement->fetchAll(PDO::FETCH_ASSOC);

                    echo(json_encode(array($data)));
            }
            else
            {
                // no such architecture -> can not delete
                // header("X-debug: no such architecture -> can not delete : " . $sql);
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
} // end of switch
?>