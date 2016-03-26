<?php
/**
 * Created by PhpStorm.
 * Author: Bo Zhou
 */
function connect() {
    $conn = oci_connect('bzhou2', 'ZHOUbo2016');
    if (!$conn) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

    return $conn;
}
?>