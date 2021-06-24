<?php
		session_start();	
		
		session_unset();							// zamknij sesję
		header('Location: index.php');	// wróc do strony głównej
		
?>
