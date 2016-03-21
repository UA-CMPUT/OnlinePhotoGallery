<?php
    include("connDB.php");
    session_start();
    if ( !isset ( $_SESSION['USER_NAME'] ) ) {
        header( "location:index.php?ERR=session" );
        exit();
    };
    $conn = connect();
    /* change date format */
    $sql_date_format = "alter session set nls_date_format = 'dd/mm/yyyy hh24:mi:ss'";
    $stid_date_format = oci_parse( $conn, $sql_date_format );
    $result_date_format = oci_execute( $stid_date_format );
    oci_free_statement($stid_date_format);
    /* check group name exist */
    $new_group = $_POST["group-input"];
    $sql = "SELECT group_id, group_name FROM groups WHERE user_name='".$user_name."'";
    $sql2 = "SELECT group_id, group_name FROM groups WHERE user_name IS NULL";

    $stid = oci_parse( $conn, $sql );
    $stid2 = oci_parse( $conn, $sql2);
    $result = oci_execute( $stid );
    $result2 = oci_execute( $stid2 );
    if (!($result&&$result2)){
        oci_free_statement($stid2);
        oci_free_statement($stid);
        header( "location:groups.php?ACK=-1" );
        exit();
    }
    $all_group_info = array();
    while ($group = oci_fetch_row($stid2)){
        if ($new_group == $group[1]){
            header( "location:groups.php?ACK=-2" );
            exit();
        }else {
            array_push($all_group_info, $group);
        }
    }
    while ($group = oci_fetch_row($stid)){
        if ($new_group == $group[1]){
            header( "location:groups.php?ACK=-2" );
            exit();
        }else {
            array_push($all_group_info, $group);
        }
    }
    oci_free_statement($stid);
    oci_free_statement($stid2);
    /* find valid group id */
    $id_guess = 1;
    foreach ($all_group_info as $group){
        if ($id_guess == $group[0]){
            $id_guess++;
        }else{
            break;
        }
    }
    $user_name = $_SESSION["USER_NAME"];
    echo $user_name;
    $now_date = date("d/m/Y H:i:s");
    $add_sql = "INSERT INTO groups (group_id, user_name, group_name, date_created) VALUES('".$id_guess."', '".$user_name."', '".$new_group."', '".$now_date."' )";
    $add_stid = oci_parse($conn, $add_sql);
    $add_result = oci_execute($add_stid);
    if (!$add_result){
        oci_rollback($conn);
        oci_free_statement($add_stid);
        oci_close($conn);
        header( "location:groups.php?ACK=-1" );
    }else{
        oci_commit($conn);
        oci_free_statement($add_stid);
        oci_close($conn);
        header( "location:groups.php?ACK=1" );
    }
?>