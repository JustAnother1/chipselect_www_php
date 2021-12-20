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
echo("<br/><h1>Vendors:</h1>");
include ("../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$sql = 'SELECT name, id FROM p_vendor WHERE alternative = 0 ORDER by name';
foreach($pdo->query($sql) as $row) {
    echo('<a href="vendor_id.php?id=' . $row['id'] . '" >' . $row['name'] . '</a></br>');
}
echo("<br/>");
include ("footer.inc");
        ?>
    </body>
</html>
