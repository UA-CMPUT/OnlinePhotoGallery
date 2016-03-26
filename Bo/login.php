<?php
/**
 * Created by PhpStorm.
 * Author: Bo Zhou
 */

include("connDB.php");
if( isset( $_POST['login-button'] ) ){
    //echo "login";
    $user_name = $_POST['login-username'];
    $user_pswd = $_POST['login-password'];
    if(!function_exists('oci_connect')) die('Cannot Connect to Oracle Database');
    $conn = connect();
    $sql = "SELECT user_name, password, date_registered FROM users WHERE users.user_name='".$user_name."'";
    $stid = oci_parse( $conn, $sql );
    $result = oci_execute( $stid );

    if ( !$result ) {
        header( "location:index.php?ERR=err" );
    } else {
        $user_info = oci_fetch_array( $stid, OCI_ASSOC );
        if ( !isset( $user_info['PASSWORD'] ) ) {
            header( "location:index.php?ERR=name" );
        } elseif ( $user_pswd == $user_info['PASSWORD'] ){
            $get_person_sql = "SELECT * FROM persons WHERE user_name = '".$user_name."'";
            $get_person_stid = oci_parse($conn, $get_person_sql);
            $get_person_result = oci_execute($get_person_stid);
            if ( !$get_person_result ) {
                header( "location:index.php?ERR=name" );
            }
            $person_info = oci_fetch_array($get_person_stid, OCI_ASSOC);

            /* start new session. */
            session_start();
            $_SESSION['REG_DATE'] = $user_info['DATE_REGISTERED'];
            $_SESSION['USER_NAME'] = $_POST['login-username'];
            $_SESSION['FIRST_NAME'] = $person_info['FIRST_NAME'];
            $_SESSION['LAST_NAME'] = $person_info['LAST_NAME'];
            $_SESSION['ADDRESS'] = $person_info['ADDRESS'];
            $_SESSION['EMAIL'] = $person_info['EMAIL'];
            $_SESSION['PHONE'] = $person_info['PHONE'];
            header( "location:main_page.php" );
        } else {
            header( "location:index.php?ERR=pswd" );
        }
    }
    oci_commit($conn);
    oci_free_statement($get_person_stid);
    oci_free_statement($stid);
    oci_close($conn);
}
?>
