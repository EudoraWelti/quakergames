<?php
require_once("./admin/library.php");
pre_header("Elimination",);
print_header("Report an Elimination");

if(isset($_SESSION['email'])){
$data = mysqli_fetch_array(mysqli_query($dbc,"SELECT firstname,lastname,target FROM `players` WHERE email='{$_SESSION['email']}'"));
$data2 = mysqli_fetch_array(mysqli_query($dbc,"SELECT firstname,lastname FROM `players` WHERE email='{$data['target']}'"));
$data3 = date("g:ia l, F j");
if(isset($_POST['report'])){
	$data4= str_replace("'","''",$_POST['description']);
	$prevReports=mysqli_query($dbc,"SELECT hunter FROM `reports` WHERE hunter='{$_SESSION['email']}'");
	if(mysqli_num_rows($prevReports)>0){
		echo"You have already submitted an elimination report. Would you like to delete that report?
			<form action='' method='post' id='approveDeny'>
		  		<input type='submit' class='submit' id='yesNo' value='Yes' name='yesDelete'>
			</form>
			<form action='' method='post' id='approveDeny'>
		  		<input type='submit' class='submit' id='yesNo' value='No' name='noDelete'>
			</form>
		";
	} else {
		mysqli_query($dbc,"INSERT INTO `reports` (hunter, target, submitTime, description) VALUES ('{$_SESSION['email']}','{$data['target']}',now(),'{$data4}')");
		echo"Your elimination has been reported. You'll receive an email with your next target when the elimination is approved.";
	}
}
if(isset($_POST['noDelete'])){

}
if(isset($_POST['yesDelete'])){
	mysqli_query($dbc,"DELETE FROM `reports` WHERE `hunter` = '{$_SESSION['email']}'");
	echo"Your previous report has been deleted. You may now submit a new one.";
}


echo"
	<table>
		<tr><th>You</th><td id='centeredTd'>{$data['firstname']} {$data['lastname']}</td></tr>
		<tr><th>Target</th><td id='centeredTd'>{$data2['firstname']} {$data2['lastname']}</td></tr>
		<tr><th>Date</th><td id='centeredTd'>$data3</td></tr>
		<tr><th>Description</th><td id='descriptionData'><textarea form='reportForm' name='description' class='textArea' placeholder='I got them out outside of the cafeteria at about noon. They agreed that they were out.'></textarea><br></td></tr>
	</table>	
	<form action='' method='post' id='reportForm'>
		<input type='submit' value='Report' name='report' class='submit'>
	</form>
";
} else {
	echo"Please log in to report an elimination.";
}
print_footer();
?>