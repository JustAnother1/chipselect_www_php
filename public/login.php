<?php
include("start.inc");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Login to ChipSelect</title>
        <meta  http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
<?php
if (isset($_POST["login_name"]) && isset($_POST["login_passwort"]))
{
    include ("../secret.inc");
    $pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $stmt = $pdo->prepare('SELECT name, password, full_name FROM p_user WHERE name = ?');
    if(true == $stmt->execute(array($_POST["login_name"])))
    {
        $row = $stmt->fetch();
        if(false != $row)
        {
            // check password
            if(true == password_verify($_POST["login_passwort"], $row["password"]))
            {
                $_SESSION["login"] = 1;
                $_SESSION['full_name'] = $row["full_name"];
                $_SESSION['login_name'] = $row["name"];
            }
        }
    }
}

include ("header.inc");

if ((isset($_SESSION['login'])) && ($_SESSION["login"] == 1))
{
    ?>
    <b>You have been logged in.</b><br>
    <?php
}
else
{
    ?>
    <form method="POST">
    <b>Login</b><br />
    <br />
    Username: <input name="login_name"><br />
    Password: <input name="login_passwort" type=password><br />
    <br />
    <input type=submit name=submit value="login">
    </form>
    <p>If you want to register an account send an email to <a href="mailto:kontakt@ing-poetter.de">kontakt@ing-poetter.de</a></p>
    <?php
}

include ("footer.inc");
        ?>
    </body>
</html>