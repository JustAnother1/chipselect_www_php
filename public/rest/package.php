<?php

include("sanitize.inc");
include("limit.inc");
include("authenticate.inc");

function handle_get($pdo)
{
    // print_r($_GET);
    if(isset($_GET["id"]))
    {
        $sql = "SELECT id, name FROM p_package WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    else if(isset($_GET["name"]))
    {
        $sql = "SELECT id, name FROM p_package WHERE name = \"" . sanitize_string($_GET["name"]) . "\"";
    }
    else
    {
        $sql = "SELECT id, name FROM p_package ORDER BY name";
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
                . ' VALUES ("INSERT", "p_package", ?, ?, ?)';
            $statement = $pdo->prepare($sql);

            $statement->execute(array("name", sanitize_string($_GET["name"]), $user_data["name"]));
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

            $sql = "INSERT INTO p_package (name) VALUES (" . sanitize_string($_GET["name"]) . ")";
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
    if((isset($_GET["id"])) && (isset($_GET["name"])))
    {
        $sql = "SELECT id, name FROM p_package WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    if(isset($sql))
    {
        try
        {
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = $data[0];

            $sql = "INSERT INTO p_log (action, on_table, on_id, on_column, old_value, new_value, user)"
                . ' VALUES ("PUT", "p_package", ' . $data['id']. ', ?, ?, ?, ?)';
            $statement = $pdo->prepare($sql);
            // name
            $newName = sanitize_string($_GET["name"]);
            if($newName != $data['name'])
            {
                $statement->execute(array("name", $data['name'], $newName, $user_data["name"]));
                $statement->fetchAll(PDO::FETCH_ASSOC);
                $data['name'] = $newName;
                // echo("now updating");
                $sql = "UPDATE p_package SET name = ? WHERE id =?";
                $statement = $pdo->prepare($sql);
                $statement->execute(array($data["name"], $data["id"]));
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
        $sql = "SELECT id, name FROM p_package WHERE id = \"" . sanitize_string($_GET["id"]) . "\"";
    }
    else if(isset($_GET["name"]))
    {
        $sql = "SELECT id, name FROM p_package WHERE name = \"" . sanitize_string($_GET["name"]) . "\"";
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
                . ' VALUES ("DELETE", "p_package", ?, ?, ?, ?)';
                $statement = $pdo->prepare($sql);
                $statement->execute(array($data['id'], "name", $data['name'], $user_data["name"]));

                $sql = "DELETE FROM p_package WHERE id =?";
                $statement = $pdo->prepare($sql);
                $statement->execute(array($data["id"]));
                $statement->fetchAll(PDO::FETCH_ASSOC);

                echo(json_encode(array($data)));
            }
            else
            {
                // no such market state -> can not delete
                // header("X-debug: no such market state -> can not delete : " . $sql);
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