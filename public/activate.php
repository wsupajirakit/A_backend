<?php
	session_start();
	$_SESSION['beta'] = 'open';
	header("Location: index.php");
?>