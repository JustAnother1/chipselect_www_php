<?php
include("start.inc");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>add new Vendor</title>
        <meta  http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
<?php
include ("header.inc");
include ("rest/sanitize.inc");

echo("<br />\n");

if(isset($_POST['add']))
{
    if((isset($_SESSION['login'])) && ($_SESSION["login"] == 1))
    {
        // logged in - > change DB
        include ("../secret.inc");
        $pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $vendorName = sanitize_string($_POST['vendor_name']);
        $vendorUrl = sanitize_string($_POST['vendor_url']);

        echo("<p>name : " . $vendorName . "</p>");
        echo("<p>URL : " . $vendorUrl . "</p>");
        
        // make change
        $sql = 'INSERT INTO p_vendor (name, url) VALUES ("' . $vendorName . '", "' . $vendorUrl . '")';
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        // echo("now requesting id");
        $sql = "SELECT LAST_INSERT_ID()";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        $entryId = $data[0]["LAST_INSERT_ID()"];
        
        // log change
        $sql = "INSERT INTO p_log (action, on_table, on_id, on_column, new_value, user)"
        . ' VALUES ("INSERT", "p_vendor", ?, ?, ?, ?)';
        $statement = $pdo->prepare($sql);
        // name
        $statement->execute(array($entryId, "name", $vendorName, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // URL
        $statement->execute(array($entryId, "url", $vendorUrl, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo("<p>new Vendor has been created!</p>");
    }
}
else
{
    if((isset($_SESSION['login'])) && ($_SESSION["login"] == 1))
    {
        // logged in - > send form
        echo("<form method=\"POST\">\n");
        echo("vendor name: <input name=\"vendor_name\" ><br />\n");
        echo("vendor URL: <input name=\"vendor_url\" ><br />");
        echo("<input type=submit name=add value=\"add\">");
        echo("</form>");
    }
    else
    {
        // not logged in - > send email
        echo("<p>You are not logged in. If you do not have an account you can <a href=\"mailto:info@chipselect.org");
        echo("?subject=add vendor");
        echo("&body=chipselect.org%20team,%0A%0D%0A%0DThere%20is%20a%20vendor%20missing%20in%20your%20list:%0A%0D%0A%0Dvendor%20name:%0A%0D%0A%0DURL:%0A%0D%0A%0Dbest%20regards,%0A%0D%0A%0Dyour%20name");
        echo("\">report the missing vendor to us</a></p>");
        echo("<p>If you want to register an account send an email to <a href=\"mailto:info@chipselect.org\">info@chipselect.org</a></p>");
    }
}
echo("<br />\n");

include ("footer.inc");
        ?>
    </body>
</html>