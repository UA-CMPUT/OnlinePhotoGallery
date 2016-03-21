<!DOCTYPE html>
<?php
    include("connDB.php");
    session_start();
    if ( !isset ( $_SESSION['USER_NAME'] ) ) {
        header( "location:index.php?ERR=session" );
    };
    $user_name = $_SESSION['USER_NAME'];
    $conn = connect();
    $sql = "SELECT group_id, group_name FROM groups WHERE user_name='".$user_name."'";
    $stid = oci_parse( $conn, $sql );
    $result = oci_execute( $stid );
    if (!$result){
        header( "location:index.php?ERR=err" );
    }
    $all_group_info = array();
    while ($group = oci_fetch_row($stid)){
        array_push($all_group_info, $group);
    }
    oci_free_statement($stid);
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
            border: 3px solid rgb(255, 200, 150);
            margin: 30px;
        }
        legend {
            color: rgb(200, 50 ,50);
            font-size: 20px;
            font-weight: bold;
        }
        audio {
            display: none;
        }
        .half {
            width: 50%;
            float: left;
            line-height: 30px;
        }
    </style>
</head>
<body>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<fieldset>
    <legend>Groups Management</legend>
    <form name="add-group" method="post" action="add-group.php" enctype="multipart/form-data">
        <?php
        if ($_GET['ACK']==1) echo "<div id='success-show' style='color:#0000FF'>Add group success.</div>" ;
        elseif ($_GET['ACK']== -1) echo "<div id='success-show' style='color:#FF0000'>Cannot add new group. Please try again.</div>" ;
        elseif ($_GET['ACK']== -2) echo "<div id='success-show' style='color:#FF0000'>You already have this group. Please change a group name.</div>" ;
        ?>
        <div class='half' style='margin-top: 30px; height: 100px'>
            <strong>Input New Group</strong><br>
            <input type="text" name="group-input" placeholder="Enter new group here..." style='width: 80%'><br>
        </div>

        <div class='half' style='margin-top: 30px; height: 100px'>
            <strong>2. Select Who Can See Your Photos </strong><br>
            <div id='t2' style='...'>
                <select name='group-name'>
                    <?php foreach($all_group_info as $info) {
                        if ($info[1] == "private"){
                            echo "<option value='" . $info[0] . "' selected>" . $info[1];
                        }else {
                            echo "<option value='" . $info[0] . "'>" . $info[1];
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <span id="lblError" style="color: red;"></span>
        <input value="add" name="button" id="upload-button" type="submit" style='margin-bottom: 30px'/>
    </form>
</fieldset>


<!-- get audio duration -->
<script type="text/javascript">
    function hideMessage() {
        var successShow = $("#success-show");
        successShow.html('<br>');
    };
    setTimeout(hideMessage, 5000);
</script>
</body>
</html>