<?php
require_once("../admin/library.php");
pre_header("Report History");
print_header("Report History");

if(isset($_SESSION['email'])){
	if($_SESSION['email']==='nrose24@sidwell.edu'){
		$result = mysqli_query($dbc,"SELECT * FROM `reportGraveyard`");
		echo"<table><tr><th>Hunter</th><th>Target</th><th>Submit Time</th><th>Description</th><th>Status</th><th>Decision Time</th></tr>";
		while ($data = mysqli_fetch_array($result)){
			echo"<tr><td>{$data['hunter']}</td><td>{$data['target']}</td><td>{$data['submitTime']}</td><td>{$data['description']}</td><td>{$data['status']}</td><td>{$data['decisionTime']}</td></tr>";
		}
		echo"</table>";
	} else {
		echo"You do not have permission to view this page.";
	}
} else {
	echo"You do not have permission to view this page.";
}

print_footer();
?>