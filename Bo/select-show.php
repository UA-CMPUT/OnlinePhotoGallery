<?php
    include("connDB.php");
    session_start();
    if ( !isset ( $_SESSION['USER_NAME'] ) ) {
        header( "location:index.php?ERR=session" );
        exit();
    };
    $user_name = $_SESSION['USER_NAME'];
    $conn = connect();
    $q=$_GET["q"];
    if ($q == -1){
        echo "";
        exit();
    }
    $sql_user_list = "SELECT user_name FROM users WHERE user_name<>'admin' AND user_name<>'".$user_name."'";
    $sql_friend_list = "SELECT friend_id, notice FROM group_lists WHERE group_id ='".$q."'";
    $sql_group_name = "SELECT group_name FROM groups WHERE group_id = '".$q."'";
    $stid_user_list = oci_parse($conn, $sql_user_list);
    $stid_friend_list = oci_parse($conn, $sql_friend_list);
    $stid_group_name = oci_parse($conn, $sql_group_name);
    $result_user_list = oci_execute($stid_user_list);
    $result_friend_list = oci_execute($stid_friend_list);
    $result_group_name = oci_execute($stid_group_name);
    $all_user = array();
    $all_friend = array();
    $all_friend_name = array();
    if ($result_friend_list && $result_group_name && $result_user_list){
//        while($friend = oci_fetch_array($stid_friend_list,OCI_ASSOC)){
//            array_push($all_friend, $friend["FRIEND_ID"]);
//        }
        while ($friend = oci_fetch_row($stid_friend_list)){
            array_push($all_friend, $friend);
            array_push($all_friend_name, $friend[0]);
        }
        while($user = oci_fetch_array($stid_user_list,OCI_ASSOC)){
            if (!(in_array($user["USER_NAME"],$all_friend_name))){
                array_push($all_user, $user["USER_NAME"]);
            }
        }
        oci_free_statement($stid_user_list);
        oci_free_statement($stid_friend_list);
        $group_name = oci_fetch_array($stid_group_name,OCI_ASSOC);
        oci_free_statement($stid_group_name);
        /* show add form */
        echo "<div style='height: 200px'><form name=\"add-to-friend\" method=\"post\" action='add-friend.php?ID=".$q."' enctype=\"multipart/form-data\">";
        echo "<div class='allfull' style='...'>";
        echo "<div class='half'><strong>Not in the Group: ". $group_name["GROUP_NAME"] ."</strong><br>";
        echo "<select class='full' multiple name='unfriend-list[]' style='height: 60%'>";
        foreach($all_user as $person) {
            echo "<option value='" . $person . "'>" . $person;
        }
        echo "</select></div>";
        echo "<div class='half'><strong>Input Notice(Optional)</strong><br>
            <textarea class='' name=\"notice\" placeholder=\"Enter notice here...\" style='width: 70%; height: 60%'></textarea></div></div><br>";
        echo "<div class='full'><button type=\"submit\" class=\"btn\" name=\"add-friend-button\">Add to friends</button></div>";
        echo "</form>";
        echo "</div>";
        echo "<div class='allfull' style='margin-top: 20px' ><hr /></div>";
        /* show delete form */
        echo "<div class='allhalf' style='margin-top: 10px'>";
        echo "<form name=\"delete-from-friend\" method=\"post\" action='delete-friend.php?ID=".$q."' enctype=\"multipart/form-data\">";
        echo "<strong>Your Friends in Group: ".$group_name["GROUP_NAME"]."</strong><br>";
        echo "<select multiple class='full' name='friend-list[]' style='height: 90px'>";
        foreach($all_friend as $person) {
            echo "<option value='" . $person[0] . "'>" . $person[0]." : ".$person[1];
        }
        echo "</select><br>";
        echo "<button type=\"submit\" class=\"btn\" name=\"delete-friend-button\" style='margin-top: 10px'>Delete from friends</button><br>";
        echo "</form>";
        echo "</div>";
    }else{
        echo "<div id='success-show' style='color:#FF0000'>Cannot connect to Oracle server. Please try again later.</div>";
    }
    oci_close($conn);
?>
