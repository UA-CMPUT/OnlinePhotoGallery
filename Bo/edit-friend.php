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
$all_friend_names = $_POST['friend-list'];
$group_id = $_GET["ID"];

if (isset($_POST['delete-friend-button'])) {
    foreach ($all_friend_names as $name) {
        $sql = "DELETE FROM group_lists WHERE group_id='" . $group_id . "' AND friend_id='" . $name . "'";
        $stid = oci_parse($conn, $sql);
        $result = oci_execute($stid);
        if (!$result) {
            oci_free_statement($stid);
            oci_rollback($conn);
            oci_close($conn);
            header("location:groups.php?ACK=-4");
            exit();
        } else {
            oci_free_statement($stid);
        }
    }
    header( "location:groups.php?ACK=3" );
}elseif (isset($_POST['edit-friend-button'])){
//    echo "this is edit part";
    $notice = $_POST['notice2'];
    foreach ($all_friend_names as $name) {
        $sql = "UPDATE group_lists SET notice='".$notice."' WHERE group_id='" . $group_id . "' AND friend_id='" . $name . "'";
        $stid = oci_parse($conn, $sql);
        $result = oci_execute($stid);
        if (!$result) {
            oci_free_statement($stid);
            oci_rollback($conn);
            oci_close($conn);
            header("location:groups.php?ACK=-6");
            exit();
        } else {
            oci_free_statement($stid);
        }
    }
    header( "location:groups.php?ACK=5" );
}
oci_commit($conn);
oci_close($conn);
?>