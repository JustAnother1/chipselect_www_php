<div id="header">
    <h1>ChipSelect</h1>
</div>
<div id="menue">
    <ul>
     <li><a href="index.php">Chips by feature</a></li>
     <li><a href="vendor.php">Chips by Vendor</a></li>
     <li><a href="about.php">about</a></li>
<?php
if((isset($_SESSION['login'])) && ($_SESSION["login"] == 1))
{
    echo("     <li><a href=\"logout.php\">logout</a></li>\n");
    if(in_array("admin", $_SESSION['user_roles']))
    {
        echo("     <li><a href=\"change_log.php\">check changes</a></li>\n");
    }
    echo("     <li><a href=\"user.php\">loged in as " . $_SESSION['full_name'] . "</a></li>\n");
}
else
{
    echo("     <li><a href=\"login.php\">login</a></li>\n");
}

?>
    </ul>
</div>
