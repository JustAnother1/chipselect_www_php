<?php

function handle_post($pdo, $opts)
{
    $user_data = authenticate($pdo);
    $entryId = $opts["entryId"];

    if(isset($_POST[$entryId]))
    {
        $data_EntryId = $_POST[$entryId];
    }

    if(isset($data_EntryId))
    {
        try
        {
            $sql = "INSERT INTO p_log (action, on_table, on_column, new_value, user)"
                . ' VALUES ("INSERT", "' . $opts["table"] . '", ?, ?, ?)';
            $statement = $pdo->prepare($sql);

            $statement->execute(array($entryId, sanitize_string($data_EntryId), $user_data["name"]));
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

            $fields = array($entryId);
            $values = array(sanitize_string($data_EntryId));

            $columns = $opts["columns"];

            for($columnIdx = 0; $columnIdx < count($columns); $columnIdx++)
            {
                $curCol = $columns[$columnIdx];
                if(isset($_POST[$curCol]))
                {
                    $val = sanitize_string($_POST[$curCol]);
                    $statement->execute(array($curCol, $val, $user_data["name"]));
                    $data = $statement->fetchAll(PDO::FETCH_ASSOC);
                    $fields[] = $curCol;
                    $values[] = $val;
                }
            }

            $sql = "INSERT INTO " . $opts["table"] . " (" . implode(", ", $fields) . ') VALUES ("' . implode('", "', $values) . '")';
            header('X-SQL: ' . $sql);
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

            // echo("now requesting id");
            $sql = "SELECT LAST_INSERT_ID()";
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            if(isset($opts["lookup_table"]))
            {
                $new_id = $data[0]["LAST_INSERT_ID()"];
                if(isset($opts["link_id"]))
                {
                    if(isset($_POST[$opts["link_id"]]))
                    {
                        $ref_id = $_POST[$opts["link_id"]];

                        // log what we will do
                        $sql = "INSERT INTO p_log (action, on_table, on_column, new_value, user)"
                            . ' VALUES ("INSERT", "' . $opts["lookup_table"] . '", ?, ?, ?)';
                            $statement = $pdo->prepare($sql);

                            $statement->execute(array($opts["lookup_reference"], $ref_id, $user_data["name"]));
                            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

                            $statement->execute(array($opts["lookup_added_id"], $new_id, $user_data["name"]));
                            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

                            // create lookup entry
                            $sql = "INSERT INTO " . $opts["lookup_table"] . " ( " . $opts["lookup_reference"] . ", " . $opts["lookup_added_id"] . " ) VALUES ( ?, ? )";
                            $statement = $pdo->prepare($sql);

                            $statement->execute(array($ref_id, $new_id));
                            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

                            $array = array(
                                "id" => $new_id,
                            );
                            echo(json_encode(array($array)));
                    }
                    else
                    {
                        header('HTTP/1.0 400 Bad Request');
                        header('X-debug: no link_id(' . $opts["link_id"] . ') value in GET parameter');
                    }
                }
                else
                {
                    header('HTTP/1.0 500 Internal Server Error');
                    header('X-debug: no link_id set');
                }
            }
            else
            {
                $array = array(
                    "id" => $data[0]["LAST_INSERT_ID()"],
                );
                echo(json_encode(array($array)));
            }
        }
        catch (PDOException $e)
        {
            header('X-debug: ' . $e->getMessage());
            header('HTTP/1.0 500 Internal Server Error');
        }
    }
    else
    {
        // no item id specified
        header('HTTP/1.0 400 Bad Request');
        header('X-Action: POST');
        header('X-debug: no entryId(' . $entryId . ') value in GET parameter');
    }
}

?>