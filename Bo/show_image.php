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
$conn = connect();
$user_name = $_SESSION["USER_NAME"];
$id = $_GET["id"];
$pass = 0;

$sql_check = "SELECT g.friend_id FROM images i, group_lists g WHERE i.photo_id = '".$id."' AND i.permitted = g.group_id";
$stid_check = oci_parse($conn, $sql_check);
$result_check = oci_execute($stid_check);
$all_friend = array();
//array_push($all_friend, $user_name);

if ($result_check){
    while ($friend = oci_fetch_array($stid_check, OCI_ASSOC)) {
        array_push($all_friend, $friend["FRIEND_ID"]);
    }
    if (in_array($user_name, $all_friend)){
        $pass = 1;
    }
    oci_free_statement($stid_check);
}else{
    echo '<div id=\'message\'>Error! Cannot connect to server!</div>';
    oci_free_statement($stid_check);
    oci_rollback($conn);
    oci_close($conn);
    exit();
}

$sql_check2 = "SELECT owner_name FROM images WHERE photo_id = '".$id."'";
$stid_check2 = oci_parse($conn, $sql_check2);
$result_check2 = oci_execute($stid_check2);
if ($result_check2){
    $name = oci_fetch_array($stid_check2, OCI_ASSOC);
    if ($user_name == $name["OWNER_NAME"]){
        $pass = 1;
    }
    oci_free_statement($stid_check2);
}else{
    echo '<div id=\'message\'>Error! Cannot connect to server!</div>';
    oci_free_statement($stid_check2);
    oci_rollback($conn);
    oci_close($conn);
    exit();
}
//echo $pass."<br>";

if ($pass == 1) {
    $sql1 = "SELECT viewer FROM images_viewed WHERE photo_id='" . $id . "'";
    $stid1 = oci_parse($conn, $sql1);
    $result1 = oci_execute($stid1);
    $all_viewer = array();
    if ($result1) {
        while ($name = oci_fetch_array($stid1, OCI_ASSOC)) {
            array_push($all_viewer, $name["VIEWER"]);
        }
        oci_free_statement($stid1);
    } else {
        echo '<div id=\'message\'>Error! Cannot check popularity!</div>';
        oci_rollback($conn);
        oci_free_statement($stid1);
        oci_close($conn);
        exit();
    }
    if (!(in_array($user_name, $all_viewer))) {
        $sql2 = "INSERT INTO images_viewed VALUES ('" . $id . "', '" . $user_name . "')";
        $stid2 = oci_parse($conn, $sql2);
        $result2 = oci_execute($stid2);
        if ($result2) {
            oci_commit($conn);
            oci_free_statement($stid2);
        } else {
            oci_rollback($conn);
            echo '<div id=\'message\'>Error! Cannot update viewer !</div>';
        }
    }

}

?>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="all_photos" content="PHP,HTML,CSS,JAVASCRIPT">
    <meta name="author" content="Bo Zhou" >
    <style type="text/css">
        #message{
            text-align: center;
            background-color: rgb(240, 240, 240);
            position: fixed;
            left: 100px;
            right: 100px;
            margin-top: 20%;
            padding: 20px;
            border: 2px groove black;
        }
        .left {
            float: left;
            width: 100%;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            line-height: 30px;
            /*margin-left: 50%;*/
            /*margin-right: 15px;*/
        }
        /*.right {*/
            /*width: 50%;*/
            /*text-align: left;*/
            /*line-height: 30px;*/
        /*}*/
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
        .btn {
            background: #3498db;
            background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
            background-image: -moz-linear-gradient(top, #3498db, #2980b9);
            background-image: -ms-linear-gradient(top, #3498db, #2980b9);
            background-image: -o-linear-gradient(top, #3498db, #2980b9);
            background-image: linear-gradient(to bottom, #3498db, #2980b9);
            -webkit-border-radius: 28;
            -moz-border-radius: 28;
            border-radius: 28px;
            font-family: Arial;
            color: #ffffff;
            font-size: 20px;
            padding: 10px 20px 10px 20px;
            text-decoration: none;
        }
        .btn:hover {
            background: #3cb0fd;
            background-image: -webkit-linear-gradient(top, #3cb0fd, #3498db);
            background-image: -moz-linear-gradient(top, #3cb0fd, #3498db);
            background-image: -ms-linear-gradient(top, #3cb0fd, #3498db);
            background-image: -o-linear-gradient(top, #3cb0fd, #3498db);
            background-image: linear-gradient(to bottom, #3cb0fd, #3498db);
            text-decoration: none;
        }
    </style>
</head>
<body>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<fieldset>
    <legend>Photo</legend>
    <?php
        if ($pass == 1){
            $sql3 = "SELECT owner_name, subject, place, timing, description FROM images WHERE photo_id='".$id."'";
            $stid3 = oci_parse($conn, $sql3);
            $result3 = oci_execute($stid3);
            $info = '';
            if ($result3) {
                $info = oci_fetch_array($stid3, OCI_ASSOC);
                oci_commit($conn);
            } else {
                oci_rollback($conn);
                echo '<div id=\'message\'>Error! Cannot get image info !</div>';
            }
            oci_free_statement($stid3);

            echo '<img src="imageView.php?image_id='.$id.'&original=1"/><br>';
            echo "<div class='left' >";
            echo "Subject: ".$info['SUBJECT'].'<br>';
            echo "Place: ".$info['PLACE'].'<br>';
            echo "DATE: ".$info['TIMING'].'<br>';
            echo "Description: ".$info['DESCRIPTION'].'<br>';
            echo "Owner: ".$info['OWNER_NAME'].'<br>';
            echo "</div>";
        }else{
            echo "<img src='ref/dist/img/denied.jpg' ><br>";
        }
    ?>
    <input style="margin-top: 20px" class="btn" type="button" value="BACK" onclick="history.go(-1);return true;">
</fieldset>

<script type="text/javascript">
    function popMessage() {
        var box = document.getElementById( "message" );
        box.style.display = "none";
        self.location = 'profile.php';
    }
    setTimeout( "popMessage()", 1000 );
</script>
<?php
oci_close($conn);
?>
</body>
</html>