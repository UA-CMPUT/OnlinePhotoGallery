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
    $sql_id = "SELECT group_id FROM groups";

    $stid = oci_parse( $conn, $sql );
    $stid2 = oci_parse( $conn, $sql2);
    $stid_id = oci_parse($conn, $sql_id);
    $result = oci_execute( $stid );
    $result2 = oci_execute( $stid2 );
    $result_id = oci_execute( $stid_id);
    if (!($result&&$result2&&$result_id)){
        oci_free_statement($stid2);
        oci_free_statement($stid);
        oci_free_statement($stid_id);
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
    $all_id = array();
    while($group_id = oci_fetch_array($stid_id, OCI_ASSOC)){
        array_push($all_id, $group_id["GROUP_ID"]);
    }
    oci_free_statement($stid_id);
    while(true){
        if (in_array($id_guess, $all_id)){
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