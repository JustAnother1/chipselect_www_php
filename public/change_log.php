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
if((isset($_SESSION['login'])) && ($_SESSION["login"] == 1))
{
    include ("../secret.inc");
    $pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if(isset($_GET['id']))
    {
        // user clicked on Accept already -> store that in the db.
        $stmt = $pdo->prepare( "UPDATE p_log SET accepted_by = ? WHERE id = ?");
        $stmt->execute(array( $_SESSION['name'], $_GET['id']));
    }
    $sql = "SELECT id, action, on_table, on_id, on_column, old_value, new_value, user, time_of_change from p_log WHERE accepted_by IS NULL";

    echo("<table>\n");
    echo("  <tr>\n");
    echo("    <th>id</th>\n");
    echo("    <th>action</th>\n");
    echo("    <th>on table</th>\n");
    echo("    <th>on column</th>\n");
    echo("    <th>old value</th>\n");
    echo("    <th>new value</th>\n");
    echo("    <th>user</th>\n");
    echo("    <th>time of change</th>\n");
    echo("    <th>OK ?</th>\n");
    echo("  </tr>\n");
    foreach($pdo->query($sql) as $row) {
        echo("  <tr>\n");
        echo("    <td>" . $row['id'] . "</td>\n");
        echo("    <td>" . $row['action'] . "</td>\n");
        echo("    <td>" . $row['on_table'] . "</td>\n");
        echo("    <td>" . $row['on_id'] . "</td>\n");
        echo("    <td>" . $row['on_column'] . "</td>\n");
        echo("    <td>" . $row['old_value'] . "</td>\n");
        echo("    <td>" . $row['new_value'] . "</td>\n");
        echo("    <td>" . $row['user'] . "</td>\n");
        echo("    <td>" . $row['time_of_change'] . "</td>\n");
        echo("    <td>\n");
        echo("      <form method=\"get\">\n");
        echo("        <input type=\"submit\" name=\"Submit_name\" value=\"accept\">\n");
        echo("        <input type=\"hidden\" id=\"id\" name=\"id\" value=\"" . $row['id'] . "\">\n");
        echo("      </form>\n");
        echo("    </td>\n");
        echo("  </tr>\n");
    }
    echo("  </table>\n");
}
echo("<br/>");
include ("footer.inc");
        ?>
    </body>
</html>