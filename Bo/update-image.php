<?php
/*
* CMPUT 391 Project Online Photo Gallery
* Written by Bo Zhou
* Mar 26, 2016
*
*/
include("connDB.php");
session_start();
if ( !isset ( $_SESSION['USER_NAME'] ) ) {
    header( "location:index.php?ERR=session" );
    exit();
};
$conn = connect();
$id = $_GET["id"];
$user_name = $_SESSION["USER_NAME"];

$sql_date_format = "alter session set nls_date_format = 'dd/mm/yyyy hh24:mi:ss'";
$stid_date_format = oci_parse( $conn, $sql_date_format );
$result_date_format = oci_execute( $stid_date_format );
oci_free_statement($stid_date_format);

$permitted_id = $_POST['group-name'];
$subject = $_POST['title'];
$description = $_POST['description'];
$now_date = $_POST['date-input'];
$place = $_POST['place'];
$sql = "UPDATE images SET permitted='" . $permitted_id . "', subject='" . $subject ."', place='". $place ."', timing='". $now_date."', description='". $description."' WHERE photo_id='".$id."'";
$stid = oci_parse($conn, $sql);
$result = oci_execute($stid);
if ($result){
    oci_commit($conn);
    header( "location:own_images.php?ACK=2" );
}else{
    oci_rollback($conn);
    header( "location:own_images.php?ACK=-2" );
}
oci_free_statement($stid);
oci_close($conn);
?>