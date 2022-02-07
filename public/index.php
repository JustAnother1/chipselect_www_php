<?php
include("start.inc");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>ChipSelect - by feature</title>
        <meta  http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
<?php
include ("header.inc");
// filter
echo("<form method=\"get\">\n");
echo("<H1>Filter chips by feature</H1>\n");

if (isset($_GET['minFreq']))
{
    echo("<p> minimum Frequency : <input type=\"text\" name=\"minFreq\" value=" . $_GET['minFreq'] . " size=5 maxlength=4> MHz</p>\n");
} else {
    echo("<p> minimum Frequency : <input type=\"text\" name=\"minFreq\" value=48 size=5 maxlength=4> MHz</p>\n");
}

if (isset($_GET['minRAM']))
{
    echo("<p> minimum RAM size : <input type=\"text\" name=\"minRAM\" value=" . $_GET['minRAM'] . " size=5 maxlength=4> kB</p>\n");
} else {
    echo("<p> minimum RAM size : <input type=\"text\" name=\"minRAM\" value=0 size=5 maxlength=4> kB</p>\n");
}

if (isset($_GET['minFLASH']))
{
    echo("<p> minimum Flash size : <input type=\"text\" name=\"minFLASH\" value=" . $_GET['minFLASH'] . " size=5 maxlength=4> kB</p>\n");
} else {
    echo("<p> minimum Flash size : <input type=\"text\" name=\"minFLASH\" value=0 size=5 maxlength=4> kB</p>\n");
}

echo("<p> market state : <select name=\"market_state\">\n<option value=-1>any</option>\n");
include ("../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$sql = 'SELECT name, id FROM p_market_state ORDER by name';

$market_states = array();

foreach($pdo->query($sql) as $row) {
    $market_states[$row['id']] = $row['name'];
    if (isset($_GET['market_state']))
    {
        if($row['id'] == $_GET['market_state'])
        {
            echo("<option selected value=\"" . $row['id'] . "\" >" . $row['name'] . "</option>\n");
        }
        else
        {
            echo("<option value=\"" . $row['id'] . "\" >" . $row['name'] . "</option>\n");
        }
    }
    else
    {
        echo("<option value=\"" . $row['id'] . "\" >" . $row['name'] . "</option>\n");
    }
}
echo("</select></p>\n");

echo("<input type=\"submit\" name=\"Submit_name\" value=\"Apply\">\n");
// echo("<input type=\"reset\" name=\"reset_name\" value=\"Reset\">\n");
echo("</p>\n");
echo("</form>\n");


// Results
$has_Filter = False;
$join = False;
$where = array();

if (isset($_GET['minFreq']))
{
    if(0 !=  $_GET['minFreq'])
    {
        $has_Filter = True;
        $where[] = "CPU_clock_max_MHz >= " . $_GET['minFreq'];
    }
}
if (isset($_GET['minRAM']))
{
    if(0 !=  $_GET['minRAM'])
    {
        $has_Filter = True;
        $where[] = "RAM_size_kB >= " . $_GET['minRAM'];
    }
}
if (isset($_GET['minFLASH']))
{
    if(0 !=  $_GET['minFLASH'])
    {
        $has_Filter = True;
        $where[] = "Flash_size_kB >= " . $_GET['minFLASH'];
    }
}
if (isset($_GET['market_state']))
{
    if(-1 !=  $_GET['market_state'])
    {
        $has_Filter = True;
        $join = True;
        $where[] = "market_state_id = " . $_GET['market_state'];
    }
    else
    {
        unset($_GET['market_state']);
    }
}

if(True == $has_Filter)
{
    echo("<table>\n");
    echo("  <tr>\n");
    echo("    <th>Vendor</th>\n");
    echo("    <th>Name</th>\n");
    echo("    <th>max Clock(MHz)</th>\n");
    echo("    <th>Flash (kB)</th>\n");
    echo("    <th>RAM (kB)</th>\n");
    echo("    <th>Vcc min(V)</th>\n");
    echo("    <th>Vcc max(V)</th>\n");
    echo("    <th>operation temperature min(°C)</th>\n");
    echo("    <th>operation temperature max(°C)</th>\n");
    if(isset($_GET['market_state']))
    {
        echo("    <th>market state</th>\n");
    }
    echo("    <th>description</th>\n");
    echo("  </tr>\n");

    // Vendor
    $sql = 'SELECT name, id FROM p_vendor';
    $vendors = array();
    foreach($pdo->query($sql) as $row) {
        $vendors[$row['id']] = $row['name'];
    }

    $fields = "name, id, CPU_clock_max_MHz, Flash_size_kB, RAM_size_kB, Supply_Voltage_min_V, Supply_Voltage_max_V, Operating_Temperature_min_degC, Operating_Temperature_max_degC, svd_id, description, vendor_id, market_state_id";
    $sql = "SELECT " . $fields . " FROM microcontroller";

    if(1 == count($where))
    {
        $sql = $sql . " WHERE " . $where[0];
    }
    else
    {
        $first = True;
        $wc = " WHERE ";
        foreach($where as $clause) {
            if(True == $first)
            {
                $wc = $wc . "(" . $clause . ")";
                $first = False;
            }
            else {
                $wc = $wc . " AND (" . $clause . ")";
            }
        }
        $sql = $sql . $wc;
    }
    $sql = $sql . " ORDER by name";

    // echo("SQL: " . $sql);

    foreach($pdo->query($sql) as $row) {
        echo("  <tr>\n");
        echo("    <td><a href=\"vendor_id.php?name=" . $vendors[$row['vendor_id']] . "\">"
            . $vendors[$row['vendor_id']] . "</a></td>\n");
        if ( 0 == $row['svd_id']) {
            // Name
            echo('    <td><a href="device_id.php?id=' . $row['id'] . '">' . $row['name'] . "</a></td>\n");
        } else {
            echo('    <td><a href="device_id.php?id=' . $row['svd_id'] . '">' . $row['name'] . "</a></td>\n");
        }
        // max Clock(MHz)
        echo("    <td>" . $row['CPU_clock_max_MHz'] . "</td>\n");
        // Flash (kB)
        echo("    <td>" . $row['Flash_size_kB'] . "</td>\n");
        // RAM (kB)
        echo("    <td>" . $row['RAM_size_kB'] . "</td>\n");
        // Vcc min(V)
        echo("    <td>" . $row['Supply_Voltage_min_V'] . "</td>\n");
        // Vcc max(V)
        echo("    <td>" . $row['Supply_Voltage_max_V'] . "</td>\n");
        // operation temperature min(°C)
        echo("    <td>" . $row['Operating_Temperature_min_degC'] . "</td>\n");
        // operation temperature max(°C)
        echo("    <td>" . $row['Operating_Temperature_max_degC'] . "</td>\n");
        if(isset($_GET['market_state']))
        {
            echo("    <td>" . $market_states[$row['market_state_id']] . "</td>\n");
        }
        // description
        echo("    <td>" . $row['description'] . "</td>\n");
        // end of row
        echo("     </tr>\n");
    }
    echo("  </table>\n");
}
else
{
    echo("Select options to search for the devices.");
}

include ("footer.inc");
        ?>
    </body>
</html>
