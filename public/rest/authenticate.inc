<?php
function authenticate($pdo)
{
    if (!isset($_SERVER['PHP_AUTH_USER']))
    {
        // no username / password provided
        header('WWW-Authenticate: Basic realm="chipselect"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    }
    else
    {
        $stmt = $pdo->prepare('SELECT name, password, full_name, email, roles FROM p_user WHERE name = ?');
        if(true == $stmt->execute(array($_SERVER['PHP_AUTH_USER'])))
        {
            $row = $stmt->fetch();
            if(false != $row)
            {
                // check password
                if(true == password_verify($_SERVER['PHP_AUTH_PW'], $row["password"]))
                {
                    $roles = explode(",", $row["roles"]);
                    $roles = array_map('trim', $roles);
                    $array = array(
                        "name" => $row["name"],
                        "full_name" => $row["full_name"],
                        "email" => $row["email"],
                        "user_roles" => $roles,
                    );
                    return $array;
                }
                else
                {
                    header('WWW-Authenticate: Basic realm="chipselect"');
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }
            }
            else
            {
                header('WWW-Authenticate: Basic realm="chipselect"');
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        }
        else
        {
            header('WWW-Authenticate: Basic realm="chipselect"');
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }
    }
}
?>