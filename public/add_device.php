<?php
include("start.inc");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>add new device</title>
        <meta  http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
<?php
include ("header.inc");
include ("rest/sanitize.inc");
include ("add.inc");
include ("../secret.inc");

function getIdForVendorName($pdo, $name) {
    $stmt = $pdo->prepare('SELECT id FROM p_vendor WHERE alternative = 0 AND name = ?');
    $stmt->execute(array($name));
    $row = $stmt->fetch();
    return $row[0];
}

function inputSvdId($pdo, $vendor_name) {
    $vid = getIdForVendorName($pdo, $vendor_name);
    $sql = 'SELECT name'
        . ' FROM microcontroller'
        . ' WHERE svd_id = 0 AND vendor_id =' . $vid
        . ' ORDER BY name';
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute();
    $res = $stmt->fetchAll();
    
    $size = count($res);
    echo("another device that has the same peripherals/Registers if any:<select name=\"svd_ref\" size=\"1\">\n");
    echo("    <option>no device</option>\n");
    for ($i = 0; $i < $size; $i++) {
        $row = $res[$i];
        if(NULL != $row['name']) {
            echo("    <option>" . $row['name']);
            echo("</option>\n");
        }
    }
    echo("</select><br />\n");
}

function getSvdIdForName($pdo, $device_name) {
    $stmt = $pdo->prepare('SELECT id FROM microcontroller WHERE name = ?');
    $stmt->execute(array($device_name));
    $row = $stmt->fetch();
    return $row[0];
}

function inputArchitecture($pdo) {
    $sql = 'SELECT name, revision, endian'
        . ' FROM p_architecture'
        . ' WHERE alternative = 0'
        . ' ORDER BY name';
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute();
    $res = $stmt->fetchAll();
    
    $size = count($res);
    echo("architecture:<select name=\"architecture\" size=\"1\">\n");
    
    for ($i = 0; $i < $size; $i++) {
        $row = $res[$i];
        if(NULL != $row['name']) {
            echo("    <option>" . $row['name']);
            if(NULL != $row['revision']) {
                echo("/" . $row['revision']);
            }
            if(NULL != $row['endian']) {
                echo("/" . $row['endian']);
            }
            echo("</option>\n");
        }
    }
    echo("</select><br />\n");
}

function getArchitectureIdForName($pdo, $name) {
    $pieces = explode("/", $name);
    $size = count($pieces);
    $sql = 'SELECT id'
        . ' FROM p_architecture'
        . ' WHERE name = "' . $pieces[0] . '"';
    if($size > 1) {
        $sql = $sql . ' AND revision = "' . $pieces[1] . '"';
    }
    if($size > 2) {
        $sql = $sql . ' AND endian = "' . $pieces[2] . '"';
    }
    print($sql);
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row[0];
}


function inputMarketState($pdo) {
    $sql = 'SELECT name'
        . ' FROM p_market_state'
        . ' ORDER BY name';
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute();
    $res = $stmt->fetchAll();
    
    $size = count($res);
    echo("market state:<select name=\"market_state\" size=\"1\">\n");
    
    for ($i = 0; $i < $size; $i++) {
        $row = $res[$i];
        if(NULL != $row['name']) {
            echo("    <option>" . $row['name'] . "</option>\n");
        }
    }
    echo("</select><br />\n");
}

function getMarketStateIdForName($pdo, $name) {
    $stmt = $pdo->prepare('SELECT id FROM p_market_state WHERE name = ?');
    $stmt->execute(array($name));
    $row = $stmt->fetch();
    return $row[0];
}

function inputPackage($pdo) {
    $sql = 'SELECT name'
        . ' FROM p_package'
        . ' ORDER BY name';
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute();
    $res = $stmt->fetchAll();
    
    $size = count($res);
    echo("package:<select name=\"package\" size=\"1\">\n");
    
    for ($i = 0; $i < $size; $i++) {
        $row = $res[$i];
        if(NULL != $row['name']) {
            echo("    <option>" . $row['name'] . "</option>\n");
        }
    }
    echo("</select><br />\n");
}

function getPackageIdForName($pdo, $name) {
    $stmt = $pdo->prepare('SELECT id FROM p_package WHERE name = ?');
    $stmt->execute(array($name));
    $row = $stmt->fetch();
    return $row[0];
}


echo("<br />\n");

$vendor_name = $_GET['vendor'];

if(isset($_POST['add']))
{
    if((isset($_SESSION['login'])) && ($_SESSION["login"] == 1))
    {
        // logged in - > change DB
        $pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        $deviceName = sanitize_string($_POST['name']);
        $CpuClockMhz = sanitize_string($_POST['CPU_clock_max_MHz']);
        $FlashSize = sanitize_string($_POST['Flash_size_kB']);
        $RamSizeKb = sanitize_string($_POST['RAM_size_kB']);
        $RamSizeBytes = sanitize_string($_POST['RAM_size_byte']);
        $RamStart = sanitize_string($_POST['RAM_start_address']);
        $SupplyVoltageMin = sanitize_string($_POST['Supply_Voltage_min_V']);
        $SupplyVoltageMax = sanitize_string($_POST['Supply_Voltage_max_V']);
        $MinTemp = sanitize_string($_POST['Operating_Temperature_min_degC']);
        $MaxTemp = sanitize_string($_POST['Operating_Temperature_max_degC']);
        $AddressableUnit = sanitize_string($_POST['Addressable_unit_bit']);
        $BusWidth = sanitize_string($_POST['bus_width_bit']);
        $description = sanitize_string($_POST['description']);
        $VendorName = sanitize_string($_POST['vendor_name']);
        $VendorId = getIdForVendorName($pdo, $VendorName);
        $SvdRefName = sanitize_string($_POST['svd_ref']);
        $SvdId = getSvdIdForName($pdo, $SvdRefName);
        $ArchitectureName = sanitize_string($_POST['architecture']);
        $ArchitectureId = getArchitectureIdForName($pdo, $ArchitectureName);
        $MarketStateName = sanitize_string($_POST['market_state']);
        $MarketStateId = getMarketStateIdForName($pdo, $MarketStateName);
        $PackageName = sanitize_string($_POST['package']);
        $PackageId = getPackageIdForName($pdo, $PackageName);
        
        // make change
        $sql = 'INSERT INTO microcontroller (name, CPU_clock_max_MHz, Flash_size_kB, RAM_size_kB, Supply_Voltage_min_V, '
            . 'Supply_Voltage_max_V, Operating_Temperature_min_degC, Operating_Temperature_max_degC, svd_id, '
            . 'Addressable_unit_bit, bus_width_bit, description, architecture_id, market_state_id, package_id, '
            . 'vendor_id, RAM_size_byte, RAM_start_address) VALUES ("' 
            . $deviceName . '", "'
            . $CpuClockMhz . '", "'
            . $FlashSize . '", "'
            . $RamSizeKb . '", "'
            . $SupplyVoltageMin . '", "'
            . $SupplyVoltageMax . '", "'
            . $MinTemp . '", "'
            . $MaxTemp . '", "'
            . $SvdId . '", "'
            . $AddressableUnit . '", "'
            . $BusWidth . '", "'
            . $description . '", "'
            . $ArchitectureId . '", "'
            . $MarketStateId . '", "'
            . $PackageId . '", "'
            . $VendorId . '", "'
            . $RamSizeBytes . '", "'
            . $RamStart . '")';
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
        . ' VALUES ("INSERT", "microcontroller", ?, ?, ?, ?)';
        $statement = $pdo->prepare($sql);
        
        // name
        $statement->execute(array($entryId, "name", $deviceName, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // CPU_clock_max_MHz
        $statement->execute(array($entryId, "CPU_clock_max_MHz", $CpuClockMhz, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // Flash_size_kB
        $statement->execute(array($entryId, "Flash_size_kB", $FlashSize, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // RAM_size_kB
        $statement->execute(array($entryId, "RAM_size_kB", $RamSizeKb, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // Supply_Voltage_min_V
        $statement->execute(array($entryId, "Supply_Voltage_min_V", $SupplyVoltageMin, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // Supply_Voltage_max_V
        $statement->execute(array($entryId, "Supply_Voltage_max_V", $SupplyVoltageMax, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // Operating_Temperature_min_degC
        $statement->execute(array($entryId, "Operating_Temperature_min_degC", $MinTemp, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // Operating_Temperature_max_degC
        $statement->execute(array($entryId, "Operating_Temperature_max_degC", $MaxTemp, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // svd_id
        $statement->execute(array($entryId, "svd_id", $SvdId, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // Addressable_unit_bit
        $statement->execute(array($entryId, "Addressable_unit_bit", $AddressableUnit, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // bus_width_bit
        $statement->execute(array($entryId, "bus_width_bit", $BusWidth, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // description
        $statement->execute(array($entryId, "description", $description, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // architecture_id
        $statement->execute(array($entryId, "architecture_id", $ArchitectureId, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // market_state_id
        $statement->execute(array($entryId, "market_state_id", $MarketStateId, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // package_id
        $statement->execute(array($entryId, "package_id", $PackageId, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // vendor_id
        $statement->execute(array($entryId, "vendor_id", $VendorId, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // RAM_size_byte
        $statement->execute(array($entryId, "RAM_size_byte", $RamSizeBytes, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        // RAM_start_address
        $statement->execute(array($entryId, "RAM_start_address", $RamStart, $_SESSION['name']));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo("<p>new Device has been created!</p>");
    }
}
else
{
    if((isset($_SESSION['login'])) && ($_SESSION["login"] == 1))
    {
        // logged in - > send form
        $pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        echo("<form method=\"POST\">\n");
        echo("name: <input name=\"name\" ><br />\n");
        echo("max CPU clock: <input name=\"CPU_clock_max_MHz\" > MHz<br />\n");
        echo("Flash size in kB: <input name=\"Flash_size_kB\" > kB<br />\n");
        echo("RAM size in kB: <input name=\"RAM_size_kB\" >kB<br />\n");
        echo("RAM size in bytes: <input name=\"RAM_size_byte\" > Bytes<br />\n");
        echo("RAM start address: <input name=\"RAM_start_address\" ><br />\n");
        echo("supply Voltage min: <input name=\"Supply_Voltage_min_V\" > V <br />\n");
        echo("supply Voltage max: <input name=\"Supply_Voltage_max_V\" > V <br />\n");
        echo("operating temperature min (degree C): <input name=\"Operating_Temperature_min_degC\" ><br />\n");
        echo("operating temperature max (degree C): <input name=\"Operating_Temperature_max_degC\" ><br />\n");
        inputSvdId($pdo, $vendor_name);
        echo("addressable unit (in Bits): <input name=\"Addressable_unit_bit\" value=\"8\" ><br />\n");
        echo("bus width in bit: <input name=\"bus_width_bit\" value=\"32\" ><br />\n");
        inputArchitecture($pdo);
        inputMarketState($pdo);
        inputPackage($pdo);
        echo("description: <input name=\"description\" ><br />\n");
        echo("<input type=\"hidden\" name=\"vendor_name\" value=\"" . $vendor_name . "\">");
        echo("<input type=submit name=add value=\"add\">");
        echo("</form>");
    }
    else
    {
        // not logged in - > send email
        echo("<p>You are not logged in. If you do not have an account you can <a href=\"mailto:info@chipselect.org");
        echo("?subject=add%20device%20of%20" . $vendor_name);
        $mail = "chipselect.org team,\n\n"
              . "There is a device of " . $vendor_name . " missing :\n\n"
              . "name : \n\n"
              . "max CPU clock : \n\n"
              . "Flash size in kB : \n\n"
              . "RAM size in kB : \n\n"
              . "RAM size in bytes : \n\n"
              . "RAM start address : \n\n"
              . "supply Voltage min : \n\n"
              . "supply Voltage max : \n\n"
              . "operating temperature min (degree C) : \n\n"
              . "operating temperature max (degree C) : \n\n"
              . "name of other device that has the same peripherals/Registers if any : \n\n"
              . "addressable unit (in Bits) : \n\n"
              . "bus width in bit : \n\n"
              . "architecture : \n\n"
              . "market state : \n\n"
              . "package : \n\n"
              . "description : \n\n"
              . "best regards, \n\n\n"
              . "your name\n\n";
        $mail = StringToEmailBody($mail);
        echo("&body=" . $mail);
        echo("\">report the missing device to us</a></p>");
        echo("<p>If you want to register an account send an email to <a href=\"mailto:info@chipselect.org\">info@chipselect.org</a></p>");
    }
}
echo("<br />\n");

include ("footer.inc");
        ?>
    </body>
</html>