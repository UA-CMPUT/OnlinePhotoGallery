<!DOCTYPE html>
<?php
    include("connDB.php");
    session_start();
    if ( !isset ( $_SESSION['USER_NAME'] ) ) {
        header( "location:index.php?ERR=session" );
        exit;
    };
    $user_name = $_SESSION['USER_NAME'];
    $conn = connect();
    $sql = "SELECT group_id, group_name FROM groups WHERE user_name='".$user_name."'";
//    $sql_user_list = "SELECT user_name FROM users WHERE user_name <> 'admin'";
//    $sql_group_list = "SELECT user"
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
        .out{
            width: 100%;
            height: 500px;
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
    <legend>Add New Group</legend>
    <form name="add-group" method="post" action="add-group.php" enctype="multipart/form-data">
        <?php
        if ($_GET['ACK']==1) echo "<div id='success-show' style='color:#0000FF'>Add group success.</div>" ;
        elseif ($_GET['ACK']== -1) echo "<div id='success-show' style='color:#FF0000'>Cannot add new group. Please try again.</div>" ;
        elseif ($_GET['ACK']== -2) echo "<div id='success-show' style='color:#FF0000'>You already have this group. Please change a group name.</div>" ;
        ?>
        <div class='full' style='margin-top: 30px; height: 100px'>
            <strong>Input New Group</strong><br>
            <input type="text" name="group-input" placeholder="Enter new group here..." style='width: 80%'><br>
        </div>
        <span id="lblError" style="color: red;"></span>
        <input class="btn" value="add" name="button" id="upload-button" type="submit" style='margin-bottom: 30px'/>
    </form>
</fieldset>
<fieldset>
    <legend>Groups Management</legend>
    <div class='full' style='margin-top: 30px; height: 100'>
        <?php
        if ($_GET['ACK']== 2) echo "<div id='success-show' style='color:#0000FF'>Add friends success.</div>" ;
        elseif ($_GET['ACK']== 3) echo "<div id='success-show' style='color:#0000FF'>Delete friends success.</div>" ;
        elseif ($_GET['ACK']== -3) echo "<div id='success-show' style='color:#FF0000'>Cannot add friends into the group. Please try again.</div>" ;
        elseif ($_GET['ACK']== -4) echo "<div id='success-show' style='color:#FF0000'>Cannot delete friends from the group. Please try again.</div>" ;
        ?>
        <strong>Select the Group You Want to Edit</strong><br>
        <div class="allfull" id='t2' style='...'>
            <form>
                <select name='group-name' onchange="showUser(this.value)">
                    <option value="-1" selected>None</option>
                    <?php
                    foreach($all_group_info as $info) {
                        echo "<option value='" . $info[0] . "'>" . $info[1]."</option>";
                    }
                    ?>
                </select>
            </form>
            <div class="out" id="txtHint">
            </div>
        </div>
    </div>
</fieldset>


<script type="text/javascript">
    function hideMessage() {
        var successShow = $("#success-show");
        successShow.html('<br>');
    };
    setTimeout(hideMessage, 5000);

    /* select onchange event show */
    var xmlHttp;

    function showUser(str)
    {
        xmlHttp=GetXmlHttpObject();
        if (xmlHttp==null)
        {
            alert ("Browser does not support HTTP Request");
            return
        }
        var url="select-show.php";
        url=url+"?q="+str;
        url=url+"&sid="+Math.random();
        xmlHttp.onreadystatechange=stateChanged;
        xmlHttp.open("GET",url,true);
        xmlHttp.send(null);
    }

    function stateChanged()
    {
        if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
        {
            document.getElementById("txtHint").innerHTML=xmlHttp.responseText;
        }
    }

    function GetXmlHttpObject()
    {
        var xmlHttp=null;
        try
        {
            // Firefox, Opera 8.0+, Safari
            xmlHttp=new XMLHttpRequest();
        }
        catch (e)
        {
            //Internet Explorer
            try
            {
                xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e)
            {
                xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
        }
        return xmlHttp;
    }
</script>
</body>
</html>