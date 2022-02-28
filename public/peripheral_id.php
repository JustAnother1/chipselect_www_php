<?php
include("start.inc");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
<?php
include ("../secret.inc");
include ("svg.inc");
// connect to database
$dbh = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$peripheral_id = $_GET['id'];
// check if it is an int
if(!is_numeric($peripheral_id)) {
    echo("    </head>\n");
    echo("    <body>\n");
    echo("     <h1> Invalid peripheral id of " . $peripheral_id . "</h1>\n");
    echo("    </body>\n");
    echo("</html>\n");
    exit;
}

$sql = 'SELECT group_name'
    . ' FROM p_peripheral_instance '
    . ' WHERE id = ?';
$stmt = $dbh->prepare($sql);
if(false == $stmt->execute(array($peripheral_id))) {
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
    echo("     <h1> Invalid peripheral id of " . $peripheral_id . "</h1>\n");
    echo("    </body>\n");
    echo("</html>\n");
    exit;
}
$peripheral_data = $row;

echo('<title>' . $peripheral_data['group_name'] . '</title>\n');
        ?>
        <meta  http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>

<?php

function print_enum_elements($dbh, $enum_id) {
    $sql = 'SELECT id, name, description, value'
    . ' FROM p_enumeration_element INNER JOIN  pl_enumeration_element ON  pl_enumeration_element.value_id  = p_enumeration_element.id'
    . ' WHERE pl_enumeration_element.enum_id = ?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($enum_id));
    echo("<div id=\"enum_value\">\n");
    foreach ($stmt as $row) {
        echo("    <p> " . $row['value'] . " : " . $row['name'] . " <br />\n");
        echo("    <p> " . $row['description'] . " <br />\n");
        echo("</p>\n");
    }
    echo("</div>\n");
}

function print_enums($dbh, $field_id) {
    $sql = 'SELECT id, name, usage_right'
    . ' FROM p_enumeration INNER JOIN  pl_enumeration ON  pl_enumeration.enum_id  = p_enumeration.id'
    . ' WHERE pl_enumeration.field_id = ?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($field_id));
    echo("<div id=\"enum\">\n");
    foreach ($stmt as $row) {
        echo("    <p> Enumeration: ");
        if(NULL != $row['name']) {
            if('None' != $row['name']) {
                echo($row['name']);
            }
        }
        if(NULL != $row['usage_right']) {
            if('None' != $row['usage_right']) {
                echo(" ( " . $row['usage_right'] . " )");
            }
        }
        echo("<br />\n</p>\n");
        print_enum_elements($dbh, $row['id']);
        echo("<p>End of enumeration elements list.</p>\n");
    }
    echo("</div>\n");
}

function print_one_field($dbh, $row) {
    echo("    <p>" . $row['name'] . " : " . $row['description'] . "<br />\n");
    echo("    bits : " . $row['bit_offset'] . " - " . ($row['bit_offset'] + $row['size_bit'] -1) . "<br />\n");
    if(NULL != $row['access']) {
        echo("    access : " . $row['access'] . "<br />\n");
    }
    echo("</p>\n");
    print_enums($dbh, $row['id']);
}

function print_fields($dbh, $reg_name, $reg_id, $reg_size, $reg_access, $reg_reset_value) {
    $sql = 'SELECT id, name, description, bit_offset, size_bit, access'
    . ' FROM p_field INNER JOIN  pl_field ON  pl_field.field_id  = p_field.id'
    . ' WHERE pl_field.reg_id = ?'
    . ' ORDER BY bit_offset';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($reg_id));
    $all = array();
    $i = 0;
    foreach ($stmt as $row) {
        $row_arr = array();
        $row_arr['id'] = $row['id'];
        $row_arr['name'] = $row['name'];
        $row_arr['description'] = $row['description'];
        $row_arr['bit_offset'] = $row['bit_offset'];
        $row_arr['size_bit'] = $row['size_bit'];
        $row_arr['access'] = $row['access'];
        $all[$i] = $row_arr;
        $i = $i + 1;
    }

    // diagram for Register
    register_image($all, $reg_name, $reg_size, $reg_access, $reg_reset_value);

    // description of the fields
    echo("<div id=\"field\">\n");
    foreach ($all as $row) {
        print_one_field($dbh, $row);
    }
    echo("</div>\n");
}



include ("header.inc");
echo("<h1>" . $peripheral_data['group_name'] . "</h1>\n");

// Address Block
$sql = 'SELECT address_offset, size, mem_usage, protection'
    . ' FROM p_address_block INNER JOIN  pl_address_block ON  pl_address_block.addr_id  = p_address_block.id'
    . ' WHERE pl_address_block.per_id = ?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($peripheral_id));
echo("<div id=\"address_block\">\n");
echo("<h2>Peripheral Memory Blocks</h2>\n");
foreach ($stmt as $row) {
    echo("    <p>address_offset : " . $row['address_offset'] . " Bytes (0x" . dechex(intval($row['address_offset'])) . ")<br />\n");
    echo("    size : " . $row['size'] . " byte (0x" . dechex(intval($row['size'])) . ")<br />\n");
    echo("    mem_usage : " . $row['mem_usage'] . "<br />\n");
    if($row['protection'] == "n") {
        echo("    protection : not protected<br />\n");
    } else {
        echo("    protection : " . $row['protection'] . "<br />\n");
    }
    echo("</p>\n");
}
echo("</div>\n");


// Registers
$sql = 'SELECT name, display_name, description, address_offset, size, access, reset_value, alternate_register, reset_Mask, id'
    . ' FROM p_register INNER JOIN  pl_register ON  pl_register.reg_id  = p_register.id'
    . ' WHERE pl_register.per_id = ?'
    . ' ORDER BY address_offset';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($peripheral_id));
echo("<div id=\"registers\">\n");
echo("<h2>Registers</h2>\n");
echo("<div id=\"registers_idx\">\n");

foreach ($stmt as $row) {
    if($row['display_name'] == "")
    {
        echo("    <p><a href=\"#" . $row['name'] . "\">" . $row['name'] . "</a></p>\n");
    }
    else if($row['name'] != $row['display_name']) {
        echo("    <p><a href=\"#" . $row['name'] . "\">" . $row['display_name'] . " (" . $row['name'] . ")</a></p>\n");
    } else {
        echo("    <p><a href=\"#" . $row['name'] . "\">" . $row['name'] . "</a></p>\n");
    }
}
echo("<br />\n");
$stmt = $dbh->prepare($sql);
$stmt->execute(array($peripheral_id));
echo("</div>\n");
foreach ($stmt as $row) {
    if($row['display_name'] == "")
    {
        echo("    <h3 id=\"" . $row['name'] . "\">" . $row['name'] . "</h3>\n");
    }
    else if($row['name'] != $row['display_name']) {
        echo("    <h3 id=\"" . $row['name'] . "\">" . $row['display_name'] . " (" . $row['name'] . ")</h3>\n");
    } else {
        echo("    <h3 id=\"" . $row['name'] . "\">" . $row['name'] . "</h3>\n");
    }
    echo("<p>\n");
    echo("    " . $row['description'] . "<br />\n");
    echo("    address_offset : " . $row['address_offset'] . " Bytes (0x" . dechex(intval($row['address_offset'])) . ")<br />\n");
    if($row['size'] != "")
    {
        echo("    size : " . $row['size'] . " bit<br />\n");
    }
    echo("    access : " . $row['access'] . "<br />\n");
    echo("    reset_value : 0x" . dechex(intval($row['reset_value'])) . "<br />\n");
    if(NULL != $row['alternate_register']) {
        if("None" != $row['alternate_register']) {
            echo("    alternate_register : " . $row['alternate_register'] . "<br />\n");
        }
    }
    if(NULL != $row['reset_Mask']) {
        if((32 == $row['size']) && ('ffffffff' != dechex(intval($row['reset_Mask']))))
        {
            echo("    reset_Mask : 0x" . dechex(intval($row['reset_Mask'])) . "<br />\n");
        }
        // Reset Mask of 0xffffffff just means all the pins are reset on reset. Duh! The mask is only interesting if not all bits are affected.
    }
    echo("</p>\n");
    print_fields($dbh, $row['display_name'],  $row['id'], $row['size'], $row['access'], $row['reset_value']);
    echo("<br />\n");
}
echo("</div>\n");



// Footer
include ("footer.inc");
        ?>
    </body>
</html>
