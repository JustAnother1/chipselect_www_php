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

$device_id = $_GET['id'];

// check if it is an int
if(!is_numeric($device_id)) {
    echo("    </head>\n");
    echo("    <body>\n");
    echo("     <h1> Invalid device id of " . $device_id . "</h1>\n");
    echo("    </body>\n");
    echo("</html>\n");
    exit;
}

$sql = 'SELECT name, CPU_clock_max_MHz, Flash_size_kB, RAM_size_kB, Supply_Voltage_min_V, Supply_Voltage_max_V, Operating_Temperature_min_degC, Operating_Temperature_max_degC, svd_id, description'
    . ' FROM microcontroller '
    . ' WHERE id = ?';
$stmt = $dbh->prepare($sql);
if(false == $stmt->execute(array($device_id))) {
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
    echo("     <h1> Invalid device id of " . $device_id . "</h1>\n");
    echo("    </body>\n");
    echo("</html>\n");
    exit;
}
$device_data = $row;
if ( 0 == $row['svd_id']) {
    $device_data['id'] =  $device_id;
} else {
    $device_data['id'] =  $row['svd_id'];
}
echo('<title>' . $device_data['name'] . '</title>');
        ?>
        <meta  http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>

<?php
include ("header.inc");
echo("<h1>" . $device_data['name'] . "</h1>\n");
// Vendor
$sql = 'SELECT name, url, id'
    . ' FROM p_vendor INNER JOIN pl_vendor ON pl_vendor.vendor_id = p_vendor.id'
    . ' WHERE pl_vendor.dev_id = ?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($device_data['id']));
$row = $stmt->fetch();
if( false != $row) {
    echo("<div id=\"vendor\">\n");
    echo("    <p>Vendor Web: <a href=\"" . $row['url'] . "\">" . $row['name'] . "</a></p>\n");
    echo("    <p><a href=\"" .  $row['name'] . "--" . $device_data['name'] . ".svd\" download>download svd file</a></p>\n");
    echo("    <p><a href=\"vendor_id.php?id=" . $row['id'] . "\">All devices of this Vendor</a></p>\n");
    echo("</div>\n");
}

// general Device Data
echo("<div id=\"general\">\n");
echo("    <p>Name : " . $device_data['name'] . "</p>\n");
if( NULL != $device_data['CPU_clock_max_MHz']) {
    echo("    <p>max Clock : " . $device_data['CPU_clock_max_MHz'] . " MHz</p>\n");
}
if( NULL != $device_data['Flash_size_kB']) {
echo("    <p>Flash : " . $device_data['Flash_size_kB'] . " kB</p>\n");
}
if( NULL != $device_data['RAM_size_kB']) {
echo("    <p>RAM : " . $device_data['RAM_size_kB'] . " kB</p>\n");
}
if( NULL != $device_data['Supply_Voltage_min_V']) {
echo("    <p>V<sub>cc</sub> min : " . $device_data['Supply_Voltage_min_V'] . " V</p>\n");
}
if( NULL != $device_data['Supply_Voltage_max_V']) {
echo("    <p>V<sub>cc</sub> max : " . $device_data['Supply_Voltage_max_V'] . " V</p>\n");
}
if( NULL != $device_data['Operating_Temperature_min_degC']) {
echo("    <p>operation temperature min : " . $device_data['Operating_Temperature_min_degC'] . " °C</p>\n");
}
if( NULL != $device_data['Operating_Temperature_max_degC']) {
echo("    <p>operation temperature max : " . $device_data['Operating_Temperature_max_degC'] . " °C</p>\n");
}
if( NULL != $device_data['description']) {
echo("    <p>description : " . $device_data['description'] . "</p>\n");
}
echo("</div>\n");

// Package
$sql = 'SELECT name'
    . ' FROM  p_package INNER JOIN pl_package ON pl_package.package_id =  p_package.id'
    . ' WHERE pl_package.dev_id = ?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($device_data['id']));
$row = $stmt->fetch();
if( false != $row) {
    echo("<div id=\"package\">\n");
    echo("    <p>Package : " . $row['name'] . "</a></p>\n");
    echo("</div>\n");
}

// Market State
$sql = 'SELECT name'
    . ' FROM  p_market_state INNER JOIN pl_market_state ON pl_market_state.market_state_id =  p_market_state.id'
    . ' WHERE pl_market_state.dev_id = ?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($device_data['id']));
$row = $stmt->fetch();
if( false != $row) {
    echo("<div id=\"market_state\">\n");
    echo("    <p>Market State : " . $row['name'] . "</a></p>\n");
    echo("</div>\n");
}

// Architecture
$sql = 'SELECT name, svd_name, revision, endian, hasMPU, hasFPU, interrupt_prio_bits, ARM_Vendor_systick'
    . ' FROM p_architecture INNER JOIN pl_architecture ON pl_architecture.arch_id = p_architecture.id'
    . ' WHERE pl_architecture.dev_id = ?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($device_data['id']));
$row = $stmt->fetch();
if( false != $row) {
    echo("<div id=\"architecture\">\n");
    echo("<h2>Architecture</h2>\n");
    // echo("<!-- " . json_encode($row) . " -->\n");
    echo("    <p>Architecture : " . $row['name']);
    if(NULL != $row['svd_name']) {
        print(" (" . $row['svd_name'] . ")</p>\n");
    } else {
        print("</p>\n");
    }
    if(NULL != $row['revision']) {
        print("    <p>revision : " . $row['revision'] . "</p>\n");
    }
    if(NULL != $row['endian']) {
        print("    <p>endian : " . $row['endian'] . "</p>\n");
    }
    if(NULL != $row['hasMPU']) {
        if(true == $row['hasMPU']) {
            print("    <p>Memory Protection Unit (MPU) : available</p>\n");
        } else {
            print("    <p>Memory Protection Unit (MPU) : not available</p>\n");
        }
    }
    if(NULL != $row['hasFPU']) {
        if(true == $row['hasFPU']) {
            print("    <p>Floating Point Unit (FPU) : available</p>\n");
        } else {
            print("    <p>Floating Point Unit (FPU) : not available</p>\n");
        }
    }
    if(NULL != $row['interrupt_prio_bits']) {
        print("    <p>Number of relevant bits in Interrupt priority : " . $row['interrupt_prio_bits'] . "</p>\n");
    }
    if(NULL != $row['ARM_Vendor_systick']) {
        if(true == $row['ARM_Vendor_systick']) {
            print("    <p>Systick : vendor specific</p>\n");
        } else {
            print("    <p>Systick : as defined by ARM</p>\n");
        }
    }
    echo("</div>\n");
}
// else architecture unknown

// Peripheral Instances + Interrupts
$sql = 'SELECT name, description, base_address, peripheral_id, per_in_id'
    . ' FROM p_peripheral_instance INNER JOIN  pl_peripheral_instance ON  pl_peripheral_instance.per_in_id  = p_peripheral_instance.id'
    . ' WHERE pl_peripheral_instance.dev_id = ?'
    . ' ORDER BY name';
$stmt = $dbh->prepare($sql);

$irq_sql = 'SELECT name, description, number'
        . ' FROM p_interrupt INNER JOIN pl_interrupt ON pl_interrupt.irq_id     = p_interrupt.id'
        . ' WHERE pl_interrupt.per_in_id = ?'
        . ' ORDER BY number';
$irq_stmt = $dbh->prepare($irq_sql);


$stmt->execute(array($device_data['id']));
echo("<div id=\"peripheral_instance\">\n");
echo("<h2>Peripherals</h2>\n");
foreach ($stmt as $row) {
    echo("    <p>name : <a href=\"peripheral_id.php?id=" . $row['peripheral_id'] . "\">" . $row['name'] . "</a><br />\n");
    echo("    description : " . $row['description'] . "</a><br />\n");
    echo("    base address : 0x" . dechex(intval($row['base_address'])) . "<br />\n");
    $irq_stmt->execute(array($row['per_in_id']));
    foreach ($irq_stmt as $irq_row) {
        echo("    Interrupt (" . $irq_row['number'] . ")  " .  $irq_row['name'] . " : " . $irq_row['description'] . "<br />\n");
    }
    echo("</p>\n");
}
echo("</div>\n");

// Footer
include ("footer.inc");
        ?>
    </body>
</html>
