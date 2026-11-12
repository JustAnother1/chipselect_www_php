<?php
session_start();

$session_timeout = 1800; // seconds
if((isset($_SESSION['last_visit'])) && ((time() - $_SESSION['last_visit']) > $session_timeout))
{
    session_destroy();
}
$_SESSION['last_visit'] = time();

?>