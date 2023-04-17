<?php
//change how all text is highlighted green on /week2/
//make a user data page when name is clicked
	
	define('base_url',"https://quakergames.com");
	define('DB_USER','nico');
	define('DB_PASSWORD','password');
	define('DB_HOST','localhost');
	define('DB_NAME','nicoTable');
	date_default_timezone_set('America/New_York');

	if ($dbc = @mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD)) mysqli_select_db($dbc,DB_NAME);
	else die('Failed to connect to database!');
	$url='';
	$errMsg='';
	
	function pre_header($title){
		global $dbc;
		global $errMsg;
		session_start();
		
		error_reporting(-1); //change before publication
		ini_set('display_errors','On');
		echo"
			<script type='text/javascript'>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<script src='https://accounts.google.com/gsi/client' async defer></script>
    		<script src='https://unpkg.com/jwt-decode/build/jwt-decode.js'></script>
		    <script>
			  function handleCredentialResponse(response) {
			    console.log(jwt_decode(response.credential)['email']);
			    document.getElementById('emailVariable').setAttribute('value',jwt_decode(response.credential)['email']); 
			    let myUsername = document.getElementById('emailVariable').getAttribute('value');
				console.log(myUsername);
				document.getElementById('emailSubmitForm').submit();
			  }

			</script>
		";
		echo"
			<!DOCTYPE html>
			<html>

			<head>
				<link rel='preconnect' href='https://fonts.googleapis.com'>
				<link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
				<link href='https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap' rel='stylesheet'>
				<link rel='stylesheet' type='text/css' href='/styles.css'>
			 	<title>$title</title>
			 	<link rel='icon' type='image/x-icon' href='/images/shine.png'>
			</head>
		";
		if(isset($_POST['emailVariable'])){
			$_SESSION['email']=$_POST['emailVariable'];
		}
		if(isset($_POST['loginSubmitted'])){
			$result = mysqli_fetch_array(mysqli_query($dbc,"SELECT password FROM `accounts` WHERE email='{$_POST['email']}'"));
			if($result==null){
				$errMsg="Unregistered Email<br>";
			} else if(sha1($_POST['password'])==$result['password']) {
				$_SESSION['email']=$_POST['email'];
				mysqli_query($dbc, "UPDATE `accounts` SET last_login=CONVERT_TZ(CURRENT_TIMESTAMP,'+00:00','+02:00') WHERE email='{$_POST['email']}'");
				$result2 = mysqli_query($dbc,"SELECT firstname FROM `players` WHERE email='{$_SESSION['email']}'");
				while ($data = mysqli_fetch_array($result2)){
					$_SESSION['name']=$data['firstname'];
				}
			} else {
				$errMsg="Incorrect password<br>";
			}
		}
		if(isset($_POST['logout'])){
			session_destroy();
			unset($_SESSION);
		}
		if(isset($_POST['signUp'])){
			$pass=sha1($_POST['createdPassword']);
			mysqli_query($dbc,"INSERT INTO `accounts` (email, firstname, lastname, password) VALUES ('{$_POST['createdEmail']}','{$_POST['firstname']}','{$_POST['lastname']}','$pass')");
		}
		if (isset($_POST['changedPassword'])){
			$result = mysqli_fetch_array(mysqli_query($dbc,"SELECT password FROM `accounts` WHERE email='{$_SESSION['email']}'"));
			if(sha1($_POST['checkPassword'])==$result['password']){
				mysqli_query($dbc,"UPDATE `accounts` SET password='".sha1($_POST['newpassword'])."' WHERE email='{$_SESSION['email']}'");
				echo"Password changed!";
			}
		}

		
	}

	function print_header($title) {
		global $dbc;
		global $errMsg;
		echo"
			<body>

			<div class='header'>

			<div class='home'>
				<a href='/'><img src='/images/shine.png' id='burger'></a>
			</div>
			<div class='title'>
				<h1>$title</h1>
			</div>
			<div class='subtitles'>
				<h2><a href='/leaderboard.php' id='headerLink'>Leaderboard</a></h2>
				<h2><a href='/rules.php' id='headerLink'>Rules</a></h2>
			</div>
		";
		
		if(isset($_SESSION['email'])){
			echo"
				<div class='login'>
				<form action='' method='post'>
					<input type='submit' class='submit' id='loginButton' name='logout' value='Log Out'>
				</form>
				</div>
			";
		} else {
			echo"
				<div id='g_id_onload'
			        data-client_id='16190065804-2q6fv728fvvdaoddr86g9p8rerdc336j.apps.googleusercontent.com'
			        data-callback='handleCredentialResponse'
			        data-context='signin'
				    data-ux_mode='popup'
				    data-login_uri='quakergames.com'
				    data-itp_support='true'
			    ></div>

				<div class='login'>
					<div class='g_id_signin' data-type='standard'
						data-type='icon'
					    data-shape='pill'
					    data-theme='filled_black'
					    data-text='signin'
					    data-size='medium'
					    data-logo_alignment='left'
					    data-width='150px'
					></div>
					
				</div>
				<form action='' method='post' id='emailSubmitForm'>
					<input type='hidden' id='emailVariable' name='emailVariable'>
				</form>
				<div id='fakeButton'>
					<form>
						<input type='submit' class='submit' id='loginButton' value='Log In'>
						
					</form>
				</div>
			";
		}
		echo"</div>";

		if(isset($_SESSION['email'])){
			$result=mysqli_query($dbc,"SELECT hunter, target, ADDTIME(submitTime,'0:15:00') as newTime, TRIM(LEADING '0' FROM DATE_FORMAT(TIMEDIFF(ADDTIME(submitTime,'0:14:59'),now()),'%i:%s')) as timeDiff, TIMEDIFF(ADDTIME(submitTime,'0:14:59'),now()) as sign FROM `reports` WHERE target='{$_SESSION['email']}'");
			if(mysqli_num_rows($result)>0){ //countdown from w3
				$data=mysqli_fetch_array($result);
				$hunter=mysqli_fetch_array(mysqli_query($dbc,"SELECT firstname, lastname FROM `playerList` WHERE email='{$data['hunter']}'"));
				$hunterName="{$hunter['firstname']} {$hunter['lastname']}";
				$yourl='"/contest.php"';
				
				if($data['sign']>0){
					echo"<div id='eliminationAlert'>Urgent! Your elimination was reported by {$hunterName}. If you want to, you have {$data['timeDiff']} to contest it&nbsp<a href=$yourl>here</a>.</div>";
				} else {
					echo"<div id='eliminationAlert'>Urgent! Your elimination was reported by {$hunterName}. If you want to, contest it&nbsp<a href=$yourl>here</a>.</div>";
				}
				
				echo"
					<script>
						console.log('{$data['newTime']}');
						var countDownDate = new Date('{$data['newTime']}').getTime();
						var x = setInterval(function() {
						  var now = new Date().getTime();
						  var distance = countDownDate - now;
						  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
						  var seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
						  document.getElementById('eliminationAlert').innerHTML = 'Urgent! Your elimination was reported by {$hunterName}. If you want to, you have ' + minutes + ':' + seconds + ' to contest it&nbsp<a href=$yourl>here</a>.';
						  if (distance < 0) {
						    clearInterval(x);
						    document.getElementById('eliminationAlert').innerHTML = 'Urgent! Your elimination was reported by {$hunterName}. If you want to, contest it&nbsp<a href=$yourl>here</a>.';
						  }
						}, 1000);
					</script>
				";				
			}
		}
		echo"
			<div class='middle'><br>
		";

	}

	function print_footer() {
		echo "
			<br></div>
			<div class='footer'>
				<div id='footerDate'>
					Page loaded at: ",date('m/d/Y h:i:sa', time()),"
				</div>";
		if(isset($_SESSION['email'])){
			echo"
				<div id='footerUser'>
					Logged in as <a id='footerLink' href='/userInfo.php'>{$_SESSION['email']}</a>
				</div>
			";
		}
		echo"
			</div>
			</body>
			</html>
		";
	}

	function ordinal($number) {
	    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
	    if ((($number % 100) >= 11) && (($number%100) <= 13))
	        return $number. 'th';
	    else
	        return $number. $ends[$number % 10];
	}
?>