<?php

require_once("connDB.php");

session_start();
if (!$_SESSION['USER_NAME']) {
    redirect('index.php');
}

$user = $_SESSION['username'];

$message = 'Select an image for upload';
$registered = true;
$php_self = $_SERVER['PHP_SELF'];

// Retrieve groups the user can upload to
$conn=connect();

if ($user == 'admin') {
    $groups = '';
    $sql = 'SELECT g.group_id, g.group_name, g.user_name FROM groups g';
}
else {
    $groups = '<option value="2">private</option><option value="1">public</option>';
    $sql = 'SELECT g.group_id, g.group_name, g.user_name FROM groups g left outer join group_lists l on g.group_id=l.group_id WHERE g.user_name=\'' . $user . '\' or l.friend_id=\'' . $user . '\'';
}

$stid = oci_parse($conn, $sql);
oci_execute($stid);

while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    $group_id = $row['GROUP_ID'];
    $group_name = $row['GROUP_NAME'];
    $group_owner = $row['USER_NAME'];
    $groups .= '<option value="'.$group_id.'">'.$group_name.' - ' . $group_owner .'</option>';
}

oci_free_statement($stid);
oci_close($conn);

// Define max thumbnail size
define('MAX_THUMBNAIL_DIMENSION', 100);

// Define default timezone
date_default_timezone_set('America/Denver');

// modified from https://docs.oracle.com/cd/B28359_01/appdev.111/b28845/ch7.htm
if (!empty($_POST) && isset($_POST['submitUpload']) && isset($_FILES['userfiles']))
{
    // Iterate through all files that were selected
    foreach ($_FILES['userfiles']['name'] as $i => $name) {
        // Try to upload image to database in blob format
        try    {
            // Retrieve entered info
            $subject = $_POST['subject'];
            $place = $_POST['place'];
            $description = $_POST['description'];
            $date = $_POST['date'];//date('d.M.y');
            $date = str_replace('-', '/', $date);
            $group = $_POST['group_id'];

            $imageFileType = pathinfo($_FILES['userfiles']['name'][$i], PATHINFO_EXTENSION);

            $imgcheck = 0;

            // Check to see that image type is jpeg, jpg, or gif according to project spec
            switch ($imageFileType) {
                case 'jpg':
                    if (getimagesize($_FILES['userfiles']['tmp_name'][$i]) != false)
                        $imgcheck = 1;
                    break;
                case 'jpeg':
                    if (getimagesize($_FILES['userfiles']['tmp_name'][$i]) != false)
                        $imgcheck = 1;
                    break;
                case 'gif':
                    if (getimagesize($_FILES['userfiles']['tmp_name'][$i]) != false)
                        $imgcheck = 1;
                    break;
                default:
                    $imgcheck = 0;
            }

            // Image accepted, upload
            if($imgcheck == 1) {
                $image = file_get_contents($_FILES['userfiles']['tmp_name'][$i]);
                $thumbnail = thumbnail($_FILES['userfiles']['tmp_name'][$i]);

                $name = $_FILES['userfiles']['name'][$i];
                $maxsize = 99999999;

                // Make sure image size is less than max
                if($_FILES['userfiles']['size'][$i] < $maxsize ) {
                    ini_set('display_errors', 1);
                    error_reporting(E_ALL);

                    //establish connection
                    $conn=connect();
                    if (!$conn) {
                        $e = oci_error();
                        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                    }

                    // Generate unique ID
                    $curr_id = hexdec(uniqid());

                    // Need to assign a unique ID to every picture and let the uploader choose group for permission
                    $sql = 'INSERT INTO images VALUES '
                        . '('.$curr_id.',\''.$user.'\',\''.$group.'\',\''.$subject.'\',\''.$place.'\','
                        . 'TO_DATE(\''.$date.'\', \'yyyy/mm/dd\'),\''.$description.'\',empty_blob(),empty_blob()) '
                        . 'RETURNING thumbnail, photo INTO :thumbnail, :photo';

                    $stid = oci_parse($conn, $sql);

                    // Create blobs from photo and thumbnail
                    $thumbnail_blob = oci_new_descriptor($conn, OCI_D_LOB);
                    $photo_blob = oci_new_descriptor($conn, OCI_D_LOB);

                    oci_bind_by_name($stid, ':thumbnail', $thumbnail_blob, -1, OCI_B_BLOB);
                    oci_bind_by_name($stid, ':photo', $photo_blob, -1, OCI_B_BLOB);

                    $res=oci_execute($stid, OCI_NO_AUTO_COMMIT);

                    // Save image blobs to database
                    if(!$thumbnail_blob->save($thumbnail) || !$photo_blob->save($image)) {
                        oci_rollback($conn);
                    }
                    else {
                        oci_commit($conn);
                    }

                    if (!$res) {
                        $err = oci_error($stid);
                        echo htmlentities($err['message']);
                    }

                    oci_free_statement($stid);

                    // Sync indexes for searching
                    $sql = 'BEGIN sync_index; END;';
                    $stid = oci_parse($conn, $sql);
                    oci_execute($stid);
                    oci_free_statement($stid);

                    oci_close($conn);

                    $photo_blob->free();
                    $thumbnail_blob->free();

                    $message = '<p>Thank you for submitting</p>';
                }
                else {
                    // throw an exception if image too large
                    throw new Exception("File Size Error");
                }
            }
        }
        catch(Exception $e) {
            echo '<h4>'.$e->getMessage().'</h4>';
        }
    }
}

// modified from https://docs.oracle.com/cd/B28359_01/appdev.111/b28845/ch7.htm
// Create a thumbnail from the submitted image
function thumbnail($imgfile) {
    list($w, $h, $type) = getimagesize($imgfile);

    // Retrieve old image
    switch ($type) {
        case IMAGETYPE_GIF:
            $src_img = imagecreatefromgif($imgfile);
            break;
        case IMAGETYPE_JPEG:
            $src_img = imagecreatefromjpeg($imgfile);
            break;
        case IMAGETYPE_PNG:
            $src_img = imagecreatefrompng($imgfile);
            break;
        default:
            throw new Exception('Unrecognized image type ' . $type);
    }

    if ($w > MAX_THUMBNAIL_DIMENSION || $h > MAX_THUMBNAIL_DIMENSION) {
        // Rescale image to thumbnail size
        $scale =  MAX_THUMBNAIL_DIMENSION / (($h > $w) ? $h : $w);
        $nw = $w * $scale;
        $nh = $h * $scale;

        $dest_img = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $nw, $nh, $w, $h);

        // overwrite file with new thumbnail
        switch ($type) {
            case IMAGETYPE_JPEG:
                // overwrite file with new thumbnail
                imagejpeg($dest_img, $imgfile);
                break;
            case IMAGETYPE_GIF:
                imagegif($dest_img, $imgfile);
                break;
            default:
                throw new Exception('Unrecognized image type ' . $type);
        }

        // Clean up
        imagedestroy($src_img);
        imagedestroy($dest_img);
    }

    return file_get_contents($imgfile);
}

?>

<html>
<head><title>Upload Image</title></head>
<body>

<form name="HomeForm" action="home.php" method="get" >

    <input type="submit" value="Home">

</form>

<h1><center>Upload</center></h1>

<form enctype="multipart/form-data" id='upload' action="<?php echo $php_self?>" method='post'
      accept-charset='UTF-8'>

    <p><?php echo $message ?></p>

    <input type='hidden' name='MAX_FILE_SIZE' value='99999999' />
    <input type="file" name="userfiles[]" id="userfile" multiple="" directory="" webkitdirectory="" mozdirectory="" />

    <table>
        <tr valign=top align=left>
            <td>
                <b><i>Subject*: </i></b></td>
            <td>
                <input type='text' name='subject' id='subject' maxlength="128" /><br>
            </td>
        </tr>
        <tr valign=top align=left>
            <td>
                <b><i>Place*: </i></b></td>
            <td>
                <input type='text' name='place' id='place' maxlength="128" /><br>
            </td>
        </tr>
        <tr valign=top align=left>
            <td>
                <b><i>Date*: </i></b></td>
            <td>
                <!-- CHECK TO SEE IF THIS CAN BE CHANGED TO INPUT TYPE = DATE IN CHROME maxlength="12" -->
                <input type='date' name='date' id='date'/><br>
            </td>
        </tr>
        <tr valign=top align=left>
            <td>
                <b><i>Description*: </i></b></td>
            <td>
                <input type='text' name='description' id='description' maxlength="2048" /><br>
            </td>
        </tr>
        <tr valign=top align=left>
            <td>
                <b><i>Permission*: </i></b></td>
            <td>
                <select name="group_id">
                    <?php
                    echo $groups;

                    ?>
                </select>
                <br>
            </td>
        </tr>
    </table>

    <input type='submit' name='submitUpload' value='Upload' />

</form>

</body></html>