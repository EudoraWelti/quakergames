<?php
/*
-------------TODO-------------
	- style google login
	- mobile browser compatibility
	- scrape directory for firstname,lastname,email,grade (photo+schedule?)
*/
require_once("./admin/library.php");
pre_header("Quaker Games");
if(isset($_SESSION['email'])){ 
	$result = mysqli_query($dbc,"SELECT firstname FROM `playerList` WHERE email='{$_SESSION['email']}'");
	if(mysqli_num_rows($result)>0){ //if a player
		$name = mysqli_fetch_array($result)['firstname'];
		print_header("Welcome, $name");
		$result = mysqli_query($dbc,"SELECT target FROM `players` WHERE email='{$_SESSION['email']}'");
		if(mysqli_num_rows($result)>0){
			$data = mysqli_fetch_array($result);
			$data2 = mysqli_fetch_array(mysqli_query($dbc,"SELECT firstname,lastname FROM `players` WHERE email='{$data['target']}'"));
			echo"Your Target: {$data2['firstname']} {$data2['lastname']}<br>";
			echo"
				<form action='/elimination.php' method='post'>
					<input type='submit' id='elimReport' value='Report Elimination' class='submit'>
				</form>
			";
		} else {
			echo"Unfortunately, you have been eliminated from the Quaker Games.";
		}
	} else {
		print_header("Welcome!");
		echo"You are not a registered player in this year's Quaker Games.";
	}
	if($_SESSION['email']==='nrose24@sidwell.edu'){
		echo"<br><br><a href='/admin'>Admin</a>";
	}
} else {
	print_header("Welcome!");
	echo"Please login to view your target.";
}

print_footer();
?>