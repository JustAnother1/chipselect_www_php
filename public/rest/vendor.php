<?php
header('Content-Type: application/json');

include ("../../secret.inc");
$pdo = new PDO('mysql:dbname=microcontrollis;host=' . $db_host, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
if(isset($_GET["name"]))
{
    $sql = "SELECT id, name, url FROM p_vendor WHERE name = \"" . $_GET["name"] . "\"";
}
else
{
    $sql = "SELECT id, name, url FROM p_vendor WHERE alternative = 0 ORDER BY id";
}
$statement = $pdo->prepare($sql);
$statement->execute();
$data = $statement->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data);
?>