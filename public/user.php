<?php
include("start.inc");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>ChipSelect by Vendor</title>
        <meta  http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
<?php
include ("header.inc");
if((isset($_SESSION['login'])) && ($_SESSION["login"] == 1))
{
    include ("../secret.inc");
    $pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if(isset($_POST['change_password']))
    {
        $stmt = $pdo->prepare('SELECT password FROM p_user WHERE name = ?');
        if(true == $stmt->execute(array($_SESSION['name'])))
        {
            $row = $stmt->fetch();
            if(false != $row)
            {
                // check password
                if(true == password_verify($_POST['old_password'], $row["password"]))
                {
                    // old password matches -> update password
                    $pw = password_hash($_POST['new_password'],  PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('UPDATE p_user SET password = ? WHERE name = ?');
                    if(true == $stmt->execute(array($pw , $_SESSION['name'])))
                    {
                        echo("<br/><h1>Password has been changed!</h1>");
                    }
                }
            }
        }
    }


    echo("<br/><h1>User account settings</h1>");

    $stmt = $pdo->prepare('SELECT name, password, full_name, email, roles FROM p_user WHERE name = ?');
    if(true == $stmt->execute(array($_SESSION["name"])))
    {
        $row = $stmt->fetch();
        if(false != $row)
        {
            echo("<p>user name : " . $row["name"] . "</p>");
            echo("<p>full Name : " . $row["full_name"] . "</p>");
            echo("<p>email : " . $row["email"] . "</p>");
            echo("<p>role : " . $row["roles"] . "</p>");
        }
    }

    // change password
    echo("<br/><h2>change password</h2>\n");
    echo("<form method=\"POST\">\n");
    echo("current password: <input name=\"old_password\" type=password><br />\n");
    echo("new password: <input name=\"new_password\" type=password><br />");
    echo("<input type=submit name=change_password value=\"change password\">");
    echo("</form>");
}
echo("<br/>");
include ("footer.inc");
        ?>
    </body>
</html>