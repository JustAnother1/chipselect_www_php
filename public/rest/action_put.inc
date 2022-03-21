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
        $sql = "not set";
        try
        {
            $statement = $pdo->prepare($opts["sql"]);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            if(0 != count($data))
            {
                $data = $data[0];
                if(isset($data['id']))
                {
                    $changed = false;
                    $sql = "INSERT INTO p_log (action, on_table, on_id, on_column, old_value, new_value, user)"
                        . ' VALUES ("UPDATE", "' . $opts["table"] . '", ' . $data['id']. ', ?, ?, ?, ?)';
                    $statement = $pdo->prepare($sql);

                    $columns = $opts["columns"];
                    $updateSql = "UPDATE " . $opts["table"];

                    for($columnIdx = 0; $columnIdx < count($columns); $columnIdx++)
                    {
                        $curCol = $columns[$columnIdx];
                        if(isset($_GET[$curCol]))
                        {
                            $val = sanitize_string($_GET[$curCol]);
                            header('X-change_col_' . $curCol .': ' . $val . ' -> ' . sanitize_string($data[$curCol]));
                            $identical = True;
                            if(isset($opts['int_columns']))
                            {
                                if(in_array($curCol, $opts["int_columns"]))
                                {
                                    if((str_starts_with($val, '0x')) || (str_starts_with($val, '0X')))
                                    {
                                        if(intval($val, 16) != intval($data[$curCol]))
                                        {
                                            $val = intval($val, 16);
                                            $identical = False;
                                        }
                                        // else $identical = True;
                                    }
                                    else
                                    {
                                        if(intval($val) != intval($data[$curCol]))
                                        {
                                            $val = intval($val);
                                            $identical = False;
                                        }
                                        // else $identical = True;
                                    }
                                }
                                else if($val != $data[$curCol])
                                {
                                    $identical = False;
                                }
                                // else $identical = True;
                            }
                            else
                            {
                                if($val != $data[$curCol])
                                {
                                    $identical = False;
                                }
                                // else $identical = True;
                            }

                            if (False == $identical)
                            {
                                // log the change
                                $statement->execute(array($curCol, $data[$curCol], $val, $user_data["name"]));
                                $statement->fetchAll(PDO::FETCH_ASSOC);
                                // extend the update sql
                                if(False == $changed)
                                {
                                    $updateSql = $updateSql . " SET " . $curCol . ' = "' . $val . '"';
                                }
                                else
                                {
                                    $updateSql = $updateSql . ", " . $curCol . ' = "' . $val . '"';
                                }
                                $data[$curCol] = $val;
                                $changed = True;
                            }
                        }
                    }

                    if(True == $changed)
                    {
                        // echo("now updating");
                        $updateSql = $updateSql . " WHERE id = " . $data['id'];
                        header('X-SQL: ' . $updateSql);
                        $sql = $updateSql;
                        $statement = $pdo->prepare($sql);
                        $statement->execute();
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
                    header("X-debug: no such item -> can not update : " . $sql);
                    header('HTTP/1.0 400 Bad Request');
                }
            }
            else
            {
                header("X-debug: read old value -> no data : " . $opts["sql"]);
                header('HTTP/1.0 500 Internal Server Error');
            }
        }
        catch (PDOException $e)
        {
            header("X-debug: exception -> can not update : " . $sql);
            header("X-EXCEPTION: " . $e->getMessage());
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