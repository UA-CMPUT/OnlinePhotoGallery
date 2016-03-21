<?php

    function img_resize($target, $newcopy, $w, $h) {
        list($w_orig, $h_orig) = getimagesize($target);
        $scale_ratio = $w_orig / $h_orig;
        if (($w / $h) > $scale_ratio) {
            $w = $h * $scale_ratio;
        } else {
            $h = $w / $scale_ratio;
        }
        $img = imagecreatefromjpeg($target);
        $tci = imagecreatetruecolor($w, $h);
        imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
        imagejpeg($tci, $newcopy, 80);
    }

    include("connDB.php");
    $conn = connect();

    $sql_date_format = "alter session set nls_date_format = 'dd/mm/yyyy hh24:mi:ss'";
    $stid_date_format = oci_parse( $conn, $sql_date_format );
    $result_date_format = oci_execute( $stid_date_format );
    oci_free_statement($stid_date_format);

    $object = file_get_contents($_FILES['file']['tmp_name']);
    $now_date = $_POST['date-input'];
    if ($now_date ==''){
        $now_date = date("d/m/Y H:i:s");
    }

    $sql_id = "select photo_id from images";
    $id_stid = oci_parse($conn, $sql_id);
    $id_result = oci_execute($id_stid);
    $all_object_id = array();
    while ($object_id = oci_fetch_array($id_stid, OCI_ASSOC)) {
            array_push($all_object_id, $object_id["PHOTO_ID"]);
    }
    $id_guess = 1;
    while (true) {
        if (in_array($id_guess, $all_object_id)) {
            $id_guess++;
        } else {
            break;
        };
    }
    $fileName = $_FILES["file"]["name"];
    $fileTmpLoc = $_FILES["file"]["tmp_name"];
    $moveResult = move_uploaded_file($fileTmpLoc, "/tmp/" . $fileName);
    $target_file = "/tmp/" . $fileName;
    $resized_file = "/tmp/resize_" . $fileName;
    $wmax = 160;
    $hmax = 100;
    $ext_arr = explode(".", $fileName);
    $fileExt = end($ext_arr);
    img_resize($target_file, $resized_file, $wmax, $hmax);
    $thumb_img = file_get_contents($resized_file);
    $owner_name = $_SESSION['USER_NAME'];
    $permitted_id = $_POST['group-name'];
    $subject = $_POST['title'];
    if($subject == ''){
        $subject = $_FILES["file"]['name'];
    }
    $description = $_POST['description'];
    $place = $_POST['place'];
    $sql = "INSERT INTO images (photo_id, owner_name, permitted, subject, place, timing, description, thumbnail, photo) VALUES('" . $id_guess . "', '" . $owner_name . "', '" . $permitted_id . "', '" . $subject ."', '". $place ."', '". $now_date."', '". $description."', empty_blob(), empty_blob() ) RETURNING thumbnail, photo INTO :thumb_img, :object";

    $result = oci_parse($conn, $sql);
    $blob1 = oci_new_descriptor($conn, OCI_D_LOB);
    oci_bind_by_name($result, ":thumb_img", $blob1, -1, OCI_B_BLOB);
    $blob2 = oci_new_descriptor($conn, OCI_D_LOB);
    oci_bind_by_name($result, ":object", $blob2, -1, OCI_B_BLOB);
    oci_execute($result, OCI_DEFAULT) or die ("Unable to execute query");
    if (!$blob2->save($object)) {
        oci_rollback($conn);
        $blob2->free();
        header("location: upload_file.php?ACK=-1");
        exit();
    }
    if (!$blob1->save($thumb_img)) {
        oci_rollback($conn);
        $blob1->free();
        header("location: upload_file.php?ACK=-1");
        exit();
    }
    oci_free_statement($result);
    unlink($target_file);
    unlink($resized_file);

    oci_commit($conn);
    oci_close($conn);
    header( "location: upload_file.php?ACK=1");
?>
