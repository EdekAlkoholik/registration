<?php
		session_start();	
		
		if(!isset($_SESSION['udanarejestracja'])) 
		{
				header('Location: index.php');
				exit();
		}
		else
		{
			unset($_SESSION['udanarejestracja']);
		}
		
		// czyszczenie sesji z zapamiętanych danych
		
		if(isset($_SESSION['last_nick'])) unset($_SESSION['last_nick']);
		if(isset($_SESSION['last_email'])) unset($_SESSION['last_email']);
		if(isset($_SESSION['last_pass1'])) unset($_SESSION['last_pass1']);
		if(isset($_SESSION['last_pass2'])) unset($_SESSION['last_pass2']);
		if(isset($_SESSION['last_regulamin'])) unset($_SESSION['last_regulamin']);
		
		if(isset($_SESSION['error_nick'])) unset($_SESSION['error_nick']);
		if(isset($_SESSION['error_email'])) unset($_SESSION['error_email']);
		if(isset($_SESSION['error_pass1'])) unset($_SESSION['error_pass1']);
		if(isset($_SESSION['error_pass2'])) unset($_SESSION['error_pass2']);
		if(isset($_SESSION['error_regulamin'])) unset($_SESSION['error_regulamin']);
		
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-eqiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Plemiona - gra przeglądarkowa</title>
</head>
<body>
	Dziekujemy za rejestrację w serwisie - możesz już zalogować się na swoje konto
	
	<br /><br />
	<a href="index.php">Zaloguj się na swoje konto</a>
	<br /><br />

	
</body>
</html>