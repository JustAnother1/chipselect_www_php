<?php

function handle_get($pdo, $opts)
{
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

    if((false == $found) && (false == $opts["allowUnfiltered"]))
    {
        header('HTTP/1.0 400 Bad Request');
    }
    else
    {
        if(false == $found)
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
            $opts["sql"] = $opts["sql"]
                . " WHERE (id > " . $left_off_id . ")"
                . " ORDER BY id ASC"
                . " LIMIT " . $limit;
        }

        header('X-SQL: ' . $opts["sql"]);
        try
        {
            $statement = $pdo->prepare($opts["sql"]);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            if(isset($opts["hexColumns"]))
            {
                $toHexCol = $opts["hexColumns"];
                $hexIdx = 0;
                for($hexIdx = 0; $hexIdx< count($toHexCol); $hexIdx++)
                {
                    foreach($data as &$per) // the & before $per makes the changes to the content apply to $data
                    {
                        $val = $per[$toHexCol[$hexIdx]];
                        if (str_starts_with($val, '0x'))
                        {
                            // already hex
                        }
                        else if (str_starts_with($val, '0X'))
                        {
                            // also already hex
                        }
                        else
                        {
                            // convert decimal value to hex
                            $val = "0x" . dechex($val);
                            $per[$toHexCol[$hexIdx]] = $val;
                        }
                    }
                }
            }
            echo json_encode($data);
        }
        catch (PDOException $e)
        {
            header('HTTP/1.0 500 Internal Server Error');
        }
    }
}
?>
