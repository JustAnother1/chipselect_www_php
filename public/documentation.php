<?php
include("start.inc");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>ChipSelect - documentation</title>
        <meta  http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
<?php
include ("header.inc");
?>
<H1>Interfaces</H1>
Chipselect provides the information on the following interfaces:

<H2>Web</H2>
You can use the "Chips by Vendor" and "Chips by Feature" pages to get the information you need.

<H2>SVD download</H2>
For all Chips SVD files can be directly downloaded. They are available at http://chipselect.org/<vendor name>--<device name>.svd<br />
Example: http://chipselect.org/STMicroelectronics--STM32F407.svd

<H2>REST API</H2>
The REST API is available at http://chipselect.org/rest/<br />
Ressources are:

vendor
Modes: GET
Filter: id, name

Example: http://chipselect.org/rest/vendor?name=STMicroelectronics


microcontroller
Modes: GET
Filter: id, name, limit, left_off_id
Example: http://chipselect.org/rest/microcontroller?limit=5&left_off_id=100

<?php
include ("footer.inc");
        ?>
    </body>
</html>
