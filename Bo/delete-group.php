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
$q=$_GET["ID"];
$conn = connect();
/* update image permitted; */
$sql_update_images = "UPDATE images SET permitted=2 WHERE permitted='".$q."'";
$stid_update_images = oci_parse($conn, $sql_update_images);
$result_update_images = oci_execute($stid_update_images);
/* delete all friends before delete group */
$sql_delete_friend = "DELETE FROM group_lists WHERE group_id = '".$q."'";
$stid_delete_friend = oci_parse($conn,$sql_delete_friend);
$result_delete_friend = oci_execute($stid_delete_friend);
/* delete the group */
$sql_delete_group = "DELETE FROM groups WHERE group_id = '".$q."'";
$stid_delete_group = oci_parse($conn,$sql_delete_group);
$result_delete_group = oci_execute($stid_delete_group);
if ($result_delete_friend && $result_delete_group && $result_update_images){
    oci_commit($conn);
    header( "location:groups.php?ACK=4" );
}else{
    oci_rollback($conn);
    header( "location:groups.php?ACK=-5" );
}
oci_free_statement($stid_delete_group);
oci_free_statement($stid_delete_friend);
oci_free_statement($stid_update_images);
oci_close($conn);
?>