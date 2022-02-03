<?php

function handle_put($pdo, $opts)
{
    $user_data = authenticate($pdo);

    $filters = $opts["filters"];
    $filterIdx = 0;
    $found = false;

    for($filterIdx = 0; $filterIdx < count($filters); $filterIdx++)
    {
        if(isset($_GET[$filters[$filterIdx]]))
        {
            $opts["sql"] = $opts["sql"] . " WHERE " . $filters[$filterIdx] . " = \"" . $_GET[$filters[$filterIdx]] . "\"";
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
                $changed = false;
                $sql = "INSERT INTO p_log (action, on_table, on_id, on_column, old_value, new_value, user)"
                    . ' VALUES ("PUT", "' . $opts["table"] . '", ' . $data['id']. ', ?, ?, ?, ?)';
                $statement = $pdo->prepare($sql);

                $columns = $opts["columns"];

                for($columnIdx = 0; $columnIdx < count($columns); $columnIdx++)
                {
                    $curCol = $columns[$columnIdx];
                    if(isset($_GET[$curCol]))
                    {
                        $val = sanitize_string($_GET[$curCol]);
                        if($val != $data[$curCol])
                        {
                            $changed = True;
                            $statement->execute(array($curCol, $data[$curCol], $val, $user_data["name"]));
                            $statement->fetchAll(PDO::FETCH_ASSOC);
                            $data[$curCol] = $val;
                        }
                    }
                }

                if(True == $changed)
                {
                    // echo("now updating");
                    $sql = "UPDATE p_architecture SET name = ?, svd_name = ?, revision = ?, endian = ?, hasMPU = ?, hasFPU = ?, interrupt_prio_bits = ?, ARM_Vendor_systick = ? WHERE id =?";
                    header('X-SQL: ' . $sql);
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
                // no such item -> can not update
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

?>