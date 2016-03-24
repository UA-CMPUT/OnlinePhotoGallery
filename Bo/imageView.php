<?php
include("connDB.php");
session_start();
if ( !isset ( $_SESSION['USER_NAME'] ) ) {
    header( "location:index.php?ERR=session" );
    exit;
};
$conn = connect();
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
if(isset($_GET['image_id'])) {
    $sql = "SELECT thumbnail, photo FROM images WHERE photo_id=". $_GET['image_id'];
    $stid = oci_parse( $conn, $sql );
    $result = oci_execute( $stid ) or die("<b>Error:</b> Problem on Retrieving Image BLOB<br/>");
    $row = oci_fetch_array( $stid, OCI_RETURN_LOBS );
    header('Content-Type: image/jpeg');
    if (isset($_GET['original']) && $_GET['original']){
        echo $row["PHOTO"];
    } else{
        echo $row["THUMBNAIL"];
    }
    oci_free_statement($stid);
}
oci_close($conn);
?>
