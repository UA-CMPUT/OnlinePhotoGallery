<?php


include("connDB.php");
session_start();

if ( !isset ( $_SESSION['USER_NAME'] ) ) {
  	 exit();
};

$user=$_SESSION["USER_NAME"];
//echo $user;
//Only administrater has right to do the dataAnalysis

if(isset($_POST['upload_analysis'])) {
	//Post the method which checked by admin through check boxes
	$keywords = $_POST['keywords']; $keyList = explode(' ', $keywords);
	$users = $_POST['users']; $userList = explode(' ', $users);
	$startDate = $_POST['start']; $startDate = str_replace('-', '/', $startDate);
	$endDate = $_POST['end']; $endDate = str_replace('-', '/', $endDate);
    $start_array = explode('/',$startDate);
    $end_array = explode('/', $endDate);
    $start_year = $start_array[0];
    $start_month = $start_array[1];
    $end_year = $end_array[0];
    if (intval($end_array[1]) < 12){
        $end_month = strval(intval($end_array[1])+1);
        $end_year2 = $end_year;
    }else{
        $end_month = 1;
        $end_year2 = strval(intval($end_year)+1);
    }
//    echo $end_month."<br>";

	$showYearly=$_POST['showYearly'];
	$showMonthly=$_POST['showMonthly'];
	$showWeekly=$_POST['showWeekly'];
	$showUsers=$_POST['showUsers'];
	$showSubjects= $_POST['showSubjects'];
	
	//===================Form the select clause which specify by the user===================
    $conn=connect();

    if ($startDate != ''){
        $sql_get_weekday1 = "select to_char(to_date('".$startDate."', 'yyyy/mm/dd'), 'd') as d FROM users where user_name = 'admin'";
        $sql_get_weekday2 = "select to_char(to_date('".$endDate."', 'yyyy/mm/dd'), 'd') as d FROM users where user_name = 'admin'";
        $stid_get_weekday1 = oci_parse($conn, $sql_get_weekday1);
        $stid_get_weekday2 = oci_parse($conn, $sql_get_weekday2);
        $result_get_weekday1 = oci_execute($stid_get_weekday1);
        $result_get_weekday2 = oci_execute($stid_get_weekday2);
        $gap1 = oci_fetch_array($stid_get_weekday1,OCI_ASSOC);
        $gap2 = oci_fetch_array($stid_get_weekday2,OCI_ASSOC);
        $delay1 = intval($gap1["D"]) - 1;
        $delay2 = 7 - intval($gap2["D"]);
        oci_free_statement($stid_get_weekday1);
        oci_free_statement($stid_get_weekday2);
    }
//    echo "gap1 is".$delay1."<br>";
//    echo "gap2 is".$delay2."<br>";
//    echo $gap1['D']."<br>";
//    echo $gap2['D']."<br>";




    $query ='SELECT';
	
	$identifier=0;
	
	$columns = '<tr><td><b>';
	
	
	
	
	//Use if statement to check does admin select the user option
	if($showUsers){
		$identifier=1;
		$query .= ' owner_name';
		$columns .= 'Users';
	}
	
	
	//Use if statement to check does admin select the subject option
	if($showSubjects) {
		
		
		//Check does user select other option before this one
		// If we have other option, add comma before the sql statement
		if($identifier==0) {
		
			$identifier=1;
			$query .= ' subject';
			$columns .= 'Subjects';
		} else {
			
			$query .= ', subject';
			$columns .= '</b></td><td><b>Subject';
		}
	}
	
	
	
	//Use if statement to check does admin select the Yearly option
	if($showYearly) {
		
		//Check does user select other option before this one
		// If we have other option, add comma before the sql statement
		if($identifier==0) {
			$identifier=1;
			$query .= ' EXTRACT(YEAR FROM timing) year';
			$columns .= 'Year';
		}else {
			
			$query .= ', EXTRACT(YEAR FROM timing) year';
			$columns .= '</b></td><td><b>Year';
		
		
		}	
	}
	
	//Use if statement to check does admin select the Monthly option
	if($showMonthly) {
		
		//Check does user select other option before this one
		// If we have other option, add comma before the sql statement
		if($identifier==0) {
			$identifier=1;
			$query .= ' EXTRACT(MONTH FROM timing) month';
			
			$columns .= 'Month';
			
		}else {
			$query .= ', EXTRACT(MONTH FROM timing) month';
			$columns .= '</b></td><td><b>Month';
		}
		
	}
	
	//Use if statement to check does admin select the weekly option
	if($showWeekly) {
		
		
		//Check does user select other option before this one
		// If we have other option, add comma before the sql statement
		if($identifier==0) {
			$identifier=1;
			$query .= ' TO_CHAR(timing+1,\'IW\',\'NLS_DATE_LANGUAGE = American\') week';
			$columns .= 'Week';
			
		}else {
			$query .= ', TO_CHAR(timing+1,\'IW\',\'NLS_DATE_LANGUAGE = American\') week';
			$columns .= '</b></td><td><b>Week';
		}
		
	}
	
	
	//If admin select none of the option, we show the total number of images
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
    
    
    
    //Check does admin enter a specify start date
    if ($showWeekly){
        if($startDate != '') {
            if($identifier == 0) {
                $query .= ' WHERE timing >= TO_DATE(\''.$startDate.'\', \'yyyy/mm/dd\') - '.$delay1;
                $identifier=1;

            }else {
                $query .= ' AND timing >= TO_DATE(\''.$startDate.'\', \'yyyy/mm/dd\') - '.$delay1;
            }

        }
    }elseif ($showYearly){
        if($startDate != '') {
            if($identifier == 0) {
                $query .= ' WHERE timing >= TO_DATE(\''.$start_year."/01/01".'\', \'yyyy/mm/dd\')';
                $identifier=1;

            }else {
                $query .= ' AND timing >= TO_DATE(\''.$start_year."/01/01".'\', \'yyyy/mm/dd\')';
            }

        }
    }elseif ($showMonthly){
        if($startDate != '') {
            if($identifier == 0) {
                $query .= ' WHERE timing >= TO_DATE(\''.$start_year."/".$start_month."/01".'\', \'yyyy/mm/dd\')';
                $identifier=1;

            }else {
                $query .= ' AND timing >= TO_DATE(\''.$start_year."/".$start_month."/01".'\', \'yyyy/mm/dd\') ';
            }

        }
    }
    else {

        if ($startDate != '') {
            if ($identifier == 0) {
                $query .= ' WHERE timing >= TO_DATE(\'' . $startDate . '\', \'yyyy/mm/dd\')';
                $identifier = 1;

            } else {
                $query .= ' AND timing >= TO_DATE(\'' . $startDate . '\', \'yyyy/mm/dd\')';
            }

        }
    }
    
    
    //Check does admin enter a specify end date
    if ($showWeekly){
        if($endDate != '') {
            if($identifier == 0) {

                $query .=  ' WHERE timing <= TO_DATE(\''.$endDate.'\', \'yyyy/mm/dd\') +1 +'.$delay2;
                $identifier=1;


            }else {
                $query .= ' AND timing <= TO_DATE(\''.$endDate.'\', \'yyyy/mm/dd\') +1 +'.$delay2;

            }
        }
    }elseif ($showYearly){
        if($endDate != '') {
            if($identifier == 0) {

                $query .=  ' WHERE timing <= TO_DATE(\''.$end_year."/12/31".'\', \'yyyy/mm/dd\') + 1';
                $identifier=1;


            }else {
                $query .= ' AND timing <= TO_DATE(\''.$end_year."/12/31".'\', \'yyyy/mm/dd\') +1 ';

            }
        }
    }elseif ($showMonthly){
        if($endDate != '') {
            if($identifier == 0) {

                $query .=  ' WHERE timing <= TO_DATE(\''.$end_year2."/".$end_month."/01".'\', \'yyyy/mm/dd\')';
                $identifier=1;


            }else {
                $query .= ' AND timing <= TO_DATE(\''.$end_year2."/".$end_month."/01".'\', \'yyyy/mm/dd\') ';

            }
        }
    }

    else {
        if ($endDate != '') {
            if ($identifier == 0) {

                $query .= ' WHERE timing <= TO_DATE(\'' . $endDate . '\', \'yyyy/mm/dd\') +1';
                $identifier = 1;


            } else {
                $query .= ' AND timing <= TO_DATE(\'' . $endDate . '\', \'yyyy/mm/dd\') +1';

            }
        }
    }
    
    
    
    //===============Form the group by clause which specify by the user=======================
    $identifier=0;	
    
    
    //Use if statement to check does admin select the user option
    if($showUsers) {
		$identifier=1;    	
    	$query .= ' GROUP BY owner_name';
    	
    }
    
    
    //Use if statement to check does admin select the subject option
    if($showSubjects) {
    	
    	//Check does user select other option before this one
		// If we have other option, add comma before the sql statement
    	if($identifier==0) {
    		
    		$query .= ' GROUP BY subject';
			$identifier=1;
		}else {
			 
    		$query .= ', subject';
    		
    	}
    }
    
    
    
    //Use if statement to check does admin select the yearly option
    if($showYearly) {
    	
    	//Check does user select other option before this one
		// If we have other option, add comma before the sql statement
    	if($identifier==0) {
    		
    		$query .= ' GROUP BY EXTRACT(YEAR FROM timing)';
    		$identifier=1;
    	}else {
    		$query .= ', EXTRACT(YEAR FROM timing)';
    		
    	}
    }
    
    
    //Use if statement to check does admin select the monthly option
    if($showMonthly) {
    	
    	
    	//Check does user select other option before this one
		// If we have other option, add comma before the sql statement
    	if($identifier==0) {
    		
    		$query .= ' GROUP BY EXTRACT(MONTH FROM timing)';
    		$identifier=1;
    	}else {
    		$query .= ', EXTRACT(MONTH FROM timing)';
    		
    	}
    }
    
    
    //Use if statement to check does admin select the weekly option
    if($showWeekly) {
    	
    	//Check does user select other option before this one
		// If we have other option, add comma before the sql statement
    	if($identifier==0) {
    		
    		$query .= ' GROUP BY TO_CHAR(timing+1,\'IW\',\'NLS_DATE_LANGUAGE = American\')';
    		$identifier=1;
    	}else {
    		$query .= ', TO_CHAR(timing+1,\'IW\',\'NLS_DATE_LANGUAGE = American\')';
    		
    	}
    }
    
    
    
    $query .= ' ORDER BY count DESC';
//    echo $query."<br>";
    
    
    //===============Form the Connection here========================================

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


