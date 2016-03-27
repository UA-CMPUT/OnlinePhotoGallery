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
$sql = "DELETE FROM images WHERE photo_id='".$id."'";
$sql2 = "DELETE FROM images_viewed WHERE photo_id='".$id."'";
$stid = oci_parse($conn, $sql);
$stid2 = oci_parse($conn, $sql2);
$result = oci_execute($stid);
$result2 = oci_execute($stid2);
if($result && $result2){
    oci_commit($conn);
    header( "location:own_images.php?ACK=1" );
}else{
    oci_rollback($conn);
    header( "location:own_images.php?ACK=-1" );
}
oci_free_statement($stid);
oci_free_statement($stid2);
oci_close($conn);
?>