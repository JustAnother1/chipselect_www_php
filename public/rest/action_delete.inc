<?php

function handle_delete($pdo, $opts)
{
    $user_data = authenticate($pdo);
    $roles = $user_data["user_roles"];
    if(in_array("delete", $roles))
    {
        $filters = $opts["filters"];
        $filterIdx = 0;
        $found = false;

        for($filterIdx = 0; $filterIdx < count($filters); $filterIdx++)
        {
            if(isset($_POST[$filters[$filterIdx]]))
            {
                $opts["sql"] = $opts["sql"] . " WHERE " . $filters[$filterIdx] . " = \"" . $_POST[$filters[$filterIdx]] . "\"";
                $found = true;
                break;
            }
        }
        if(true == $found)
        {
            try
            {
                $statement = $pdo->prepare($opts["sql"]);
                $statement->execute();
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                $data = $data[0];
                if(isset($data['id']))
                {
                    $sql = "INSERT INTO p_log (action, on_table, on_id, on_column, old_value, user)"
                        . ' VALUES ("DELETE", "' . $opts["table"] . '", ' . $data['id']. ', ?, ?, ?)';
                    $statement = $pdo->prepare($sql);

                    $columns = $opts["columns"];

                    for($columnIdx = 0; $columnIdx < count($columns); $columnIdx++)
                    {
                        $curCol = $columns[$columnIdx];
                        $statement->execute(array($curCol, $data[$curCol], $user_data["name"]));
                    }

                    $sql = "DELETE FROM " . $opts["table"] . " WHERE id =?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute(array($data["id"]));
                    $statement->fetchAll(PDO::FETCH_ASSOC);

                    echo(json_encode(array($data)));
                }
                else
                {
                    // no such item -> can not delete
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
    else
    {
        // not an "delete" user -> may not delete !
        header('WWW-Authenticate: Basic realm="chipselect"');
        header('HTTP/1.0 401 Unauthorized');
    }
}

?>