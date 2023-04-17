<?php
require_once("./admin/library.php");
pre_header("Leaderboard");
print_header("Leaderboard");

echo"<h3>Player Leaderboard</h3><table>";
$result = mysqli_query($dbc,"SELECT * FROM `players` WHERE `killCount` > 14 ORDER BY killCount DESC");
if(mysqli_num_rows($result)>0){
	echo"<tr><th colspan='3'>Garman Level</th></tr>";
	while ($data = mysqli_fetch_array($result)){
		echo"<tr><td id='centeredTd'>{$data['killCount']}</td><td>{$data['firstname']} {$data['lastname']}</td><td id='centeredTd'>{$data['grade']}</td></tr>";
	}
}
$result = mysqli_query($dbc,"SELECT * FROM `players` WHERE `killCount` > 9 AND `killCount` < 15 ORDER BY killCount DESC");
if(mysqli_num_rows($result)>0){
	echo"<tr><th colspan='3'>Mamadou Level</th></tr>";
	while ($data = mysqli_fetch_array($result)){
		echo"<tr><td id='centeredTd'>{$data['killCount']}</td><td>{$data['firstname']} {$data['lastname']}</td><td id='centeredTd'>{$data['grade']}</td></tr>";
	}	
}
$result = mysqli_query($dbc,"SELECT * FROM `players` WHERE `killCount` > 4 AND `killCount` < 10 ORDER BY killCount DESC");
if(mysqli_num_rows($result)>0){
	echo"<tr><th colspan='3'>Gross Level</th></tr>";
	while ($data = mysqli_fetch_array($result)){
		echo"<tr><td id='centeredTd'>{$data['killCount']}</td><td>{$data['firstname']} {$data['lastname']}</td><td id='centeredTd'>{$data['grade']}</td></tr>";
	}
}
$result = mysqli_query($dbc,"SELECT * FROM `players` WHERE `killCount` > 1 AND `killCount` < 5 ORDER BY killCount DESC");
if(mysqli_num_rows($result)>0){
	echo"<tr><th colspan='3'>Woods Level</th></tr>";
	while ($data = mysqli_fetch_array($result)){
		echo"<tr><td id='centeredTd'>{$data['killCount']}</td><td>{$data['firstname']} {$data['lastname']}</td><td id='centeredTd'>{$data['grade']}</td></tr>";
	}
}
$result = mysqli_query($dbc,"SELECT * FROM `players` WHERE `killCount` < 2 ORDER BY killCount DESC");
if(mysqli_num_rows($result)>0){
	echo"<tr><th colspan='3'>Mormino Level</th></tr>";
	while ($data = mysqli_fetch_array($result)){
		echo"<tr><td id='centeredTd'>{$data['killCount']}</td><td>{$data['firstname']} {$data['lastname']}</td><td id='centeredTd'>{$data['grade']}</td></tr>";
	}
}
echo"</table><br>";

echo"<h3>Grade Leaderboard</h3><table>";
$result=mysqli_query($dbc,"SELECT `grade`,SUM(`killCount`) FROM `players` GROUP BY `grade` ORDER BY SUM(`killCount`) DESC,`grade` ASC;");
echo"<tr><th>Elims</th><th>Grade</th></tr>";
while($data=mysqli_fetch_array($result)){
	$ord=ordinal($data['grade']);
	echo"<tr><td id='centeredTd'>{$data['SUM(`killCount`)']}</td><td id='centeredTd'>$ord</td></tr>";
}
echo"</table>";
print_footer();
?>