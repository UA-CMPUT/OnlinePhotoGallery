<?php
/*
 * CMPUT 391 Project Group 6
 * Written by Glenn Meyer and Dylan Stankievech
 * November 26, 2014 * 
 * 
 */
include_once ("helper.php");

$message = "";
$images = "";
$php_self = $_SERVER['PHP_SELF'];

session_start();
if (!$_SESSION['username']) {
    // If user hasn't started a session, redirect to login page
    redirect('login.php');
}

// Get username of current user from session
$user = $_SESSION['username'];

if(!empty($_POST) && isset($_POST['submitSearch'])) {
    // The user submitted search query, get the conditions
    $keywords = $_POST['keywords'];
    $after = $_POST['after'];
    $after = str_replace('-', '/', $after);
    $before = $_POST['before'];
    $before = str_replace('-', '/', $before);
    $searchType = $_POST['searchType'];
    
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
        
        if ($searchType == 'newest') {
            $sql = $sql . ' ORDER BY timing DESC';
        }
        else if ($searchType == 'oldest') {
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
        
        if ($searchType == 'newest') {
            $sql = $sql . ' ORDER BY timing DESC';
        }
        else if ($searchType == 'oldest') {
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
else {
    $conn=connect();
    
    //Awesome sql query to find 5 most popular images that current user can view
    $sql = 'SELECT i.photo_id, i.thumbnail FROM images i
            JOIN total_views v ON v.photo_id = i.photo_id
            WHERE v.total IN (
                SELECT t.total FROM total_views t
                join images p on p.photo_id = t.photo_id 
                WHERE ROWNUM < 6
                and (p.owner_name = \''.$user.'\' or \''.$user.'\'=\'admin\' or p.permitted = 1 or
                p.permitted in (SELECT group_id FROM group_lists WHERE friend_id = \''.$user.'\')
                or \''.$user.'\' in (SELECT user_name FROM groups WHERE group_id = p.permitted))
            )
            and (i.owner_name = \''.$user.'\' or \''.$user.'\'=\'admin\' or i.permitted = 1 or
            i.permitted in (SELECT group_id FROM group_lists WHERE friend_id = \''.$user.'\')
            or \''.$user.'\' in (SELECT user_name FROM groups WHERE group_id = i.permitted))
            ORDER BY v.total desc';
    
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    
    $images = '<br><tr><td>Popular Images: </td></tr>';
    while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        // Loop through all popular images
        $id = $row['PHOTO_ID'];
        $data = $row['THUMBNAIL']->load();
        
        // Append its thumbnail as clickable image to display.php
        $images .= '<tr><td><a href=display.php?photo_id=' . $id . '><img src="data:image/jpeg;base64,'.base64_encode( $data ).'"/></a></td></tr>';            
    }
    
    oci_close($conn);
}
?>
<html>
<head>
<title>Search</title>
</head>

<body>
<form name="HomeForm" action="home.php" method="get" >

<input type="submit" value="Home">

</form>
<h1><center>Search</center></h1>

    <?php 
        echo 'Hello ' . $_SESSION['username'];
    ?>
    
</p>
<p><?php echo $message ?></p>

<form name="SearchForm" action="<?php echo $php_self?>" method="post" >
<table
    
<tr valign=top align=left>
<td><b><i>Search method:</i></b></td>
<td>
<input type="radio" name="searchType" value="newest">Newest<br>
<input type="radio" name="searchType" value="oldest">Oldest<br>
</td>
</tr>

<tr valign=top align=left>
<td><b><i>Keywords:</i></b></td>
<td><input type="text" name="keywords" value="" autofocus ><br></td>
</tr>

<tr valign=top align=left>
<td><b><i>Start Date:</i></b></td>
<td><input type="date" name="after"><br></td>
</tr>

<tr valign=top align=left>
<td><b><i>End Date:</i></b></td>
<td><input type="date" name="before"><br></td>
</tr>

<tr valign=top align=left>
    <td><input type="submit" name="submitSearch" value="Search"></td>
</tr>
</table>

<?php
// Will output the clickable thumbnails of popular images, or search result
echo '<table>';
echo $images;
echo '</table>';
?>

</table>
</form>

</body>
</html>