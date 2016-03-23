<html>
<body>
	<h1>Search Page</h1>
	<p>Search Conditions:</p>
	<div id="search_panel">
		<form action="" method="post">
			<fieldset>
				Key Words: <input type="text" name="description"> <br />
				<br/> Date Range: <br>
				From: <input id="from_date" type="date" value=""><br/>
				To:   <input id="to_date" type="date" value=""><br/>
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
	<?php
// Will output the clickable thumbnails of popular images, or search result
echo '<table>';
echo $images;
echo '</table>';
?>
</body>

</html>

<?php
function search($conn) {
	include("connDB.php");
	$images="";
	
	
	if(!empty($_POST) && isset($_POST['submit_search'])) {
    // The user submitted search query, get the conditions
    $keywords = $_POST['text'];
    $from = $_POST['from_date'];
    $from = str_replace('-', '/', $from);
    $to = $_POST['to_date'];
    $to = str_replace('-', '/', $to);
    $searchType = $_POST['type'];
    $user = 'bq';
    
    $conn=connect();
    
    if ($keywords != '') {
        // If there are keywords
        $key_array = explode(' ', $keywords);
        
        $contains = '%'.$key_array[0].'%';
        
        foreach ($key_array as $key) {
            if ($key_array[0] != $key) {
                $contains = $contains.' | %'.$key.'%';
            }
        }
        
        // Construct the query based on keywords, and the other search criteria entered
        $sql = 'SELECT photo_id, thumbnail, ((SCORE(1) * 6) + (SCORE(2) * 3) + SCORE(3)) score FROM images WHERE CONTAINS (subject, \''.$contains.'\', 1) > 0 OR CONTAINS (place, \''.$contains.'\', 2) > 0 OR CONTAINS (description, \''.$contains.'\', 3) > 0';
        
        $sql = $sql . ' and (owner_name = \''.$user.'\' or \''.$user.'\' = \'admin\' or permitted = 1 or permitted in (SELECT group_id FROM group_lists WHERE friend_id = \''.$user.'\') or \''.$user.'\' in (SELECT user_name FROM groups WHERE group_id = permitted))';
        
        if ($after != '') {
            $sql = $sql . ' and timing >= TO_DATE(\''.$after.'\', \'yyyy/mm/dd\')';
        }
        else if ($before != '') {
            $sql = $sql . ' and timing <= TO_DATE(\''.$before.'\', \'yyyy/mm/dd\')';
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
        
        if ($after != '') {
            $sql = $sql . ' and timing >= TO_DATE(\''.$after.'\', \'yyyy/mm/dd\')';
        }
        else if ($before != '') {
            $sql = $sql . ' and timing <= TO_DATE(\''.$before.'\', \'yyyy/mm/dd\')';
        }  
        
        if ($searchType == 'f') {
            $sql = $sql . ' ORDER BY timing DESC';
        }
        else if ($searchType == 'l') {
            $sql = $sql . ' ORDER BY timing';
        }
    }
    
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    
    $images = '<br><tr><td>Search Results: </td></tr>';
    while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        // Loop through each search result
        $id = $row['PHOTO_ID'];
        $data = $row['THUMBNAIL']->load();
        
        // Append a clickable thumbnail that links to display.php
        $images .= '<tr><td><a href=display.php?photo_id=' . $id . '><img src="data:image/jpeg;base64,'.base64_encode( $data ).'"/></a></td></tr>';            
    }
    
    oci_free_statement($stid);
    oci_close($conn);
}


}








?>
