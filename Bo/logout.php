<?php
/*
* CMPUT 391 Project Online Photo Gallery
* Written by Bo Zhou
* Mar 26, 2016
*
*/
    session_start();
    $_SESSION = array();
    if( isset( $_COOKIE[session_name()] ) ) {
        setCookie( session_name(), '', time() - 3600, '/' );
    }
    session_destroy();
    echo 'Log out successfully. Back to the login page in 3 seconds.';
    header( 'Refresh:3; url=index.php')
?>