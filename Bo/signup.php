<?php
/**
 * Created by PhpStorm.
 * Author: Bo Zhou
 */

include("connDB.php");
if( isset( $_POST['signup-button'])){
    $user_name = $_POST['signup-username'];
    $user_pswd = $_POST['signup-password'];
    $user_first_name = $_POST['signup-first-name'];
    $user_last_name = $_POST['signup-last-name'];
    $user_email = $_POST['form-email'];
    $user_phone = $_POST['form-phone'];
    $user_address = $_POST['form-address'];

    $conn = connect();

    /* check username duplication */
    $get_username_sql = "SELECT user_name FROM users";
    $get_username_stid = oci_parse($conn, $get_username_sql);
    $username_result = oci_execute($get_username_stid);
    if ( !$username_result ){
        header( "location:index.php?ERR=err" );
        oci_free_statement($get_username_stid);
        oci_close($conn);
        exit();
    }
    while ($one_username = oci_fetch_array($get_username_stid, OCI_ASSOC)){
        if ($one_username['USER_NAME'] == $user_name){
            header("location: index.php?ERR=dup-name");
            oci_free_statement($get_username_stid);
            oci_close($conn);
            exit();
        }
    }
    oci_free_statement($get_username_stid);


    /* check email duplication */
    $get_email_sql = "SELECT email FROM persons";
    $get_email_stid = oci_parse($conn, $get_email_sql);
    $email_result = oci_execute($get_email_stid);
    if ( !$email_result ){
        header( "location:index.php?ERR=err" );
        oci_free_statement($get_email_stid);
        oci_close($conn);
        exit();
    }
    while ($one_email = oci_fetch_array($get_email_stid, OCI_ASSOC)){
        if ($one_email['EMAIL'] == $user_email){
            header("location: index.php?ERR=dup-email");
            oci_free_statement($get_email_stid);
            oci_close($conn);
            exit();
        }
    }
    oci_free_statement($get_email_stid);

    /* start insert infomation into table users and persons */
    /* get date and time */
    $now_date = date("d/m/Y H:i:s");
    /* change date type in Oracle */
    $date_format_sql = "alter session set nls_date_format = 'dd/mm/yyyy hh24:mi:ss'";
    $date_format_stid = oci_parse($conn, $date_format_sql);
    $date_format_result = oci_execute($date_format_stid);
    oci_free_statement($date_format_stid);

    /* insert into table: users */
    $insert_users_sql = "INSERT INTO users VALUES ('".$user_name."', '".$user_pswd."', '".$now_date."')";
    $insert_users_stid = oci_parse($conn, $insert_users_sql);
    $insert_users_result = oci_execute($insert_users_stid);

    /* insert into table: persons */
    $insert_persons_sql = "INSERT INTO persons VALUES ('".$user_name."', '".$user_first_name."', '".$user_last_name."', '".$user_address."', '".$user_email."', '".$user_phone."')";
    $insert_persons_stid = oci_parse($conn, $insert_persons_sql);
    $insert_persons_result = oci_execute($insert_persons_stid);

    /* insert into public group list */
    $insert_public_sql = "INSERT INTO group_lists VALUES (1, '".$user_name."', '".$now_date."', 'system added' )";
    $insert_public_stid = oci_parse($conn, $insert_public_sql);
    $insert_public_result = oci_execute($insert_public_stid);

    /* all insertion success, header to main_page */
    if ( $insert_users_result && $insert_persons_result && $insert_public_result){
        oci_commit($conn);
        oci_free_statement($insert_persons_stid);
        oci_free_statement($insert_users_stid);
        oci_free_statement($insert_public_stid);
        oci_close($conn);
        session_start();
        $_SESSION['REG_DATE'] = $now_date;
        $_SESSION['USER_NAME'] = $user_name;
        $_SESSION['FIRST_NAME'] = $user_first_name;
        $_SESSION['LAST_NAME'] = $user_last_name;
        $_SESSION['ADDRESS'] = $user_address;
        $_SESSION['EMAIL'] = $user_email;
        $_SESSION['PHONE'] = $user_phone;
        header( "location:main_page.php" );
    } else {
        /* any one fail, rollback all */
        oci_rollback($conn);
        oci_free_statement($insert_persons_stid);
        oci_free_statement($insert_users_stid);
        oci_free_statement($insert_public_stid);
        oci_close($conn);
//        echo 4;
        header("location: index.php?ERR=err");
    }
}
?>
