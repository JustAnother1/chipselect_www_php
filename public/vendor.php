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
echo("<br />\n<h1>Vendors:</h1>\n");
echo("<a href=\"add_vendor.php\" >add a new vendor </a>\n<br />\n<br />\n");
include ("../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$sql = 'SELECT name, id FROM p_vendor WHERE alternative = 0 ORDER by name';
foreach($pdo->query($sql) as $row) {
    echo("<a href=\"vendor_id.php?name=" . $row['name'] . "\" >" . $row['name'] . "</a><br />\n");
}
echo("<br />\n");
include ("footer.inc");
        ?>
    </body>
</html>
