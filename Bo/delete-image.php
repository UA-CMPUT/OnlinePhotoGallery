<?php
//echo "delete<br>";
//echo $_GET["id"];
include("connDB.php");
session_start();
if ( !isset ( $_SESSION['USER_NAME'] ) ) {
    header( "location:index.php?ERR=session" );
    exit();
};
$id = $_GET["id"];



?>