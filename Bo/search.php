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
function search($conn) {
	include("connDB.php");
	echo "function called";
	

	if(!empty($_POST) && isset($_POST['submit_search'])) {
    // The user submitted search query, get the conditions
    $keywords = $_POST['description'];
    $from = $_POST['from_date'];
    $from = str_replace('-', '/', $from);
    $to = $_POST['to_date'];
    $to = str_replace('-', '/', $to);
    $searchType = $_POST['type'];
    $user = 'Aa';
    
    $conn=connect();
    echo "1";
    if ($keywords != '') {
    	echo"here second";
        // If there are keywords
        $key_array = explode(' ', $keywords);
        
        $contains = '%'.$key_array[0].'%';
        
        foreach ($key_array as $key) {
            if ($key_array[0] != $key) {
                $contains = $contains.' | %'.$key.'%';
            }
        }
        echo $keywords;
        
        // Construct the query based on keywords, and the other search criteria entered
        $sql = "SELECT photo_id, thumbnail FROM images where owner_name = '".$user."' and (place like '".$contains."' or subject like'".$contains."' or description like'" .$contains."')";
        
        if ($from != '') {
            $sql = $sql . " and timing >= TO_DATE('".$from."', 'yyyy/mm/dd')";
            }
       
        else if ($to != '') {
            $sql = $sql . " and timing >= TO_DATE('".$to."', 'yyyy/mm/dd')";
        }
        }
   
        /*
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
    /*else {
    	  echo "here third";
        // Else there are no keywords, so construct the query only based on time
        $sql = 'SELECT photo_id, thumbnail FROM images';
        
        $sql = $sql . ' WHERE (owner_name = \''.$user.'\' or \''.$user.'\' = \'admin\' or permitted = 1 or permitted in (SELECT group_id FROM group_lists WHERE friend_id = \''.$user.'\') or \''.$user.'\' in (SELECT user_name FROM groups WHERE group_id = permitted))';
        
        if ($from != '') {
            $sql = $sql . ' and timing >= TO_DATE(\''.$from.'\', \'yyyy/mm/dd\')';
        }
        else if ($to != '') {
            $sql = $sql . ' and timing <= TO_DATE(\''.$to.'\', \'yyyy/mm/dd\')';
        }  
        
        if ($searchType == 'f') {
            $sql = $sql . ' ORDER BY timing DESC';
        }
        else if ($searchType == 'l') {
            $sql = $sql . ' ORDER BY timing';
        }
    }*/
    echo $sql;
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    
     while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        // Loop through each search result
        $id = $row['PHOTO_ID'];
        echo $id;        
    }
    
    oci_free_statement($stid);
    oci_close($conn);
}

}








?>
