<?php
include("start.inc");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
<?php
include ("../secret.inc");
// connect to database
$dbh = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$vendor_name = $_GET['name'];
// $vendor_id = $_GET['id'];
// // check if it is an int
// if(!is_numeric($vendor_id)) {
//     echo("    </head>\n");
//     echo("    <body>\n");
//     echo("     <h1> Invalid vendor id of " . $vendor_id . "</h1>\n");
//     echo("    </body>\n");
//     echo("</html>\n");
//     exit;
// }

// if we get more information regarding the Vendor into p_vendor then we could request that and show it as an introduction to the vendors devices
$stmt = $dbh->prepare('SELECT id, name, url, alternative FROM p_vendor WHERE name = ?');
if(false == $stmt->execute(array($vendor_name))) {
    echo("    </head>\n");
    echo("    <body>\n");
    echo("     <h1> Can not talk to database !</h1>\n");
    echo("    </body>\n");
    echo("</html>\n");
    exit;
}
$row = $stmt->fetch();
if(false == $row) {
    echo("    </head>\n");
    echo("    <body>\n");
    echo("     <h1> Invalid vendor name of " . $vendor_name . "</h1>\n");
    echo("    </body>\n");
    echo("</html>\n");
    exit;
}
$vendor_name =  $row['name'];
$vendor_url =  $row['url'];
if(0 != $row['alternative'])
{
    $vendor_id = $row['alternative'];
}
else
{
    $vendor_id = $row['id'];
}

echo('<title>' . $vendor_name . '</title>');
        ?>
        <meta  http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>

<?php
include ("header.inc");
echo('<p><a href="index.php">all vendors' . "</a></p>\n");
echo("<h1>" . $vendor_name . "</h1>\n");
echo('<p><a href="' . $vendor_url . '">' . $vendor_url . "</a></p>\n");
// if we get more information regarding the Vendor into p_vendor then we could request that and show it as an introduction to the vendors devices
echo("<h2>Devices</h2>\n");

echo("<table>\n");
echo("  <tr>\n");
echo("    <th>Name</th>\n");
echo("    <th>max Clock(MHz)</th>\n");
echo("    <th>Flash (kB)</th>\n");
echo("    <th>RAM (kB)</th>\n");
echo("    <th>Vcc min(V)</th>\n");
echo("    <th>Vcc max(V)</th>\n");
echo("    <th>operation temperature min(°C)</th>\n");
echo("    <th>operation temperature max(°C)</th>\n");
echo("    <th>description</th>\n");
echo("  </tr>\n");
$sql = 'SELECT name, id, CPU_clock_max_MHz, Flash_size_kB, RAM_size_kB, Supply_Voltage_min_V, Supply_Voltage_max_V, Operating_Temperature_min_degC, Operating_Temperature_max_degC, svd_id, description'
    . ' FROM microcontroller'
    . ' WHERE vendor_id = ?'
    . ' ORDER by name';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($vendor_id));
foreach ($stmt as $row) {
    echo("  <tr>\n");
    // echo("<!-- " . json_encode($row) . " -->\n");
    if ( 0 == $row['svd_id']) {
    // Name
    echo('    <td><a href="device_id.php?id=' . $row['id']);
    } else {
    echo('    <td><a href="device_id.php?svd_id=' . $row['svd_id']);
    }
    echo('&name=' . $row['name'] . '&vendor=' . $vendor_name . '">' . $row['name'] . "</a></td>\n");

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
    // description
    echo("    <td>" . $row['description'] . "</td>\n");

    // end of row
    echo("     </tr>\n");
}
echo("  </table>\n");

include ("footer.inc");
        ?>
    </body>
</html>
