<?php
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
            session_start();
//            echo $user_info['PASSWORD'];
//            echo $user_info['USER_NAME'];
            $_SESSION['REG_DATE'] = $user_info['DATE_REGISTERED'];
            $_SESSION['USER_NAME'] = $_POST['login-username'];
            header( "location:main_page.php" );
        } else {
            header( "location:index.php?ERR=pswd" );
        }
    }
    oci_commit($conn);
    oci_free_statement($stid);
    oci_close($conn);
}
?>
