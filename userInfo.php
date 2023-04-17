<?php
	require_once("./admin/library.php");
	pre_header("User Info");
	print_header("User Info");

if(isset($_SESSION['email'])){
	$result = mysqli_fetch_array(mysqli_query($dbc,"SELECT firstname,lastname,last_login FROM `accounts` WHERE email='{$_SESSION['email']}'"));
	echo"<br>
	<h3>Info for {$_SESSION['email']}</h3>
	First Name: {$result['firstname']}<br>
	Last Name: {$result['lastname']}<br>
	Last login: {$result['last_login']}<br>";
} else {
	echo"Please login to view your user info.";
}

	print_footer();
?>