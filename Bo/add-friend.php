<?php
//echo "This is add-friend";
    include("connDB.php");
    session_start();
    if ( !isset ( $_SESSION['USER_NAME'] ) ) {
        header( "location:index.php?ERR=session" );
        exit();
    };
    $conn = connect();
    $all_add_names = $_POST['unfriend-list'];
//    echo "id is".$_GET["ID"]."<br>";
    $date_format_sql = "alter session set nls_date_format = 'dd/mm/yyyy hh24:mi:ss'";
    $date_format_stid = oci_parse($conn, $date_format_sql);
    $date_format_result = oci_execute($date_format_stid);
    oci_free_statement($date_format_stid);

    $group_id = $_GET["ID"];
    $notice = $_POST["notice"];
    foreach ($all_add_names as $name){
//        echo $group_id."<br>";
        $now_date = date("d/m/Y H:i:s");
        $sql = "INSERT INTO group_lists (group_id, friend_id, date_added, notice ) VALUES ('".$group_id."', '".$name."', '".$now_date."', '".$notice."' )";
        $stid = oci_parse($conn, $sql);
        $result = oci_execute($stid);
        if (!$result){
            oci_free_statement($stid);
            oci_rollback($conn);
            oci_close($conn);
//            echo "www ".$name."<br>";
            header( "location:groups.php?ACK=-3" );
            exit();
        }else{
            oci_free_statement($stid);
        }
    }
    oci_commit($conn);
    oci_close($conn);
    header( "location:groups.php?ACK=2" );
?>