<?php
require_once("../admin/library.php");
pre_header("Admin");
print_header("Admin");

if(isset($_SESSION['email'])){
	if($_SESSION['email']==='nrose24@sidwell.edu'){
		if(isset($_POST['firstname'])){
			mysqli_query($dbc,"INSERT INTO `players` (firstname, lastname, email, grade) VALUES ('{$_POST['firstname']}','{$_POST['lastname']}','{$_POST['email']}','{$_POST['grade']}')");
			$result = mysqli_query($dbc,"SELECT firstname FROM `playerList` where email='{$_POST['email']}'");
			if(mysqli_num_rows($result)<1){
				mysqli_query($dbc,"INSERT INTO `playerList` (firstname, lastname, email, grade) VALUES ('{$_POST['firstname']}','{$_POST['lastname']}','{$_POST['email']}','{$_POST['grade']}')");
			}
		}
		if(isset($_POST['remove'])){
			mysqli_query($dbc,"DELETE FROM `players` WHERE `id` = '{$_POST['id']}'");
		}

		if(isset($_POST['approve'])){
			$data=mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `reports` WHERE id='{$_POST['reportId']}'"));
			mysqli_query($dbc,"INSERT INTO `reportGraveyard` (hunter, target, submitTime, description, status, decisionTime) VALUES ('{$data['hunter']}','{$data['target']}','{$data['submitTime']}','{$data['description']}','approved',now())");
			$target=mysqli_fetch_array(mysqli_query($dbc,"SELECT target,email FROM `players` WHERE email='{$data['target']}'"));
			mysqli_query($dbc,"UPDATE `players` SET target = '{$target['target']}', killCount=killCount+1 WHERE email='{$data['hunter']}';");
			mysqli_query($dbc,"DELETE FROM `players` WHERE `email` = '{$data['target']}'");
			mysqli_query($dbc,"DELETE FROM `reports` WHERE `id` = '{$_POST['reportId']}'");
		}
		if(isset($_POST['deny'])){
			$data=mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `reports` WHERE id='{$_POST['reportId']}'"));
			mysqli_query($dbc,"INSERT INTO `reportGraveyard` (hunter, target, submitTime, description, status, decisionTime) VALUES ('{$data['hunter']}','{$data['target']}','{$data['submitTime']}','{$data['description']}','denied',now())");
			mysqli_query($dbc,"DELETE FROM `reports` WHERE `id` = '{$_POST['reportId']}'");
		}

		if(isset($_POST['contestApprove'])){
			$data=mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `contests` WHERE id='{$_POST['contestId']}'"));
			mysqli_query($dbc,"INSERT INTO `contestGraveyard` (hunter, target, contestTime, decisionTime, description, status, id) VALUES ('{$data['hunter']}','{$data['target']}','{$data['contestTime']}',now(),'{$data['description']}','approved','{$data['id']}')");
			mysqli_query($dbc,"DELETE FROM `reports` WHERE `id` = '{$_POST['contestId']}'");
			mysqli_query($dbc,"DELETE FROM `contests` WHERE `id` = '{$_POST['contestId']}'");
		}
		if(isset($_POST['contestDeny'])){
			$data=mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `contests` WHERE id='{$_POST['contestId']}'"));
			mysqli_query($dbc,"INSERT INTO `contestGraveyard` (hunter, target, contestTime, decisionTime, description, status, id) VALUES ('{$data['hunter']}','{$data['target']}','{$data['contestTime']}',now(),'{$data['description']}','denied','{$data['id']}')");
			mysqli_query($dbc,"DELETE FROM `contests` WHERE `id` = '{$_POST['contestId']}'");
		}

		if(isset($_POST['shuffle'])){
			$players=array();
			$result = mysqli_query($dbc,"SELECT id,firstname,email FROM `players`");
			$playerCount=mysqli_num_rows($result);
			while ($data = mysqli_fetch_array($result)){
				$id=$data['id'];
				$players[$id]=$data['email']; //change to email later
			}

			$activePlayer=array_rand($players,1);
			$ogPlayer=$players[$activePlayer];
			unset($players[$activePlayer]);
			for ($i = 0; $i < $playerCount-1; $i++) {
				$activeTarget=array_rand($players,1);
				mysqli_query($dbc,"UPDATE players SET target = '{$players[$activeTarget]}' WHERE id = $activePlayer;");
				unset($players[$activeTarget]);
		  		$activePlayer=$activeTarget;
			}
			mysqli_query($dbc,"UPDATE players SET target = '{$ogPlayer}' WHERE id = $activePlayer;");
		}
		if(isset($_POST['killReset'])){
			mysqli_query($dbc,"UPDATE players SET killCount = 0;");
		}
		if(isset($_POST['gameReset'])){
			mysqli_query($dbc,"DELETE FROM `players`");
			$result=mysqli_query($dbc,"SELECT * FROM `playerList`");
			while($data=mysqli_fetch_array($result)){
				mysqli_query($dbc,"INSERT INTO `players` (firstname, lastname, email, grade) VALUES ('{$data['firstname']}','{$data['lastname']}','{$data['email']}','{$data['grade']}')");
			}
		}

		echo"<h3>Reported Eliminations</h3>";

		$result = mysqli_query($dbc,"SELECT hunter,target,description,id,DATE_FORMAT(submitTime,'%r, %a %b %e') as submitTime,contested,TRIM(LEADING '0' FROM DATE_FORMAT(TIMEDIFF(ADDTIME(submitTime,'0:14:59'),now()),'%i:%s')) as timeDiff, TIMEDIFF(ADDTIME(submitTime,'0:14:59'),now()) as sign FROM `reports`");
		echo"<table><tr><th>hunter</th><th>Target</th><th>Time</th><th>Description</th><th>Wait</th><th></th></tr>";
		while ($data = mysqli_fetch_array($result)){
			$hunter=mysqli_fetch_array(mysqli_query($dbc,"SELECT firstname,lastname FROM `players` WHERE email='{$data['hunter']}'"));
			$target=mysqli_fetch_array(mysqli_query($dbc,"SELECT firstname,lastname FROM `players` WHERE email='{$data['target']}'"));

			if($data['contested']==1){
				echo"<tr id='stop'>";
			} else {
				if($data['sign']>0){
					echo"<tr id='wait'>";
				} else {
					echo"<tr id='allgood'>";
				}
			}
			echo"<td>{$hunter['firstname']} {$hunter['lastname']}</td><td>{$target['firstname']} {$target['lastname']}</td><td>{$data['submitTime']}</td><td>{$data['description']}</td>";
			if($data['sign']>0){
				echo"<td>{$data['timeDiff']}</td>";
			} else {
				echo"<td>Ready</td>";
			}
			echo"
				<td><form action='' method='post'>
					<input type='hidden' name='reportId' value='".$data['id']."'>
			  		<input type='submit' class='submit' id='rm' value='&#10008;' name='deny'>
				</form>
				<form action='' method='post'>
					<input type='hidden' name='reportId' value='".$data['id']."'>
			  		<input type='submit' class='submit' id='rm' value='&#10004;' name='approve'>
				</form>
			</td></tr>";
		}
		echo"</table><br>";

		echo"<h3>Contested Eliminations</h3>";
		$result = mysqli_query($dbc,"SELECT hunter, target, description, DATE_FORMAT(reportTime,'%r, %a %b %e') as reportTime,DATE_FORMAT(contestTime,'%r, %a %b %e') as contestTime, id FROM `contests`");
		echo"<table><tr><th>Hunter</th><th>Target</th><th>Report Time</th><th>Contest Time</th><th>Description</th><th></th></tr>";
		while ($data = mysqli_fetch_array($result)){
			$hunter=mysqli_fetch_array(mysqli_query($dbc,"SELECT firstname,lastname FROM `players` WHERE email='{$data['hunter']}'"));
			$target=mysqli_fetch_array(mysqli_query($dbc,"SELECT firstname,lastname FROM `players` WHERE email='{$data['target']}'"));
			echo"<tr><td>{$hunter['firstname']} {$hunter['lastname']}</td><td>{$target['firstname']} {$target['lastname']}</td><td>{$data['reportTime']}</td><td>{$data['contestTime']}</td><td>{$data['description']}</td><td>
				<form action='' method='post'>
					<input type='hidden' name='contestId' value='".$data['id']."'>
			  		<input type='submit' class='submit' id='rm' value='&#10008;' name='contestDeny'>
				</form>
				<form action='' method='post'>
					<input type='hidden' name='contestId' value='".$data['id']."'>
			  		<input type='submit' class='submit' id='rm' value='&#10004;' name='contestApprove'>
				</form>
			</td></tr>";
		}
		echo"</table><br>";

		echo"<h3>Players</h3>";
		echo"
			<form action='' method='post'>
				<input type='submit' value='Shuffle' name='shuffle' class='submit'>
			</form>
			<form action='' method='post'>
				<input type='submit' value='Reset Kills' name='killReset' class='submit'>
			</form>
			<form action='' method='post'>
				<input type='submit' value='Reset Game' name='gameReset' class='submit'>
			</form>
		";

		$result = mysqli_query($dbc,"SELECT * FROM `players`");
		echo"<table><tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Grade</th><th>Target</th><th>Kills</th><th></th></tr>";


		while ($data = mysqli_fetch_array($result)){
			echo"<tr><td>{$data['firstname']}</td><td>{$data['lastname']}</td><td>{$data['email']}</td><td id='centeredTd'>{$data['grade']}</td><td>{$data['target']}</td><td id='centeredTd'>{$data['killCount']}</td><td>
			<form action='' method='post'>
				<input type='hidden' name='id' value='".$data['id']."'>
		  		<input type='submit' class='submit' id='rm' value='-' name='remove'>
			</form>
			</td></tr>";
		}
		echo"
			<form action='./' method='post'>
				<tr id='inputRow'>
				<td><input type='text' size='1' name='firstname' class='textEntry'></td>
				<td><input type='text' size='1' name='lastname' class='textEntry'></td>
				<td><input type='text' size='1' name='email' class='textEntry'></td>
				<td><input type='text' size='1' name='grade' class='textEntry'></td><td></td><td></td>
				<td><input type='submit' size='1' value='+' class='submit' id='rm'></td>
				</tr>
			</form>
		";
		echo"</table>";

		echo"<a href='/admin/reportHistory.php'>Report History</a>";
	} else {
		echo"You do not have permission to view this page.";
	}
} else {
	echo"You do not have permission to view this page.";
}

print_footer();
?>