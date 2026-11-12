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
session_destroy();
$_SESSION["login"] = 0;

include ("header.inc");

echo("<b>You have been logged out.</b><br>");

include ("footer.inc");
?>
    </body>
</html>