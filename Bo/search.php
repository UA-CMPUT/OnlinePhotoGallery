<html>
<body>
	<h1>Search Page</h1>
	<p>Search Conditions:</p>
	<div id="search_panel">
		<form action="" method="post">
			<fieldset>
				Key Words: <input type="text" name="description"> <br />
				<br/> Date Range: <br>
				From: <input type="date" name="from_date" ><br/>
				To:   <input type="date" name="to_date"><br/>
				Result rank by: <select name="type" Method="">Rank Method</option>
				<?php
				   echo "<option value=>Default</option>";
					echo "<option value=f>most-recent-first</option>"; 
					echo "<option value=l>most-recent-last</option>"; 
				?>
				</select><br />
				<input type="submit" name="submit_search" value="Search">
			</fieldset>
		</form>
	</div>
	<?php
	      search($conn);
	?>
</body>

</html>


<?php
//this is the function to search the photo
function search($conn) {
	include("connDB.php");
	session_start();
	if(!empty($_POST) && isset($_POST['submit_search'])) {
    // The user submitted search query, get the conditions
    $keywords = $_POST['description'];
    $from = $_POST['from_date'];
    $from = str_replace('-', '/', $from);
    $to = $_POST['to_date'];
    $to = str_replace('-', '/', $to);
    $searchType = $_POST['type'];
    $user = $_SESSION['USER_NAME'];
    
    
    //create index for image table
    $conn=connect();
    $stid = oci_parse($conn, 'drop INDEX descIndex');
    oci_execute($stid, OCI_NO_AUTO_COMMIT);
    $stid = oci_parse($conn, 'drop INDEX subjIndex');
    oci_execute($stid, OCI_NO_AUTO_COMMIT);
    $stid = oci_parse($conn, 'drop INDEX placeIndex');
    oci_execute($stid, OCI_NO_AUTO_COMMIT);
    $stid = oci_parse($conn, 'CREATE INDEX descIndex ON images(description) INDEXTYPE IS CTXSYS.CONTEXT');
    oci_execute($stid, OCI_NO_AUTO_COMMIT);
    $stid = oci_parse($conn, 'CREATE INDEX subjIndex ON images(subject) INDEXTYPE IS CTXSYS.CONTEXT');
    oci_execute($stid, OCI_NO_AUTO_COMMIT);
    $stid = oci_parse($conn, 'CREATE INDEX placeIndex ON images(place) INDEXTYPE IS CTXSYS.CONTEXT');
    oci_execute($stid);
    
    //if there are keywords 
    if ($keywords != '') {
  
        $key_array = explode(' ', $keywords);
        
        $contains = '%'.$key_array[0].'%';
        
        foreach ($key_array as $key) {
            if ($key_array[0] != $key) {
                $contains = $contains.' | %'.$key.'%';
            }
        }

         // Create the query based on keywords, and permission
        $sql = 'SELECT photo_id, thumbnail, ((SCORE(1) * 6) + (SCORE(2) * 3) + SCORE(3)) score FROM images WHERE (CONTAINS (subject, \''.$contains.'\', 1) > 0 OR CONTAINS (place, \''.$contains.'\', 2) > 0 OR CONTAINS (description, \''.$contains.'\', 3) > 0)';
        
        $sql = $sql . ' and (owner_name = \''.$user.'\' or \''.$user.'\' = \'admin\' or permitted = 1 or permitted in (SELECT group_id FROM group_lists WHERE friend_id = \''.$user.'\') or \''.$user.'\' in (SELECT user_name FROM groups WHERE group_id = permitted))';
        
        if ($from != '') {
            $sql = $sql . ' and timing >= TO_DATE(\''.$from.'\', \'yyyy/mm/dd\')';
        }
        if ($to != '') {
            $sql = $sql . ' and timing <= TO_DATE(\''.$to.'\', \'yyyy/mm/dd\')';
        }
        
        if ($searchType == 'f') {
            $sql = $sql . ' ORDER BY timing DESC';
        }
        else if ($searchType == 'l') {
            $sql = $sql . ' ORDER BY timing';
        }
        else {
            $sql = $sql . ' ORDER BY score DESC';
        }
    }
    else {
        // Else there are no keywords, so create the query only based on time
        $sql = 'SELECT photo_id, thumbnail FROM images';
        
        $sql = $sql . ' WHERE (owner_name = \''.$user.'\' or \''.$user.'\' = \'admin\' or permitted = 1 or permitted in (SELECT group_id FROM group_lists WHERE friend_id = \''.$user.'\') or \''.$user.'\' in (SELECT user_name FROM groups WHERE group_id = permitted))';
        
        if ($from != '') {
            $sql = $sql . ' and timing >= TO_DATE(\''.$from.'\', \'yyyy/mm/dd\')';
        }
        if ($to != '') {
            $sql = $sql . ' and timing <= TO_DATE(\''.$to.'\', \'yyyy/mm/dd\')';
        }  
        
        if ($searchType == 'f') {
            $sql = $sql . ' ORDER BY timing DESC';
        }
        else if ($searchType == 'l') {
            $sql = $sql . ' ORDER BY timing';
        }
    }
    
    //echo $sql."<br>";
   
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    
    
     echo "Search Result: <br>";
     while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        // Loop through each search result
        $id = $row['PHOTO_ID'];
        //show each photo
         echo "<a target='_parent' href='show_image.php?id=".$id."'><img src='imageView.php?image_id=".$id."&original=0'/></a>";       
    }
 
    //oci_free_statement($stid);
    //oci_close($conn);
    
    //and condition
    //$conn=connect();
    /*if ($keywords != '') {
        // If there are keywords
        $key_array = explode(' ', $keywords);
        
        $contains = '%'.$key_array[0].'%';
        
        foreach ($key_array as $key) {
            if ($key_array[0] != $key) {
                $contains = $contains.' and %'.$key.'%';
            }
        }
        echo $contains."<br>";
         // Construct the query based on keywords, and the other search criteria entered
        $sql = 'SELECT photo_id, thumbnail, ((SCORE(1) * 6) + (SCORE(2) * 3) + SCORE(3)) score FROM images WHERE CONTAINS (subject, \''.$contains.'\', 1) > 0 OR CONTAINS (place, \''.$contains.'\', 2) > 0 OR CONTAINS (description, \''.$contains.'\', 3) > 0';
        
        $sql = $sql . ' and (owner_name = \''.$user.'\' or \''.$user.'\' = \'admin\' or permitted = 1 or permitted in (SELECT group_id FROM group_lists WHERE friend_id = \''.$user.'\') or \''.$user.'\' in (SELECT user_name FROM groups WHERE group_id = permitted))';
        
        if ($from != '') {
            $sql = $sql . ' and timing >= TO_DATE(\''.$from.'\', \'yyyy/mm/dd\')';
        }
        if ($to != '') {
            $sql = $sql . ' and timing <= TO_DATE(\''.$to.'\', \'yyyy/mm/dd\')';
        }
        
        if ($searchType == 'f') {
            $sql = $sql . ' ORDER BY timing DESC';
        }
        else if ($searchType == 'l') {
            $sql = $sql . ' ORDER BY timing';
        }
        else {
            $sql = $sql . ' ORDER BY score DESC';
        }
    }
    else {
        // Else there are no keywords, so construct the query only based on time
        $sql = 'SELECT photo_id, thumbnail FROM images';
        
        $sql = $sql . ' WHERE (owner_name = \''.$user.'\' or \''.$user.'\' = \'admin\' or permitted = 1 or permitted in (SELECT group_id FROM group_lists WHERE friend_id = \''.$user.'\') or \''.$user.'\' in (SELECT user_name FROM groups WHERE group_id = permitted))';
        
        if ($from != '') {
            $sql = $sql . ' and timing >= TO_DATE(\''.$from.'\', \'yyyy/mm/dd\')';
        }
        if ($to != '') {
            $sql = $sql . ' and timing <= TO_DATE(\''.$to.'\', \'yyyy/mm/dd\')';
        }  
        
        if ($searchType == 'f') {
            $sql = $sql . ' ORDER BY timing DESC';
        }
        else if ($searchType == 'l') {
            $sql = $sql . ' ORDER BY timing';
        }
    }
    
    echo $sql."<br>";
   
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    
    
     echo "Search Result for and condition: <br>";
     while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        // Loop through each search result
        $id = $row['PHOTO_ID'];
        echo $id."<br>";
         echo '<img src="imageView.php?image_id='.$id.'&original=0"/><br>';       
    }*/
 
    oci_free_statement($stid);
    oci_close($conn);
    
}
}








?>
