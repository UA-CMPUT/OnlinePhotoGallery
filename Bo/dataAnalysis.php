<!DOCTYPE DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Data Analysis</title>
</head>


<?php
	include ("connDB.php");
	//establish connection
	$conn=connect();



//=================================================================================
//Specify table
       //Drop specify table
       echo "Drop the speify table <br>";
       $query = "DROP Table specify_table";
       $result =oci_parse($conn, $query);
       $res =oci_execute($result);
       if($res) {
       	oci_commit($conn);
       } else {
       		$error =oci_error($result);
       		echo "Data selecting inproperly". $e['message']. "<br>";
       }
       
       oci_free_statement($result);
       
       //We can create specify_table to increase efficency about OLAP queries for data_cube
       echo "Create specify_table <br>";
       
       
       //==============================
       //Might not working, need to know how does the database look like
       //Still need to figure out what is the sensor
       $query = "Create Table specify_table AS SELECT User.user_id, data".
                "FROM Users, data".
                "WHERE User.user_id=data.user_id";
       $result = oci_parse($conn, $query);
		 $res  = oci_execute($result);
		 
		 if ($res) {
				oci_commit($conn);
			} else {
				$e = oci_error($result);
				echo "Data selecting inproperly " . $e['message'] . "<br>";
			}
			oci_free_statement($result);
			
//=================================================================================

$query = "INSERT INTO specify_table VALUES (2, 'A', TO_DATE('01-NOV-15', 'DD/MM/YY'), 20)";
			$result = oci_parse($conn, $query);
			$res  = oci_execute($result);
			if ($res) {
				oci_commit($conn);
			} else {
				$e = oci_error($result);
				echo "Data selecting inproperly " . $e['message'] . "<br>";
			}
			oci_free_statement($result);
			
			
//=================================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['yearly'])) {
      echo "Year <br>";
      $date_format ="TO_CHAR(date_created, 'YYYY') as year";
		$date_rollup ="TO_CHAR(date_created, 'YYYY')";
		$date_group = "year";
    }
    
    elseif(isset($_POST['monthly'])) {
      echo "Month <br>";
      $date_format = "TO_CHAR(date_created, 'YYYY') as year, TO_CHAR(date_created, 'Mon') as month";
		$date_rollup = "TO_CHAR(date_created, 'YYYY'), TO_CHAR(date_created, 'Mon')";
		$date_group = "month";
    }
    elseif(isset($_POST['weekly'])) {
    	echo "Weekly <br>";
    	$date_format = "TO_CHAR(date_created, 'YYYY') as year, TO_CHAR(date_created, 'WW') as week";
		$date_rollup = "TO_CHAR(date_created, 'YYYY'), TO_CHAR(date_created, 'WW')";
		$date_group = "week";
    }
    
    
    
    
    
}


		       





?>








</html>

