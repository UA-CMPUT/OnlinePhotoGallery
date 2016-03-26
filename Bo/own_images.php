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

$sql_own = "SELECT i.* g.group_name FROM images i, groups g WHERE i.owner_name = '".$user_name."' AND i.permitted = g.group_id";
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

?>
<html>
<head>
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
    </style>
</head>

<body>
<?php
if( isset ( $_POST['EDIT_PHOTO'] ) ){
    $edit_subject = $_POST['EDIT_SUBJECT'];
    $edit_date = $_POST['EDIT_DATE'];
    $edit_place = $_POST['EDIT_PLACE'];
    $edit_description = $_POST['EDIT_DESCRIPTION'];

    $edit_sql = 'UPDATE images SET SUBJECT=\''.$edit_subject.'\', LAST_=\''.$edit_last_name.'\', ADDRESS=\''.$edit_address.'\', EMAIL=\''.$edit_email.'\', PHONE=\''.$edit_phone.'\' WHERE PERSON_ID=\''.$_SESSION['PERSON_ID'].'\'';
    $edit_stid = oci_parse( $conn, $edit_sql );
    $edit_result = oci_execute( $edit_stid );

    if ( !$edit_result ) {
        $err = oci_error( $edit_stid );
        echo '<div id=\'message\'>'.$err['message'].'</div>';
    } else {
        echo '<div id=\'message\'>Success!</div>';
    }
    oci_free_statement($edit_stid);
}
?>

<?php
foreach ($all_info as $info){
    echo "<fieldset>
    <legend>Photo: </legend>
    <div class='left'>
        Subject:<br>
        Permitted:<br>
        Date:<br>
        Place:<br>
        Description:<br>
    </div>
    <div class='right' id='photo1'>
        <div style='float: left'>";
    echo $info['SUBJECT'].'<br>';
    echo $info['GROUP_NAME'].'<br>';
    echo $info['DATE'].'<br>';
    echo $info['PLACE'].'<br>';
    echo $info['DESCRIPTION'].'<br>';
    echo "</div>";
    echo "<div class='control'>
            <a class='edit' href='#' onclick='edit(\"photo1\", \"photo2\")'>edit</a>
        </div>
    </div>";

    echo "<div class='right' id='photo2' style='display: none'>
            <form method='post' action='#'>
                <div style='float: left'>";
    echo '<input type=\'text\' name=\'EDIT_SUBJECT\' value=\''.$person_info['FIRST_NAME'].'\'><br>';
    echo "<select name='EDIT_PERMITTED'>";
    foreach($all_group_info as $g_info) {
        if ($info["GROUP_ID"] == $g_info["GROUP_ID"]){
            echo "<option value='" . $g_info[0] . "' selected>" . $g_info[1];
        }else {
            echo "<option value='" . $g_info[0] . "'>" . $g_info[1];
        }
    }
    echo "</select>";
    echo '<input type=\'text\' name=\'EDIT_DATE\' value=\''.$info['DATE'].'\'><br>';
    echo '<textarea name=\'EDIT_PLACE\' value=\''.$info['PLACE'].'\'><br>';
    echo '<textarea name=\'EDIT_DESCRIPTION\' value=\''.$info['DESCRIPTION'].'\'><br>';
    echo '</div>';
    echo "<div class='control'>
                <a class='edit' href='#' onclick='edit(\"photo1\", \"photo2\")'>Cancel</a>
                <button type=\"submit\" name=\"EDIT_PHOTO\">
                    <a class='edit' onclick='edit(\"photo1\", \"photo2\")'>Save</a>
                </button>
            </div>
            </form>
        </div>
    </fieldset>";
}
?>
<script type="text/javascript">
    function edit( pos_1, pos_2 ) {
        var tar_1 = typeof pos_1 == "string" ? document.getElementById( pos_1 ) : pos_1;
        var tar_2 = typeof pos_2 == "string" ? document.getElementById( pos_2 ) : pos_2;
        if ( tar_1.style.display != "none" ) {
            tar_1.style.display = "none";
            tar_2.style.display = "block";
        } else {
            tar_1.style.display = "block";
            tar_2.style.display = "none";
            location.reload();
        }
    }
    function popMessage() {
        var box = document.getElementById( "message" );
        box.style.display = "none";
        self.location = 'profile.php';
    }
    setTimeout( "popMessage()", 1000 );
</script>
</body>
</html>
<?php
oci_free_statement($stid_own);
oci_close($conn);
?>
