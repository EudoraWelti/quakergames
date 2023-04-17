<?php
require_once("./admin/library.php");
pre_header("Contest");
print_header("Contest Your Elimination");

$result=mysqli_query($dbc,"SELECT hunter,target,DATE_FORMAT(submitTime,'%r, %a %b %e') as submitTime,description,DATE_FORMAT(now(),'%r, %a %b %e') as now, id, submitTime as notFormattedTime FROM `reports` WHERE target='{$_SESSION['email']}'");
$data=mysqli_fetch_array($result);
if(isset($_POST['contest'])){
	if(mysqli_num_rows($result)>0){
		mysqli_query($dbc,"INSERT INTO `contests` (hunter, target, description, reportTime, contestTime, id) VALUES ('{$data['hunter']}','{$data['target']}','{$_POST['description']}','{$data['notFormattedTime']}',now(),'{$data['id']}')");
		mysqli_query($dbc,"UPDATE `reports` SET contested = TRUE WHERE target='{$_SESSION['email']}'");
		echo"Your elimination has been successfully contested.";
	} else {
		$result2=mysqli_query($dbc,"SELECT firstname FROM `players` WHERE email='{$_SESSION['email']}'");
		if(mysqli_num_rows($result)>0){
			$name = mysqli_fetch_array($result2)['firstname'];
			echo"Good news $name, there is no elimination report in for you!";
		} else {
			$yourl='"/appeal.php"';
			echo"Unfortunately, you've already been eliminated. If you think you've been wronged, you can appeal&nbsp<a href=$yourl>here</a>.";
		}
	}
}
echo"
<table>
	<tr><th>You</th><td id='centeredTd'>{$_SESSION['email']}</td></tr>
	<tr><th>Hunter</th><td id='centeredTd'>{$data['hunter']}</td></tr>
	<tr><th>Report Time</th><td id='centeredTd'>{$data['submitTime']}</td></tr>
	<tr><th>Report Description</th><td id='centeredTd'>{$data['description']}</td></tr>
	<tr><th>Time</th><td id='centeredTd'>{$data['now']}</td></tr>
	<tr><th>Reason for contest</th>
	<td id='descriptionData'><textarea form='reportForm' name='description' class='textArea' placeholder='They put the sticker on my forearm, not my hand.'></textarea><br></td>
	</tr>
</table>
<form action='' method='post' id='reportForm'>
	<input type='submit' value='Contest' name='contest' class='submit'>
</form>
";

print_footer();
?>