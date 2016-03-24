<!DOCTYPE html>
<?php
include("connDB.php");
session_start();
if ( !isset ( $_SESSION['USER_NAME'] ) ) {
    header( "location:index.php?ERR=session" );
    exit();
};
$user_name = $_SESSION['USER_NAME'];
$conn = connect();
$sql_other = "SELECT i.photo_id FROM images i WHERE '".$user_name."' IN (SELECT gl.friend_id FROM group_lists gl WHERE gl.group_id = i.permitted)";
$sql_own = "SELECT photo_id FROM images WHERE owner_name = '".$user_name."'";
$stid_other = oci_parse( $conn, $sql_other );
$stid_own = oci_parse( $conn, $sql_own);
$result_other = oci_execute( $stid_other );
$result_own = oci_execute($stid_own);
if (!($result_other && $result_own)){
    header( "location:index.php?ERR=err" );
}
$all_other_id = array();
$all_own_id = array();
while ($other = oci_fetch_array($stid_other, OCI_ASSOC)){
    array_push($all_other_id, $other["PHOTO_ID"]);
}
while ($other = oci_fetch_array($stid_own, OCI_ASSOC)){
    array_push($all_own_id, $other["PHOTO_ID"]);
}
oci_free_statement($stid_own);
oci_free_statement($stid_other);
oci_close($conn);
?>

<html>
<head>
    <style type="text/css">
        body{
            font-family: "Segoe UI", Arial, sans-serif;
            text-align: center;
        }
        fieldset {
            border: 3px solid rgb(53, 43, 255);
            margin: 30px;
        }
        legend {
            color: rgb(243, 3, 116);
            font-size: 20px;
            font-weight: bold;
        }
        .full {
            width: 70%;
            margin:auto;
            line-height: 50px;
            /*position:relative;*/
        }
        .allfull{
            width: 100%;
            /*height: 400px;*/
            margin: auto;
        }
        .half {
            width: 50%;
            float: left;
            /*margin: 10px;*/
            /*margin-top: 10px;*/
            height: 150px;
        }
    </style>
</head>
<body>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<fieldset>
    <legend>My Own Photos</legend>
    <?php
    foreach ($all_own_id as $id){
        echo '<img src="imageView.php?image_id='.$id.'&original=0"/><br>';
    }
    ?>
</fieldset>
<fieldset>
    <legend>Other Users' Photos</legend>
    <?php
    foreach ($all_other_id as $id){
        echo '<img src="imageView.php?image_id='.$id.'&original=0"/><br>';
    }
    ?>
</fieldset>
</body>
</html>