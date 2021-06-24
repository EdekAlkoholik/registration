<?php
	session_start();
	
	if(!isset($_SESSION['zalogowany']))		// jeśli nie (!) jesteśmy zalogowani wróć na stroną główną
	{
		header('Location: index.php');
		exit();
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
	
	<?php
		echo "<p>Witaj ".$_SESSION['user'].'! [<a href="logout.php">Wyloguj się</a>]</p>';		// link do wylogowania
		echo "<p><b>Drewno</b>: ".$_SESSION['drewno'];
		echo "|<b>Kamien</b>: ".$_SESSION['kamien'];
		echo "|<b>Zboże</b>: ".$_SESSION['kamien'];
		echo "<p><b>E-mail</b>: ".$_SESSION['email'];
		echo "<p><b>Data wygaśnięcia dni premium</b>: ".$_SESSION['dnipremium']."</br>";
		//echo "<p><b>Pozostało dni premium</b>: ".$_SESSION['']."</br>";
		
		$dataczas = new DateTime('2017-01-01 09:30:15'); 									// pusty konstruktor zrobi aktualny czas
		
		echo "Data i czas serwera: ".$dataczas->format('d.m.y H:i:s')."<br>";		// formatujemy jak chcemy pokazywać czas
		
		$koniecPremium = DateTime::createFromFormat('Y-m-d H:i:s', $_SESSION['dnipremium']);		// rzutujemy wartość dnipremium na obj DateTime
		$roznica = $dataczas->diff($koniecPremium);	// zwraca różnicę między datami kolejność dowolna
		
		if($dataczas < $koniecPremium  )		// czy aktualna data konca premium większa niż aktualny czas serwera
		{
		echo "czas do końca premium: ".$roznica->format('%y lat, %m mies, %d dni , %h godz, %i min, %s sek');
		}
		else echo "Twoje konto premium wygasło: ". $roznica->format('%y rok, %m mies, %d dzień , %h godz, %i min, %s sek');
	?>
	
</body>
</html>