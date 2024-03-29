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
    . ' FROM p_peripheral '
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

echo('    <title>' . $peripheral_data['group_name'] . '</title>\n');
        ?>
    <meta  http-equiv="content-type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php

function print_enum_elements($dbh, $field_id) {
    $sql = 'SELECT id, name, description, value'
    . ' FROM p_enumeration_element INNER JOIN  pl_field_enum_element ON  pl_field_enum_element.value_id  = p_enumeration_element.id'
    . ' WHERE pl_field_enum_element.field_id = ?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($field_id));
    echo("<div id=\"enum_value\">\n");
    foreach ($stmt as $row) {
        echo("    <p> " . $row['value'] . " : " . $row['name'] . " <br />\n");
        echo("    <p> " . $row['description'] . " <br />\n");
        echo("</p>\n");
    }
    echo("</div>\n");
}

function print_one_field($dbh, $row) {
    echo("    <p>" . $row['name'] . " : " . $row['description'] . "<br />\n");
    echo("    bits : " . $row['bit_offset'] . " - " . ($row['bit_offset'] + $row['size_bit'] -1) . " (" . $row['size_bit'] . " bit)" . "<br />\n");
    if(NULL != $row['access']) {
        echo("    access : " . $row['access'] . "<br />\n");
    }
    echo("</p>\n");
    if(1 == $row['is_Enum'])
    {
        echo("    <p> Enumeration: ");
        if(NULL != $row['enum_name']) {
            if('NULL' != $row['enum_name']) {
                if('None' != $row['enum_name']) {
                    echo($row['enum_name']);
                }
            }
        }
        if(NULL != $row['enum_usage_right']) {
            if('NULL' != $row['enum_usage_right']) {
                if('None' != $row['enum_usage_right']) {
                    echo(" ( " . $row['enum_usage_right'] . " )");
                }
            }
        }
        echo("<br />\n</p>\n");
        print_enum_elements($dbh, $row['id']);
        echo("<p>End of enumeration elements list.</p>\n");
    }
}

function print_fields($dbh, $reg_name, $reg_id, $reg_size, $reg_access, $reg_reset_value) {
    $sql = 'SELECT id, name, description, bit_offset, size_bit, access, modified_write_values, read_action, is_Enum, enum_name, enum_usage_right'
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
        $row_arr['modified_write_values'] = $row['modified_write_values'];
        $row_arr['read_action'] = $row['read_action'];
        $row_arr['is_Enum'] = $row['is_Enum'];
        $row_arr['enum_name'] = $row['enum_name'];
        $row_arr['enum_usage_right'] = $row['enum_usage_right'];
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
    echo("    </p>\n");
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
    if($row['display_name'] == "") {
        echo("    <p><a href=\"#" . $row['name'] . "\">" . $row['name'] . "</a></p>\n");
    } else if($row['name'] != $row['display_name']) {
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
    // name , display_name
    if($row['display_name'] == "") {
        echo("    <h3 id=\"" . $row['name'] . "\">" . $row['name'] . "</h3>\n");
        $row['display_name'] = $row['name'];
    } else if($row['name'] != $row['display_name']) {
        echo("    <h3 id=\"" . $row['name'] . "\">" . $row['display_name'] . " (" . $row['name'] . ")</h3>\n");
    } else {
        echo("    <h3 id=\"" . $row['name'] . "\">" . $row['name'] . "</h3>\n");
    }
    echo("    <p>\n");
    // description
    echo("    " . $row['description'] . "<br />\n");
    // address_offset
    echo("    address_offset : " . $row['address_offset'] . " Bytes (0x" . dechex(intval($row['address_offset'])) . ")<br />\n");
    // size
    if($row['size'] != "") {
        echo("    size : " . $row['size'] . " bit<br />\n");
    } else {
        echo("<!-- size is not set -->\n");
    }
    // access
    if(NULL != $row['access']) {
        echo("    access : " . $row['access'] . "<br />\n");
    }
    // reset_value
    echo("    reset_value : 0x" . dechex(intval($row['reset_value'])) . "<br />\n");
    // alternate_register
    if(NULL != $row['alternate_register']) {
        if("None" != $row['alternate_register']) {
            echo("    alternate_register : " . $row['alternate_register'] . "<br />\n");
        }
    }
    // reset_mask
    if(NULL != $row['reset_Mask']) {
        if((32 == $row['size']) && ('ffffffff' == dechex(intval($row['reset_Mask'])))) {
            // Reset Mask of 0xffffffff just means all the pins are reset on reset. Duh! The mask is only interesting if not all bits are affected.
        } else {
            echo("    reset_Mask : 0x" . dechex(intval($row['reset_Mask'])) . "<br />\n");
        }
    }
    echo("    </p>\n");
    print_fields($dbh, $row['display_name'],  $row['id'], $row['size'], $row['access'], $row['reset_value']);
    echo("<br />\n");
}
echo("</div>\n");



// Footer
include ("footer.inc");
        ?>
    </body>
</html>
