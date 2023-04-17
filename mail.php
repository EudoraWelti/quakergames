<?php
require_once("./admin/library.php");
pre_header("Mail");
print_header("Mail");

	if(isset($_POST['emailQuery'])){
		mail($_POST['to'],$_POST['subject'],$_POST['txt'],"From: {$_POST['from']} <{$_POST['senderEmail']}>");
		echo"Successfully mailed '{$_POST['txt']}' with subject '{$_POST['subject']}' to {$_POST['to']} from {$_POST['from']} <{$_POST['senderEmail']}>";
	}
?>
<form action="/mail.php" method="post">
	<input type="text" name="to" class='textEntry' placeholder="To"><br>
	<input type="text" name="subject" class='textEntry' placeholder="Subject" size="20"><br>
	<input type="text" name="txt" class='textEntry' placeholder="Text" size="40"><br>
	<input type="text" name="from" class='textEntry' placeholder="From"><br>
	<input type="text" name="senderEmail" class='textEntry' placeholder="Sender Email"><br>
	<input type="hidden" name="emailQuery" value='y'>
	<input type="submit" class='submit'>
</form>

<?php
print_footer();
?>