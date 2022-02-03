<?php

function handle_post($pdo, $opts)
{
    $user_data = authenticate($pdo);
    $entryId = $opts["entryId"];
    if(isset($_GET[$entryId]))
    {
        try
        {
            $sql = "INSERT INTO p_log (action, on_table, on_column, new_value, user)"
                . ' VALUES ("INSERT", "' . $opts["table"] . '", ?, ?, ?)';
                $statement = $pdo->prepare($sql);

                $statement->execute(array($entryId, sanitize_string($_GET[$entryId]), $user_data["name"]));
                $data = $statement->fetchAll(PDO::FETCH_ASSOC);

                $fields = array($entryId);
                $values = array(sanitize_string($_GET[$entryId]));

                $columns = $opts["columns"];

                for($columnIdx = 0; $columnIdx < count($columns); $columnIdx++)
                {
                    $curCol = $columns[$columnIdx];
                    if(isset($_GET[$curCol]))
                    {
                        $val = sanitize_string($_GET[$curCol]);
                        $statement->execute(array($curCol, $val, $user_data["name"]));
                        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                        $fields[] = $curCol;
                        $values[] = $val;
                    }
                }

                $sql = "INSERT INTO " . $opts["table"] . " (" . $fields . ") VALUES (" . $values . ")";
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
        // no item id specified
        header('HTTP/1.0 400 Bad Request');
    }
}

?>