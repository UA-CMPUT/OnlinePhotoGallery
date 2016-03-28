<!DOCTYPE html>
<?php
/*
* CMPUT 391 Project Online Photo Gallery
* Written by Bo Zhou
* Mar 26, 2016 
* 
*/
include("connDB.php");
session_start();
if ( !isset ( $_SESSION['USER_NAME'] ) ) {
    header( "location:index.php?ERR=session" );
    exit();
};
$user_name = $_SESSION['USER_NAME'];
$conn = connect();
/* get fiends photos */
$sql_other = "SELECT i.photo_id FROM images i WHERE owner_name <> '".$user_name."' AND '".$user_name."' IN (SELECT gl.friend_id FROM group_lists gl WHERE gl.group_id = i.permitted) ORDER BY i.timing DESC, i.owner_name ASC";
$stid_other = oci_parse( $conn, $sql_other );
$result_other = oci_execute( $stid_other );
if (!$result_other){
    header( "location:index.php?ERR=err" );
}
$all_other_id = array();
while ($other = oci_fetch_array($stid_other, OCI_ASSOC)){
    array_push($all_other_id, $other["PHOTO_ID"]);
}
oci_free_statement($stid_other);

/* get most popular photos */

$sql_popular = "SELECT photo_id, count(*) AS numberOfviewer FROM images_viewed GROUP BY photo_id ORDER BY numberOfviewer DESC";
$stid_popular = oci_parse($conn, $sql_popular);
$result_popular = oci_execute($stid_popular);
$all_popular = array();
if ($result_popular){
    $n = 0;
    $tmp_top = -1;
    while ($popular = oci_fetch_array($stid_popular,OCI_ASSOC)){
        if ($popular["NUMBEROFVIEWER"] == '') {
            $real = 0;
        }else{
            $real = $popular["NUMBEROFVIEWER"];
        }
        if ($real != $tmp_top){
                $tmp_top = $real;
                $n++;
            if ($n > 5){
                break;
            }
            array_push($all_popular, $popular["PHOTO_ID"]);
        }else{
            array_push($all_popular, $popular["PHOTO_ID"]);
        }
    }
}

oci_close($conn);
?>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="all_photos" content="PHP,HTML,CSS,JAVASCRIPT">
    <meta name="author" content="Bo Zhou" >
    <style type="text/css">
        a{
            width: 20%;
            height: 110px;
            margin: 10px;
            text-align: center;
            border-image: 0;
        }
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
    <legend>All Popular Photos</legend>
    <?php
    foreach ($all_popular as $id){
//        echo "<div class='control'><a target='_parent' class='edit' href='show_image.php?id=".$info["PHOTO_ID"]."'>SHOW</a></div>";

        echo "<a target='_parent' href='show_image.php?id=".$id."'><img src='imageView.php?image_id=".$id."&original=0'/></a>";
    }
    ?>
</fieldset>
<fieldset>
    <legend>Other Users' Photos</legend>
    <?php
    foreach ($all_other_id as $id){
        echo "<a target='_parent' href='show_image.php?id=".$id."'><img src='imageView.php?image_id=".$id."&original=0'/></a>";
    }
    ?>
<!--    <a style="margin: 5px"-->
</fieldset>
</body>
</html>