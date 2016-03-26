<?php
/**
 * Created by PhpStorm.
 * Author: Bo Zhou
 */

//echo "This is add-friend";
include("connDB.php");
session_start();
if ( !isset ( $_SESSION['USER_NAME'] ) ) {
    header( "location:index.php?ERR=session" );
    exit();
};
$conn = connect();
$all_friend_names = $_POST['friend-list'];
$group_id = $_GET["ID"];
foreach ($all_friend_names as $name){
    $sql = "DELETE FROM group_lists WHERE group_id='".$group_id."' AND friend_id='".$name."'";
    $stid = oci_parse($conn, $sql);
    $result = oci_execute($stid);
    if (!$result){
        oci_free_statement($stid);
        oci_rollback($conn);
        oci_close($conn);
//            echo "www ".$name."<br>";
        header( "location:groups.php?ACK=-4" );
        exit();
    }else{
        oci_free_statement($stid);
    }
}
oci_commit($conn);
oci_close($conn);
header( "location:groups.php?ACK=3" );
?>