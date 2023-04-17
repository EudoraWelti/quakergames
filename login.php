<?php
require_once("./admin/library.php");
pre_header("Log In");
print_header("Log In");


if(isset($_SESSION['email'])){
	echo"
		<h3>Change Password:</h3>
		<form action='' method='post'>
		  	<input type='password' class='textEntry' name='checkPassword' placeholder='Old Password' value='' length=10><br>
		  	<input type='password' class='textEntry' name='newpassword' placeholder='New Password' value='' length=10><br>
		  	<input type='submit' class='submit' name ='changedPassword' alue='Change'>
		</form>
		<br>
	";
} else {
	echo"
		<h3>Log In</h3>
		<form action='/' method='post'>
			$errMsg
			<input type='text' class='textEntry' name='email' value='' placeholder='Email'><br>
			<input type='password' class='textEntry' name='password' value='' placeholder='Password'><br>
			<input type='submit' class='submit' name='loginSubmitted' value='Log In'>
		</form>
		<br>
	";
	echo"
		<h3>Sign Up</h3>
		<form action='/' method='post'>
			<input type='text' name='firstname' class='textEntry' placeholder='First Name'><br>
			<input type='text' name='lastname' class='textEntry' placeholder='Last Name'><br>
			<input type='text' name='createdEmail' class='textEntry' placeholder='Email'><br>
			<input type='password' name='createdPassword' class='textEntry' placeholder='Password'><br>
			<input type='submit' value='Sign Up' name='signUp' class='submit'>
		</form>
		<br>
	";
}

print_footer();
?>