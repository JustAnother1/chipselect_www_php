<?php

function sanitize_string($dirty)
{
    $dirty = trim($dirty);
    $dirty = trim($dirty, '"');
    $bad = array(";", '"', "'");
    $dirty = str_replace($bad, " ", $dirty);
    return $dirty;
}


$req_type = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
include ("../../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
include("authenticate.inc");
switch ($req_type) {
    case 'GET':
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
        break;

    case 'POST':
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
        break;

    case 'PUT':
        authenticate($pdo);
        break;

    case 'DELETE':
        authenticate($pdo);
        break;
}
?>