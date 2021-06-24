<?php
	
	session_start();								// pozwala dokumentowi korzystać z sesji
	
	if((!isset($_POST['login'])) || (!isset($_POST['haslo'])))		// jeśli ktoś wejdzie na stronę zaloguj.php "z palca"
	{
		header('Location: index.php');
		exit();
	}
	
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	require_once "connect.php";		// wrzuca plik connect.php do tego tutaj, zawsze raz, wymaga pliku do dalszej pracy
	try
	{
	$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);		// obj. otwiera połączenie z bazą
	
	if($polaczenie->connect_errno!=0)
	{
		// mamy błąd połączenia
		throw new Exception(mysqli_connect_errno()); 
	}
	else
	{
		// mamy połączenie z bazą danych
		$login = $_POST['login'];
		$haslo = $_POST['haslo'];
		
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");				//trzeba sprawdzić co wpisane żeby nie dało się zmienić zapytania sql
		//$haslo = htmlentities($haslo, ENT_QUOTES, "UTF-8");			//zamienia znaki specjalne < > ; ' na encje html &lt &st 
																												//encja html - wyświetla taki znak jak wpisany ale nie interpretowany
		$sql = "SELECT * FROM uzytkownicy WHERE user='$login' AND pass='$haslo'";
		// zapytanie w " " a zmienne php typu $str w ' '
		
		if ($rezultat = @$polaczenie->query(			// metoda query wysyła zapytanie, sprawdzamy czy przeszło poprawnie
		sprintf("SELECT * FROM uzytkownicy WHERE user='%s'",							 	// %s miejsce na zmienną s - string
		mysqli_real_escape_string($polaczenie, $login)))) 												// specjalna zabezpieczająca funkcja mysql_real_escape_string
		{
			$ilu_userow = $rezultat->num_rows;		// metoda query zwraca rekordy w bazie
			if($ilu_userow >0)										// sprawdzamy czy są wyniki dla danego loginu
			{
				$wiersz = $rezultat->fetch_assoc();
				
				if(password_verify($haslo, $wiersz['pass']))
				{
					
					$_SESSION['zalogowany'] = true;			// zapisujemy w sesji że jesteśmy zalogowani
							
					
					$_SESSION['id'] = $wiersz['id'];
					$_SESSION['user'] = $wiersz['user'];	// wczytaj rekord z bazy w kolumnie user do tablicy asocjacyjnej sesji
					$_SESSION['drewno'] = $wiersz['drewno'];
					$_SESSION['kamien'] = $wiersz['kamien'];
					$_SESSION['zboze'] = $wiersz['zboze'];
					$_SESSION['email'] = $wiersz['email'];
					$_SESSION['dnipremium'] = $wiersz['dnipremium'];
					
					header('Location: gra.php');					// przenieś na stronę zalogowanego urzytkownika
					unset($_SESSION['blad']);						// wywal z pamieci info jesli nastapiło wcześniej błędne logowanie
					$rezultat -> free_result();						// close();	free();	free_result();		zwalnia pamięć
				}
				else
				{																	
					$_SESSION['blad'] = '<span style = "color:red">błędny login lub hasło!</span>';		
					header('Location: index.php');				
				}
			}
			else
			{																	
				$_SESSION['blad'] = '<span style = "color:red">błędny login lub hasło!</span>';		
				header('Location: index.php');				
			}
			
		}			
		
		$polaczenie->close();		// zamyka połączenie z bazą danych
	}
	}
	catch(Exception $e)
			{
				echo '<span style="color:red;">"Błąd serwera, spróbuj ponownie później"</span>';		// info dla urzytkownika
				//echo '<br /><b><span style="color:black;">"Błąd połączenia: '.$e.'</span></b>"';		// info dla programisty
			}
	
?>