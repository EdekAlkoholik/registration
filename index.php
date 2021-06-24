<?php
		session_start();	
		
		if((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany'] == true)) 
		{
				header('Location: gra.php');		// jeśli zalogowany idz do gry
				exit();											// żeby nie wykonywać dalszego kodu tutaj
		}
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-eqiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Plemiona - gra przeglądarkowa</title>
</head>
<body>
	Tylko martwi ujrzeli koniec wojny - Platon
	
	<br /><br />
	<a href="rejestracja.php">Zarejestruj się</a>
	<br /><br />
	
	<form action="zaloguj.php" method="post">
		Login: <br /> <input type="text" name="login" /> <br />
		Hasło: <br /> <input type="password" name="haslo" /> <br />
		<br /> <input type="submit" value="Zaloguj" />
	</form>
	
<?php
		if(isset($_SESSION['blad'])) echo $_SESSION['blad'];		// wyswietl info jesli nastapilo błedne logowanie, jesli nastapiło
?>
	
</body>
</html>