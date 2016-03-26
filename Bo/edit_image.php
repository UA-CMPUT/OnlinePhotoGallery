<!DOCTYPE html>
<?php
/**
 * Created by PhpStorm.
 * Author: Bo Zhou
 */

//echo "edit<br>";
include("connDB.php");
session_start();
if ( !isset ( $_SESSION['USER_NAME'] ) ) {
    header( "location:index.php?ERR=session" );
    exit();
};
$conn = connect();
$id = $_GET["id"];
$user_name = $_SESSION["USER_NAME"];

$sql_date_format = "alter session set nls_date_format = 'dd/mm/yyyy hh24:mi:ss'";
$stid_date_format = oci_parse( $conn, $sql_date_format );
$result_date_format = oci_execute( $stid_date_format );
oci_free_statement($stid_date_format);
$sql_own = "SELECT i.*, g.group_name FROM images i, groups g WHERE i.photo_id = '".$id."' AND i.permitted = g.group_id";
$stid_own = oci_parse( $conn, $sql_own);
$result_own = oci_execute($stid_own);
$all_info = array();
if ( !$result_own ) {
    echo '<div id=\'message\'>Error! Unknown Image ID!</div>';
} else {
    while ($info = oci_fetch_array($stid_own, OCI_ASSOC)){
        array_push($all_info, $info);
    }
}

$sql = "SELECT group_id, group_name FROM groups WHERE user_name='".$user_name."'";
$sql2 = "SELECT group_id, group_name FROM groups WHERE user_name IS NULL";
$stid = oci_parse( $conn, $sql );
$stid2 = oci_parse( $conn, $sql2);
$result = oci_execute( $stid );
$result2 = oci_execute( $stid2 );

if (!($result2 && $result)){
    header( "location:index.php?ERR=err" );
    exit();
}

$all_group_info = array();
while ($group = oci_fetch_row($stid2)){
    array_push($all_group_info, $group);
}
while ($group = oci_fetch_row($stid)){
    array_push($all_group_info, $group);
}

oci_free_statement($stid);
oci_free_statement($stid2);
oci_free_statement($stid_own);
oci_close($conn);
?>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="edit_image" content="PHP,HTML,CSS,JAVASCRIPT">
    <meta name="author" content="Bo Zhou" >
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
        .half {
            width: 70%;
            margin:auto;
        }

    </style>
</head>
<body>
<fieldset>
    <legend>Image</legend>
    <div style="width: 100%">
        <img src="imageView.php?image_id=<?php echo $all_info[0]["PHOTO_ID"]?>&original=0">
    </div>
</fieldset>

<fieldset>
    <legend>Image Info</legend>
    <form name="upload-files" method="post" action="update-image.php?id=<?php echo $id ?>" enctype="multipart/form-data">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <?php
        if ($_GET['ACK']==1) echo "<div id='success-show' style='color:#0000FF'>Successful update.</div>" ;
        elseif ($_GET['ACK']== -1) echo "<div id='success-show' style='color:#FF0000'>Cannot update photo. Please try again.</div>" ;
        ?>
        <div class='half' style='margin-top: 30px; height: 100px'>
            <strong>1. Edit Permission </strong><br>
            <div id='t2' style='...'>
                <select name='group-name'>
                    <?php foreach($all_group_info as $info) {
                        if ($info[0] == $all_info[0]["PERMITTED"]){
                            echo "<option value='" . $info[0] . "' selected>" . $info[1];
                        }else {
                            echo "<option value='" . $info[0] . "'>" . $info[1];
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class='half' style='margin-top: 30px; height: 100px'>
            <strong>2. Edit Date </strong><br>
            <input type="text" name="date-input" value="<?php echo $all_info[0]["TIMING"] ?>" style='width: 80%'>
        </div>
        <div class = 'half' style='margin-top: 30px; height: 100px'>
            <strong>3. Edit Title </strong><br>
            <input type="text" style="width: 80%" name="title" value="<?php echo $all_info[0]["SUBJECT"] ?>" style='...'>
        </div>
        <div class='half' style='...'>
            <strong>4. Edit Place </strong><br>
            <textarea name="place" style='width: 80%; height: 100px'><?php echo $all_info[0]["PLACE"] ?></textarea>
        </div>
        <div class='half' style='margin-top: 30px; line-height: 30px'>
            <strong>5. Edit Description </strong><br>
            <textarea name="description" style='width: 80%; height: 100px'><?php echo $all_info[0]["DESCRIPTION"] ?></textarea>
        </div>
        <span id="lblError" style="color: red;"></span>
        <input value="SAVE" name="button" id="edit-button" type="submit" style='margin-bottom: 30px'/>
        <input type="button" value="CANCEL" onclick="location.href='own_images.php'">
    </form>
</fieldset>


<script type="text/javascript">
    function hideMessage() {
        var successShow = $("#success-show");
        successShow.html('<br>');
    };
    setTimeout(hideMessage, 5000);
</script>
</body>
</html>
