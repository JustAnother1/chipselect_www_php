<?php

function print_cpu_element($xml_prefix, $xml_indent_step, $dbh, $architecture_id) {
    // Architecture
    $sql = 'SELECT name, svd_name, revision, endian, hasMPU, hasFPU, interrupt_prio_bits, ARM_Vendor_systick'
        . ' FROM p_architecture'
        . ' WHERE id = ?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($architecture_id));
    $row = $stmt->fetch();
    if( false != $row) {
        echo($xml_prefix . "<cpu>\n");
        $cpu_indent = $xml_prefix . $xml_indent_step;
        echo($cpu_indent . "<!--" . $row['name'] . "-->\n");
        if(NULL != $row['svd_name']) {
            print($cpu_indent . "<name>" . $row['svd_name'] . "</name>\n");
        }
        if(NULL != $row['revision']) {
            print($cpu_indent . "<revision>" . $row['revision'] . "</revision>\n");
        }
        if(NULL != $row['endian']) {
            print($cpu_indent . "<endian>" . $row['endian'] . "</endian>\n");
        }
        if(NULL != $row['hasMPU']) {
            if(true == $row['hasMPU']) {
                print($cpu_indent . "<mpuPresent>true</mpuPresent>\n");
            } else {
                print($cpu_indent . "<mpuPresent>false</mpuPresent>\n");
            }
        }
        if(NULL != $row['hasFPU']) {
            if(true == $row['hasFPU']) {
                print($cpu_indent . "<fpuPresent>true</fpuPresent>\n");
            } else {
                print($cpu_indent . "<fpuPresent>false</fpuPresent>\n");
            }
        }
        if(NULL != $row['interrupt_prio_bits']) {
            print($cpu_indent . "<nvicPrioBits>" . $row['interrupt_prio_bits'] . "</nvicPrioBits>\n");
        }
        if(NULL != $row['ARM_Vendor_systick']) {
            if(true == $row['ARM_Vendor_systick']) {
                print($cpu_indent . "<vendorSystickConfig>true</vendorSystickConfig>\n");
            } else {
                print($cpu_indent . "<vendorSystickConfig>false</vendorSystickConfig>\n");
            }
        }
        else
        {
            print($cpu_indent . "<vendorSystickConfig>false</vendorSystickConfig>\n");
        }
        echo($xml_prefix. "</cpu>\n");
    }
    // else architecture unknown
}

function print_addressBlock_element($xml_prefix, $xml_indent_step, $dbh, $peripheral_id ) {
    $sql = 'SELECT address_offset, size, mem_usage, protection'
        . ' FROM p_address_block  INNER JOIN  pl_address_block  ON  pl_address_block.addr_id  = p_address_block.id'
        . ' WHERE pl_address_block.per_id = ?'
        . ' ORDER BY address_offset';
    $stmt = $dbh->prepare($sql);

    $stmt->execute(array($peripheral_id));
    foreach ($stmt as $row) {
        echo($xml_prefix . "<addressBlock>\n");
        $elements_indent = $xml_prefix . $xml_indent_step;
        if(NULL != $row['address_offset']) {
            echo($elements_indent . "<offset>" . $row['address_offset'] . "</offset>\n");
        }
        else
        {
            echo($elements_indent . "<offset>0x0</offset>\n");
        }
        if(NULL != $row['size']) {
            echo($elements_indent . "<size>" . $row['size'] . "</size>\n");
        }
        if(NULL != $row['mem_usage']) {
            echo($elements_indent . "<usage>" . $row['mem_usage'] . "</usage>\n");
        } else {
            echo($elements_indent . "<usage>registers</usage>\n");
        }
        if(NULL != $row['protection']) {
            echo($elements_indent . "<protection>" . $row['protection'] . "</protection>\n");
        } else {
            echo($elements_indent . "<protection>n</protection>\n");
        }
        echo($xml_prefix . "</addressBlock>\n");
    }
}

function print_interrupt_element($xml_prefix, $xml_indent_step, $dbh, $peripheral_ins__id ) {
    $sql = 'SELECT name, description, number'
        . ' FROM p_interrupt INNER JOIN pl_interrupt ON pl_interrupt.irq_id     = p_interrupt.id'
        . ' WHERE pl_interrupt.per_in_id = ?'
        . ' ORDER BY number';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($peripheral_ins__id));
    foreach ($stmt as $row) {
        echo($xml_prefix . "<interrupt>\n");
        $elements_indent = $xml_prefix . $xml_indent_step;
        // name
        echo($elements_indent . "<name>" . $row['name'] . "</name>\n");
        // name
        echo($elements_indent . "<description>" . $row['description'] . "</description>\n");
        // name
        echo($elements_indent . "<value>" . $row['number'] . "</value>\n");
        echo($xml_prefix . "</interrupt>\n");
    }
}

function print_enumeration_value_element($xml_prefix, $xml_indent_step, $dbh, $enum_val_id ) {
    $sql = 'SELECT id, name, description, value'
        . ' FROM p_enumeration_element  INNER JOIN  pl_enumeration_element  ON  pl_enumeration_element.value_id  = p_enumeration_element.id'
        . ' WHERE pl_enumeration_element.enum_id = ?'
        . ' ORDER BY value';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($enum_val_id));
    foreach ($stmt as $row) {
        echo($xml_prefix . "<enumeratedValue>\n");
        $elements_indent = $xml_prefix . $xml_indent_step;
        if(NULL != $row['name']) {
            echo($elements_indent . "<name>" . $row['name'] . "</name>\n");
        }
        if(NULL != $row['description']) {
            echo($elements_indent . "<description>" . $row['description'] . "</description>\n");
        }
        if(NULL != $row['value']) {
            echo($elements_indent . "<value>" . $row['value'] . "</value>\n");
        }
        echo($xml_prefix . "</enumeratedValue>\n");
    }
}

function print_enumeration_element($xml_prefix, $xml_indent_step, $dbh, $enum_id ) {
    $sql = 'SELECT id, name, usage_right'
        . ' FROM p_enumeration  INNER JOIN  pl_enumeration  ON  pl_enumeration.enum_id  = p_enumeration.id'
        . ' WHERE pl_enumeration.field_id = ?'
        . ' ORDER BY name';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($enum_id));
    foreach ($stmt as $row) {
        echo($xml_prefix . "<enumeratedValues>\n");
        $elements_indent = $xml_prefix . $xml_indent_step;
        if(NULL != $row['name']) {
            echo($elements_indent . "<name>" . $row['name'] . "</name>\n");
        }
        if(NULL != $row['usage_right']) {
            echo($elements_indent . "<usage>" . $row['usage_right'] . "</usage>\n");
        }
        print_enumeration_value_element($elements_indent, $xml_indent_step, $dbh, $row['id']);
        echo($xml_prefix . "</enumeratedValues>\n");
    }
}

function print_fields_element($xml_prefix, $xml_indent_step, $dbh, $reg_id ) {
    echo($xml_prefix . "<fields>\n");
    $field_indent = $xml_prefix . $xml_indent_step;
    $sql = 'SELECT id, name, description, bit_offset, size_bit, access, modified_write_values, read_action, reset_value'
        . ' FROM p_field  INNER JOIN  pl_field  ON  pl_field.field_id  = p_field.id'
        . ' WHERE pl_field.reg_id = ?'
        . ' ORDER BY name';
    $stmt = $dbh->prepare($sql);

    $stmt->execute(array($reg_id));
    foreach ($stmt as $row) {
        echo($field_indent . "<field>\n");
        $elements_indent = $field_indent . $xml_indent_step;
        if(NULL != $row['name']) {
            echo($elements_indent . "<name>" . $row['name'] . "</name>\n");
        }
        if(NULL != $row['description']) {
            echo($elements_indent . "<description>" . $row['description'] . "</description>\n");
        }
        if(NULL != $row['bit_offset']) {
            echo($elements_indent . "<bitOffset>" . $row['bit_offset'] . "</bitOffset>\n");
        }
        else
        {
            echo($elements_indent . "<bitOffset>0</bitOffset>\n");
        }
        if(NULL != $row['size_bit']) {
            echo($elements_indent . "<bitWidth>" . $row['size_bit'] . "</bitWidth>\n");
        }
        if(NULL != $row['access']) {
            echo($elements_indent . "<access>" . $row['access'] . "</access>\n");
        }
        if(NULL != $row['modified_write_values']) {
            echo($elements_indent . "<modifiedWriteValues>" . $row['modified_write_values'] . "</modifiedWriteValues>\n");
        }
        if(NULL != $row['read_action']) {
            echo($elements_indent . "<readAction>" . $row['read_action'] . "</readAction>\n");
        }
        print_enumeration_element($elements_indent, $xml_indent_step, $dbh, $row['id'] );
        echo($field_indent . "</field>\n");
    }

    echo($xml_prefix . "</fields>\n");
}

function print_registers_element($xml_prefix, $xml_indent_step, $dbh, $peripheral_id ) {
    echo($xml_prefix . "<registers>\n");
    $sql = 'SELECT id, name, display_name, description, address_offset, size, access, reset_value, alternate_register,'
                . 'reset_Mask, read_action, modified_write_values, data_type'
        . ' FROM p_register  INNER JOIN  pl_register  ON  pl_register.reg_id  = p_register.id'
        . ' WHERE pl_register.per_id = ?'
        . ' ORDER BY name';
    $stmt = $dbh->prepare($sql);

    $stmt->execute(array($peripheral_id));
    foreach ($stmt as $row) {
        echo($xml_prefix . "<register>\n");
        $elements_indent = $xml_prefix . $xml_indent_step;
        if(NULL != $row['name']) {
            echo($elements_indent . "<name>" . $row['name'] . "</name>\n");
        }
        if(NULL != $row['display_name']) {
            echo($elements_indent . "<displayName>" . $row['display_name'] . "</displayName>\n");
        }
        if(NULL != $row['description']) {
            echo($elements_indent . "<description>" . $row['description'] . "</description>\n");
        }
        if(NULL != $row['alternate_register']) {
            echo($elements_indent . "<alternateRegister>" . $row['alternate_register'] . "</alternateRegister>\n");
        }
        if(NULL != $row['address_offset']) {
            echo($elements_indent . "<addressOffset>" . $row['address_offset'] . "</addressOffset>\n");
        }
        else
        {
            echo($elements_indent . "<addressOffset>0x0</addressOffset>\n");
        }
        if(NULL != $row['size']) {
            echo($elements_indent . "<size>" . $row['size'] . "</size>\n");
        }
        if(NULL != $row['access']) {
            echo($elements_indent . "<access>" . $row['access'] . "</access>\n");
        }
        echo($elements_indent . "<protection>n</protection>\n");
        if(NULL != $row['reset_value']) {
            echo($elements_indent . "<resetValue>0x" . dechex(intval($row['reset_value'])) . "</resetValue>\n");
        }
        if(NULL != $row['reset_Mask']) {
            echo($elements_indent . "<resetMask>0x" . dechex(intval($row['reset_Mask'])) . "</resetMask>\n");
        }
        if(NULL != $row['data_type']) {
            echo($elements_indent . "<dataType>" . $row['data_type'] . "</dataType>\n");
        }
        if(NULL != $row['modified_write_values']) {
            echo($elements_indent . "<modifiedWriteValues>" . $row['modified_write_values'] . "</modifiedWriteValues>\n");
        }
        if(NULL != $row['read_action']) {
            echo($elements_indent . "<readAction>" . $row['read_action'] . "</readAction>\n");
        }
        print_fields_element($elements_indent, $xml_indent_step, $dbh, $row['id']);
        echo($xml_prefix . "</register>\n");
    }

    echo($xml_prefix . "</registers>\n");
}

function print_peripherals_element($xml_prefix, $xml_indent_step, $dbh, $device_id ) {
    // Peripheral Instances
    $sql = 'SELECT name, description, base_address, peripheral_id, per_in_id, disable_Condition, group_name'
        . ' FROM p_peripheral_instance INNER JOIN  pl_peripheral_instance ON  pl_peripheral_instance.per_in_id  = p_peripheral_instance.id'
        . ' WHERE pl_peripheral_instance.dev_id = ?'
        . ' ORDER BY name';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($device_id));

    $periph_indent = $xml_prefix . $xml_indent_step;
    echo($xml_prefix . "<peripherals>\n");
    foreach ($stmt as $row) {
        echo($periph_indent . "<peripheral>\n");
        $elements_indent = $periph_indent . $xml_indent_step;
        // name
        echo($elements_indent . "<name>" . $row['name'] . "</name>\n");
        // description
        echo($elements_indent . "<description>" . $row['description'] . "</description>\n");
        // groupName
        echo($elements_indent . "<groupName>" . $row['group_name'] . "</groupName>\n");
        // disableCondition
        if(NULL != $row['disable_Condition']) {
            echo($elements_indent . "<disableCondition>" . $row['disable_Condition'] . "</disableCondition>\n");
        }
        // baseAddress
        echo($elements_indent . "<baseAddress>0x" . dechex(intval($row['base_address'])) . "</baseAddress>\n");

        print_addressBlock_element($elements_indent, $xml_indent_step, $dbh, $row['peripheral_id']);
        print_interrupt_element($elements_indent, $xml_indent_step, $dbh, $row['per_in_id']);
        print_registers_element($elements_indent, $xml_indent_step, $dbh, $row['peripheral_id']);
        echo($periph_indent . "</peripheral>\n");
    }
    echo($xml_prefix. "</peripherals>\n");
}


// main():
// =======

$vendor_name = $_GET['vend'];
$device_name = $_GET['dev'];

// echo("Vendor : " . $vendor_name);
// echo("   Device : " . $device_name);

include ("../secret.inc");
// connect to database
$dbh = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$stmt = $dbh->prepare('SELECT id, name, url, alternative FROM p_vendor WHERE name = ?');
if(false == $stmt->execute(array($vendor_name))) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    echo("ERROR: Can not talk to database !\n");
    exit;
}
$row = $stmt->fetch();
if(false == $row) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    echo("ERROR: Invalid vendor name of " . $vendor_name . "\n");
    exit;
}

if(0 != $row['alternative']) {
    $stmt = $dbh->prepare('SELECT id, name, url, alternative FROM p_vendor WHERE id = ?');
    $stmt->execute(array($row['alternative']));
    $row = $stmt->fetch();
}
$vendor = $row;

// now the device data
$stmt = $dbh->prepare('SELECT * FROM microcontroller WHERE name = ?');
$stmt->execute(array($device_name));
$row = $stmt->fetch();
if(false == $row) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    echo("ERROR: Invalid device name of " . $device_name . "\n");
    exit;
}
$device = $row;

// now check if this device and this Vendor match
if($row['vendor_id'] != $vendor['id']) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    echo("ERROR: Invalid device name of " . $device['name'] . " for the vendor " . $vendor['name'] . "\n");
    exit;
}

if(null == $device['description'])
{
    $device['description'] = $device['name'];
}
else if(0 ==  strlen($device['description']))
{
    $device['description'] = $device['name'];
}

// checks done, start outputting the SVD data
echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
echo("<!-- downloaded from chipselect.org on " . date("d.F Y") . " -->\n");
echo("<device schemaVersion=\"1.3\" xmlns:xs=\"http://www.w3.org/2001/XMLSchema-instance\" xs:noNamespaceSchemaLocation=\"CMSIS-SVD.xsd\">\n");

$xml_indent_step = "  ";
$xml_prefix = $xml_indent_step;

echo($xml_prefix . "<vendor>" . $vendor['name'] . "</vendor>\n");
echo($xml_prefix . "<name>" . $device['name'] . "</name>\n");
// Version is the date of creation.
// SVD Spec: Silicon vendors [..] ensure that all updated and released copies have
// a unique version string. Higher numbers indicate a more recent version.
echo($xml_prefix. "<version>" . date('Y.m.d') . "</version>\n");
echo($xml_prefix . "<description>" . $device['description'] . "</description>\n");
print_cpu_element($xml_prefix, $xml_indent_step, $dbh, $device['architecture_id']);
echo($xml_prefix . "<addressUnitBits>" . $device['Addressable_unit_bit'] . "</addressUnitBits>\n");
echo($xml_prefix. "<width>" . $device['bus_width_bit'] . "</width>\n");
print_peripherals_element($xml_prefix, $xml_indent_step, $dbh, $device['id']);
echo("</device>\n");
?>
