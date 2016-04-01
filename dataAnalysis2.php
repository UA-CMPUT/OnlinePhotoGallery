<?php


include("connDB.php");
session_start();

if ( !isset ( $_SESSION['USER_NAME'] ) ) {
    exit();
};

$user=$_SESSION["USER_NAME"];
echo $user;
//Only administrater has right to do the dataAnalysis

if(isset($_POST['upload_analysis'])) {
    //admin has specify the analsis condition and submit
    $keywords = $_POST['keywords']; $keyList = explode(' ', $keywords);
    $users = $_POST['users']; $userList = explode(' ', $users);
    $startDate = $_POST['start']; $startDate = str_replace('-', '/', $startDate);
    $endDate = $_POST['end']; $endDate = str_replace('-', '/', $endDate);
    $showYearly=$_POST['showYearly'];
    $showMonthly=$_POST['showMonthly'];
    $showWeekly=$_POST['showWeekly'];
    $showUsers=$_POST['showUsers'];
    $showSubjects= $_POST['showSubjects'];

    //===================Form the select clause which specify by the user===================

    $query='SELECT';

    $identifier=0;

    $columns = '<tr><td><b>';

    if($showUsers){
        $identifier=1;
        $query .= ' owner_name';
        $columns .= 'Users';
    }

    if($showSubjects) {
        if($identifier==0) {

            $identifier=1;
            $query .= ' subject';
            $columns .= 'Subjects';
        } else {

            $query .= ', subject';
            $columns .= '</b></td><td><b>Subject';
        }
    }

    if($showYearly) {
        if($identifier==0) {
            $identifier=1;
            $query .= ' EXTRACT(YEAR FROM timing) year';
            $columns .= 'Year';
        }else {

            $query .= ', EXTRACT(YEAR FROM timing) year';
            $columns .= '</b></td><td><b>Year';


        }
    }

    if($showMonthly) {
        if($identifier==0) {
            $identifier=1;
            $query .= ' EXTRACT(MONTH FROM timing) month';

            $columns .= 'Month';

        }else {
            $query .= ', EXTRACT(MONTH FROM timing) month';
            $columns .= '</b></td><td><b>Month';
        }

    }


    if($showWeekly) {
        if($identifier==0) {
            $identifier=1;
            $query .= ' TO_CHAR(timing,\'WW\') week';
            $columns .= 'Week';

        }else {
            $query .= ', TO_CHAR(timing,\'WW\') week';
            $columns .= '</b></td><td><b>Week';
        }

    }

    if($identifier==0) {
        $query .= ' COUNT(*) count FROM images';

        $columns .= 'Total';

    }else {
        $query .= ', COUNT(*) count FROM images';
        $columns .= '</b></td><td><b>Total';

    }

    $columns .= '</b></td></tr>';


    //===============Form the where clause which specify by the user=======================
    $identifier=0;


    //Allow admin choose to display the number of images for each user.
    if ($users != '') {
        // Create variable $nameList to genrate all users
        $nameList = '\''. $userList[0].'\'';


        foreach ( $userList as $owner) {
            if ( $userList[0] != $owner) {
                $nameList = $nameList.', \''.$owner.'\'';

            }
        }

        $query .= ' WHERE owner_name in ('.$nameList.')';
        $identifier=1;
    }

    //Allow admin choose to display the number of images for keywords.
    if ($keywords != '') {
        $contains = '%'.$keyList[0].'%';

        foreach ($keyList as $key) {
            if ($keyList[0] != $key) {
                $contains = $contains.' | %'.$key.'%';
            }
        }

        if ($identifier == 0) {
            $query .= ' WHERE CONTAINS (subject, \''.$contains.'\', 1) > 0';
            $identifier = 1;
        }
        else {
            $query .= ' AND CONTAINS (subject, \''.$contains.'\', 1) > 0';
        }
    }


    if($startDate != '') {
        if($identifier == 0) {
            $query .= ' WHERE timing >= TO_DATE(\''.$startDate.'\', \'yyyy/mm/dd\')';
            $identifier=1;

        }else {
            $query .= ' AND timing >= TO_DATE(\''.$startDate.'\', \'yyyy/mm/dd\')';
        }

    }

    if($endDate != '') {
        if($identifier == 0) {

            $query .=  ' WHERE timing <= TO_DATE(\''.$endDate.'\', \'yyyy/mm/dd\')';
            $identifier=1;


        }else {
            $query .= ' AND timing <= TO_DATE(\''.$endDate.'\', \'yyyy/mm/dd\')';

        }
    }



    //===============Form the group by clause which specify by the user=======================
    $identifier=0;

    if($showUsers) {
        $identifier=1;
        $query .= ' GROUP BY owner_name';

    }

    if($showSubjects) {
        if($identifier==0) {

            $query .= ' GROUP BY subject';
            $identifier=1;
        }else {

            $query .= ', subject';

        }
    }

    if($showYearly) {
        if($identifier==0) {

            $query .= ' GROUP BY EXTRACT(YEAR FROM timing)';
            $identifier=1;
        }else {
            $query .= ', EXTRACT(YEAR FROM timing)';

        }
    }



    if($showMonthly) {
        if($identifier==0) {

            $query .= ' GROUP BY EXTRACT(MONTH FROM timing)';
            $identifier=1;
        }else {
            $query .= ', EXTRACT(MONTH FROM timing)';

        }
    }



    if($showWeekly) {
        if($identifier==0) {

            $query .= ' GROUP BY TO_CHAR(timing,\'WW\')';
            $identifier=1;
        }else {
            $query .= ', TO_CHAR(timing,\'WW\')';

        }
    }


    // sort the image 
    //might can delete===========================
    $query .= ' ORDER BY count DESC';

    $conn=connect();

    if (!$conn) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }


    //Prepare sql using conn and returns the statement identifier
    $stid = oci_parse($conn, $query);
    oci_execute($stid);


    // Generate table from result set to be displayed
    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        foreach ($row as $item) {
            if ($row[0] == $item){
                $results .= '<tr><td>'.$item;
            }
            else {
                $results .= '</td><td>'.$item;
            }
        }
        $results .= '</td></tr>';
    }

    oci_free_statement($stid);
    oci_close($conn);


}





?>
<html>
<body>
<h1>Data Analysis</h1>

<div id="dataAnalysis_button"> Analysis Data </div>
<div id="data_analysis_panel">
    <form action="dataAnalysis.php" method="post">
        <fieldset>
            user:	<input type="text" name="users" placeholder="Please enter user id"> <br /> <br />
            key Words:	<input type="text" name="keywords" placeholder="key words for subjects"> <br /> <br />
            From:	<input type="text" name="start" placeholder="Please enter start date"> Format: yyyy-mm-dd<br /> <br />
            To:	<input type="text" name="end" placeholder="Please enter end date"> Format: yyyy-mm-dd<br /> <br />
            <button type="reset">Reset</button>
            <input type="submit" name="upload_analysis" value="Submit">


            <p>Check the information you want to show.</p>
            <form name="DataForm" action="<?php echo $php_self?>" method="post" >
                <input  type = "checkbox" name = "showUsers" value = "Users">Users
                <input  type = "checkbox" name = "showSubjects" value = "subjects">subjects


                <input type = "checkbox" name = "showYearly" value = "yearly">Yearly
                <input type = "checkbox" name = "showMonthly" value = "monthly">Monthly
                <input  type = "checkbox" name = "showWeekly" value = "weekly">Weekly

        </fieldset>


</div>

</tr>
</table>


<?php
if (isset($_POST['upload_analysis'])) {
    if ($results) {
        echo '<table border="2">';
        echo '<h3>Analysis Results:</h3>';
        echo $columns;
        echo $results;
        echo '</table>';
    }else {
        echo '<b><i>No results found</i></b>';
    }
}
?>

</table>
</body>
</html>


