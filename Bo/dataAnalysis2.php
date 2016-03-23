<?php


include("connDB.php");
$message= "";
$images= "";
$php_self= $_SERVER['PHP_SELF'];
session_start();


//If session could not define the user name, we will jump to the login page
if(!$_SESSION['username']) {
	redirect('login.php');	
}

$user=$_SESSION['username'];

//Only administrater has right to do the dataAnalysis
if ($user != 'admin') {
    redirect('main_page.php');
}

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
	
	
	
	
	
}	





?>
<html>
	<Body>
		<h1>Data Analysis</h1>
		<div id="dataAnalysis_button"> Analysis Data </div>
		<div id="data_analysis_panel">
				<form action="dataAnalysis.php" method="post">
					<fieldset>
						user:	<input type="text" name="keywords" placeholder="Please enter user id"> <br /> <br />
						key Words:	<input type="text" name="keywords" placeholder="Please enter key words"> <br /> <br />
						From:	<input type="text" name="startDate" placeholder="Please enter start date"> Format: dd/mm/yyyy hh24:mi:ss<br /> <br />			
						To:	<input type="text" name="endDate" placeholder="Please enter end date"> Format: dd/mm/yyyy hh24:mi:ss<br /> <br />
						<button type="reset">Reset</button>
						<input type="submit" name="upload_analysis" value="Submit">
						
						<input  type = "checkbox" name = "showUsers" value = "Users">Users					
						<input  type = "checkbox" name = "showSubjects" value = "subjects">subjects
						<input  type = "checkbox" name = "showWeekly" value = "weekly">Weekly
					   <input type = "checkbox" name = "showMonthly" value = "monthly">Monthly
						<input type = "checkbox" name = "showYearly" value = "yearly">Yearly
						
					</fieldset>
		
		
		</div>
</html>


