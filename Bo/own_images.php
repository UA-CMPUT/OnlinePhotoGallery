<!DOCTYPE html>
<?php
include("connDB.php");
session_start();
if ( !isset ( $_SESSION["USER_NAME"] ) ) {
    header( "location:index.php?ERR=session" );
}
$user_name = $_SESSION["USER_NAME"];
$conn = connect();

$sql_date_format = "alter session set nls_date_format = 'dd/mm/yyyy hh24:mi:ss'";
$stid_date_format = oci_parse( $conn, $sql_date_format );
$result_date_format = oci_execute( $stid_date_format );
oci_free_statement($stid_date_format);
$sql_own = "SELECT i.*, g.group_name FROM images i, groups g WHERE i.owner_name = '".$user_name."' AND i.permitted = g.group_id";
$stid_own = oci_parse( $conn, $sql_own);
$result_own = oci_execute($stid_own);
$all_info = array();
if ( !$result_own ) {
    echo '<div id=\'message\'>Error! Cannot connect to data server!</div>';
} else {
    while ($info = oci_fetch_array($stid_own, OCI_ASSOC)){
        array_push($all_info, $info);
    }
}
oci_free_statement($stid_own);
oci_close($conn);

?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="own_page" content="PHP,HTML,CSS,JAVASCRIPT">
    <meta name="author" content="Bo Zhou" >
    <style type="text/css">
        a {
            color: rgb(200, 100, 50);
            font-weight: bold;
            text-decoration: none;
            transition-property: background-color, color;
            transition-timing-function: ease, ease;
            display: block;
        }
        button{
            border: none;
            background:transparent;
        }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
        }
        fieldset {
            border: 3px solid rgb(255, 200, 150);
            margin: 30px;
        }
        legend {
            color: rgb(200, 50 ,50);
            font-size: 20px;
            font-weight: bold;
        }
        .left {
            float: left;
            width: 150px;
            text-align: right;
            font-size: 15px;
            font-weight: bold;
            line-height: 30px;
            margin-right: 15px;
        }
        .right {
            text-align: left;
            line-height: 30px;
        }
        .control {
            float: right;
            width: 10%;
            height: 50%;
        }
        .edit {
            font-size: 15px;
            text-align: center;
            padding: 5px;
            color: rgb(100, 100, 200);
            margin-right: 15px;
            transition-duration: 0.15s, 0.15s;
        }
        .edit:hover {
            background-color: rgb(200, 200, 255);
        }
    </style>
</head>

<body>
<div style="width: 100%">
    <?php
    if ($_GET['ACK']==1) echo "<div id='success-show' style='color:#0000FF'>Delete photo success.</div>" ;
    elseif ($_GET['ACK']== 2) echo "<div id='success-show' style='color:#0000FF'>Update photo success.</div>" ;
    elseif ($_GET['ACK']== -1) echo "<div id='success-show' style='color:#FF0000'>Cannot delete photo. Please try again.</div>" ;
    elseif ($_GET['ACK']== -2) echo "<div id='success-show' style='color:#FF0000'>Cannot update photo. Please try again.</div>" ;
    ?>
</div>

<?php

$num = 0;
foreach ($all_info as $info){
    $num++;
    echo "<fieldset>
    <legend>Photo: ".$num."</legend>";
    echo "<div class='left'><img src=\"imageView.php?image_id='".$info["PHOTO_ID"]."'&original=0\"></div>";
    echo "<div class='left'>
        Subject:<br>
        Permitted:<br>
        Date:<br>
        Place:<br>
        Description:<br>
    </div>
    <div class='right' id='photo1".$info["PHOTO_ID"]."'>
        <div style='float: left'>";
    echo $info['SUBJECT'].'<br>';
    echo $info['GROUP_NAME'].'<br>';
    echo $info['TIMING'].'<br>';
    echo $info['PLACE'].'<br>';
    echo $info['DESCRIPTION'].'<br>';
    echo "</div>";
    echo "<div class='control'><a class='edit' href='delete-image.php?id=".$info["PHOTO_ID"]."'>DELETE</a></div>";
    echo "<div class='control'><a class='edit' href='edit_image.php?id=".$info["PHOTO_ID"]."'>EDIT</a></div>";
echo "</fieldset>";
}
?>
<script type="text/javascript">
    function hideMessage() {
        var successShow = $("#success-show");
        successShow.html('<br>');
    };
    setTimeout(hideMessage, 5000);
</script>
</body>
</html>