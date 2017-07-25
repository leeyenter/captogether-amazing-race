<?php
$db_host        = 'localhost';
$db_user        = 'user';
$db_pass        = 'pw';
$db_database    = 'db';

$db = mysqli_connect($db_host,$db_user,$db_pass,$db_database) or die('Unable to establish a NHT_DB connection');
$db->set_charset("utf8");
if (!$db) {
    die('Connect Error: ' . mysqli_connect_error());
}
?>
