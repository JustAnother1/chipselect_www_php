<?php

function sanitize_string($dirty)
{
    $dirty = trim($dirty);
    $dirty = trim($dirty, '"');
    $bad = array(";", '"', "'");
    $dirty = str_replace($bad, " ", $dirty);
    return $dirty;
}

function handle_get($pdo)
{
    // print_r($_GET);
    if(isset($_GET["id"]))
    {
        $sql = "SELECT id, name, url FROM p_vendor WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    else if(isset($_GET["name"]))
    {
        $sql = "SELECT id, name, url FROM p_vendor WHERE name = \"" . sanitize_string($_GET["name"]) . "\"";
    }
    else
    {
        $sql = "SELECT id, name, url FROM p_vendor WHERE alternative = 0 ORDER BY id";
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
                . ' VALUES ("INSERT", "p_vendor", "name", ?, ?)';
                $statement = $pdo->prepare($sql);
                $statement->execute(array(sanitize_string($_GET["name"]), $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);

                // print_r($data);
                // echo("now inserting");
                $sql = "INSERT INTO p_vendor (name) VALUES (?)";
                $statement = $pdo->prepare($sql);
                $statement->execute(array(sanitize_string($_GET["name"])));
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
        $sql = "SELECT id, name, alternative, url FROM p_vendor WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    else if(isset($_GET["name"]))
    {
        $sql = "SELECT id, name, alternative, url FROM p_vendor WHERE name = \"" . sanitize_string($_GET["name"]) . "\"";
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
                if(isset($_GET["url"]))
                {
                    $newUrl = sanitize_string($_GET["url"]);
                    if($newUrl != $data['url'])
                    {
                        $changed = True;
                        $statement->execute(array("url", $data['name'], $newUrl, $user_data["name"]));
                        $statement->fetchAll(PDO::FETCH_ASSOC);
                        $data['url'] = $newUrl;
                    }
                }
                if(isset($_GET["alternative"]))
                {
                    $newAlt = sanitize_string($_GET["alternative"]);
                    if($newAlt != $data['alternative'])
                    {
                        $changed = True;
                        $statement->execute(array("url", $data['name'], $newUrl, $user_data["name"]));
                        $statement->fetchAll(PDO::FETCH_ASSOC);
                        $data['alternative'] = $newAlt;
                    }
                }

                // echo("now updating");
                $sql = "UPDATE p_vendor SET name = ?, url = ?, alternative = ? WHERE id =?";
                $statement = $pdo->prepare($sql);
                $statement->execute(array($data["name"], $data["url"], $data["alternative"], $data["id"]));
                $statement->fetchAll(PDO::FETCH_ASSOC);

                echo(json_encode(array($data)));
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
        // no vendor name or Vendor id  specified
        header("X-debug: no vendor name specified");
        header('HTTP/1.0 400 Bad Request');
    }
}

function handle_delete($pdo)
{
    $user_data = authenticate($pdo);
    if(isset($_GET["id"]))
    {
        $sql = "SELECT id, name, alternative, url FROM p_vendor WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    else if(isset($_GET["name"]))
    {
        $sql = "SELECT id, name, alternative, url FROM p_vendor WHERE name = \"" . sanitize_string($_GET["name"]) . "\"";
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
                    $statement->fetchAll(PDO::FETCH_ASSOC);

                    $statement->execute(array($data['id'], "alternative", $data['alternative'], $user_data["name"]));
                    $statement->fetchAll(PDO::FETCH_ASSOC);

                    $statement->execute(array($data['id'], "url", $data['url'], $user_data["name"]));
                    $statement->fetchAll(PDO::FETCH_ASSOC);

                    // echo("now inserting");
                    $sql = "DELETE FROM p_vendor WHERE id =?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute(array($data["id"]));
                    $statement->fetchAll(PDO::FETCH_ASSOC);

                    echo(json_encode(array($data)));
            }
            else
            {
                // no such vendor -> can not delete
                // header("X-debug: no such vendor -> can not delete : " . $sql);
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
        // no vendor name or Vendor id  specified
        // header("X-debug: no vendor name specified");
        header('HTTP/1.0 400 Bad Request');
    }
}


$req_type = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
include ("../../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
include("authenticate.inc");
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
        header('HTTP/1.0 400 Bad Request');
        break;
} // end of switch
?>