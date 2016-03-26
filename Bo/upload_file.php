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
    oci_close($conn);
?>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="upload_file" content="PHP,HTML,CSS,JAVASCRIPT">
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
    <legend>Files Uploading</legend>
    <form name="upload-files" method="post" action="upload-one.php" enctype="multipart/form-data">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <?php
        if ($_GET['ACK']==1) echo "<div id='success-show' style='color:#0000FF'>Successful uploading. Please upload another file.</div>" ;
        elseif ($_GET['ACK']== -1) echo "<div id='success-show' style='color:#FF0000'>Cannot your upload photo. Please try again.</div>" ;
        ?>
        <div class='half' style="margin-top: 20px">
            <strong>1. Select Upload File</strong><br>
            <input name="file" type="file" id="upload-file" this.style.backgroundColor='rgb(178,234,255)' style='width: 80%; border: 1px dotted grey'><br>
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
        <div class='half' style='margin-top: 30px; height: 100px'>
            <strong>3. Input Date (Optional)</strong><br>
            <input type="text" name="date-input" placeholder="Enter date: dd/mm/yyyy hh24:mi:ss" style='width: 80%'>
        </div>
        <div class = 'half' style='margin-top: 30px; height: 100px'>
            <strong>4. Input Title (Optional)</strong><br>
            <input type="text" style="width: 80%" name="title" placeholder="Enter title here..." style='...'>
        </div>
        <div class='half' style='...'>
            <strong>5. Input Photo Taken Place (Optional)</strong><br>
            <textarea name="place" placeholder="Enter place here..." style='width: 80%; height: 100px'></textarea>
        </div>
        <div class='half' style='margin-top: 30px; line-height: 30px'>
            <strong>6. Input Description (Optional)</strong><br>
            <textarea name="description" placeholder="Enter description here..." style='width: 80%; height: 100px'></textarea>
        </div>
        <span id="lblError" style="color: red;"></span>
        <input value="Upload" name="button" id="upload-button" type="submit" style='margin-bottom: 30px'/>
    </form>
</fieldset>

<script type="text/javascript">
    function hideMessage() {
        var successShow = $("#success-show");
        successShow.html('<br>');
    };
    setTimeout(hideMessage, 5000);
    /* check file when submit */
    $("body").on("click", "#upload-button", function () {
        var lblError = $("#lblError");
        var oFile = document.getElementById('upload-file');
        if (oFile.value == ""){
            lblError.html("Please choose a file to upload");
            return false;
        }
        var allowedFiles = [".jpg", ".jpeg", ".gif"];
        var fileUpload = $("#upload-file");
        //var fileSize = this.files[0].size;
        var fileSize = $('#upload-file')[0].files[0].size;
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + allowedFiles.join('|') + ")$");
        if (!(regex.test(fileUpload.val().toLowerCase()) && fileSize < 10485760 )) {
            lblError.html("Please upload files less than 10 MB with extensions: <b>" + allowedFiles.join(', ') + "</b> only.");
            return false;
        }
        lblError.html('');
        return true;
    });

    document.getElementById('upload-file').addEventListener('change', checkFile, false);
    approveletter.addEventListener('change', checkFile, false);
    function checkFile(e) {
        var file_list = e.target.files;
        for (var i = 0, file; file = file_list[i]; i++) {
            var sFileName = file.name;
            var sFileExtension = sFileName.split('.')[sFileName.split('.').length - 1].toLowerCase();
            var iFileSize = file.size;
            var iConvert = (file.size / 10485760).toFixed(2);
            if (!(sFileExtension === "jpeg" ||sFileExtension === "jpg"|| sFileExtension === "gif" ) || iFileSize > 10485760) {
                txt = "File type : " + sFileExtension + "\n\n";
                txt += "Size: " + iConvert + " MB \n\n";
                txt += "Please make sure your file is in jpg or jpeg or gif format and less than 10 MB.\n\n";
                alert(txt);
            }
        }
    }
</script>
</body>
</html>