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
    echo("<br/><h1>User account settings</h1>");
    include ("../secret.inc");
    $pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $stmt = $pdo->prepare('SELECT name, password, full_name, email, roles FROM p_user WHERE name = ?');
    if(true == $stmt->execute(array($_SESSION["login_name"])))
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
}
echo("<br/>");
include ("footer.inc");
        ?>
    </body>
</html>