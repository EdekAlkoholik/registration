<?php

		session_start();	
		
		if (isset($_POST['nick']))											// czy wcisnięto "zatwierdz" i wysłano zmienną nawet jeśli pusta
		{
			$jestOk = true;													// zmienna potwierdza czy jest ok
			
			$nick = $_POST['nick'];
			if((strlen($nick) < 3) || (strlen($nick) > 20))		// sprawdza długość nicku
			{
				$jestOk = false;
				$_SESSION['error_nick'] = "błędny nick, poprawny nick od 3 do 20 znaków";
			}
			
			if(ctype_alnum($nick)==false)
			{
				$jestOk = false;
				$_SESSION['error_nick'] = "błędny nick, poprawny nick zawiera tylko litery i cyfry, bez polskich znaków";
			}
			$email=$_POST['email'];
			$emailB = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);		// sanityzacja kodu - sprawdzamy poprawność podanych danych
			
			if((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email))
			{
				$jestOk = false;
				$_SESSION['error_email'] = "błędny mail, proszę o wprowadznie poprawnego emaila";
			}
			
			$pass1=$_POST['pass1'];
			$pass2=$_POST['pass2'];
			
			if(strlen($pass1) < 8 || strlen($pass1) > 20)
			{
				$jestOk = false;
				$_SESSION['error_pass'] = "błędne hasło, poprawne hasło od 3 do 20 znaków";
			}
			
			if($pass1 != $pass2)
			{
				$jestOk = false;
				$_SESSION['error_pass'] = "podane hasła nie są identyczne";
			}
			
			$haslo_hash = password_hash($pass1, PASSWORD_DEFAULT);					// wykorzystuje aktualny algorytm php do hashowiania hasla
			
			if(!isset($_POST['regulamin']))
			{
				$jestOk = false;
				$_SESSION['error_regulamin'] = "wymagana jest akceptacja regulaminu";
			}
			
			$klucz = "6LchZQwbAAAAAMeRizgAUrDmQY4u1ZtYuHJr2tAK";
			$testCaptcha = file_get_contents(
			'https://www.google.com/recaptcha/api/siteverify?secret='.$klucz.'&response='.$_POST['g-recaptcha-response']);
			
			$odpowiedzCaptcha = json_decode($testCaptcha);
			
			if($odpowiedzCaptcha->success==false)
			{
				$jestOk = false;
				$_SESSION['error_captcha'] = "potwierdz że nie jesteś robotem !";
			}
			
			$_SESSION['last_nick']=$nick;
			$_SESSION['last_email']=$email;
			$_SESSION['last_pass1']=$pass1;
			$_SESSION['last_pass2']=$pass2;
			if(isset($_POST['regulamin']))
			{
				$_SESSION['last_regulamin']=true;
			}
			
			
			require_once "connect.php";
			
			mysqli_report(MYSQLI_REPORT_STRICT);		// informujemy że chcemy sami obsługiwać wyjątki w try/catch
			
			try
			{
				$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);	
				if($polaczenie->connect_errno!=0)
				{
					throw new Exception(mysqli_connect_errno());
				}
				else	
				{
					
					//sprawdzamy dostępdnośc maila
					$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");
					
					if (!$rezultat) throw new Exception ($polaczenie->error);		// jeśli zapytanie się nie (!) uda wyrzuć błąd
					
					$ile_maily = $rezultat->num_rows;
					if($ile_maily > 0)
					{
						$jestOk = false;
						$_SESSION['error_email'] = "Użytkownik o podanym adresie e-mail już istnieje";
					}
					
					//sprawdzamy dostępdnośc nicku
					$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE user='$nick'");
					
					if (!$rezultat) throw new Exception ($polaczenie->error);		
					
					$ile_userow = $rezultat->num_rows;
					if($ile_userow > 0)
					{
						$jestOk = false;
						$_SESSION['error_nick'] = "Użytkownik o podanym nicku już istnieje";
					}
					
					if($jestOk == true)
					{
						// udało się możemy zarejestrować użytkownika
						if($polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL, '$nick', '$haslo_hash', '$email', 100, 100, 100, now() + INTERVAL 14 DAY)"))  // dla każdego nowego użytkownika ustawiamy czas wygaśnięcia premium za 14 dni
						{
							$_SESSION['udanarejestracja'] = true;
							header('Location: witamy.php');
						}
						else
						{
							throw new Exception ($polaczenie->error);
						}
					}
					
					$polaczenie->close();	
				}
			}
			catch(Exception $e)
			{
				echo '<span style="color:red;">"Błąd serwera, spróbuj ponownie później"</span>';		// info dla urzytkownika
				//echo '<br /><b><span style="color:black;">"Błąd połączenia: '.$e.'</span></b>"';		// info dla programisty
			}
			
			
		}
		
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-eqiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Plemiona - załóż nowe plemie</title>
	
	<script src='https://www.google.com/recaptcha/api.js'></script>		
	
	<style>
		.error
		{
			color:red;
			margin-top 10px;
			margin-bottom 10px;
		}
	</style>
	
</head>
<body>
	 
	 <form method = "post">
			
			
			
			Nickname: </br> <input type="text"  value="<?php
				if(isset($_SESSION['last_nick']))
				{
					echo $_SESSION['last_nick'];
					unset ($_SESSION['last_nick']);
				}
			?>" name="nick" /> </br>
			
			<?php
				if(isset($_SESSION['error_nick']))
				{
					echo '<div class="error">'.$_SESSION['error_nick'].'</div>';
					unset($_SESSION['error_nick']);
				}
			?>
			
			Adres e-mail: </br> <input type="text" value="<?php
				if(isset($_SESSION['last_email']))
				{
					echo $_SESSION['last_email'];
					unset ($_SESSION['last_email']);
				}
			?>" name="email" /> </br>
			
			<?php
				if(isset($_SESSION['error_email']))
				{
					echo '<div class="error">'.$_SESSION['error_email'].'</div>';
					unset($_SESSION['error_email']);
				}
			?>
			
			Hasło: </br> <input type="password" value="<?php
				if(isset($_SESSION['last_pass1']))
				{
					echo $_SESSION['last_pass1'];
					unset ($_SESSION['last_pass1']);
				}
			?>" name="pass1" /> </br>
			
			<?php
				if(isset($_SESSION['error_pass']))
				{
					echo '<div class="error">'.$_SESSION['error_pass'].'</div>';
					unset($_SESSION['error_pass']);
				}
			?>
			
			Powtórz hasło: </br> <input type="password" value="<?php
				if(isset($_SESSION['last_pass2']))
				{
					echo $_SESSION['last_pass2'];
					unset ($_SESSION['last_pass2']);
				}
			?>" name="pass2" /> </br>
			
			<label>
			<input type="checkbox" "  name="regulamin" <?php
				if(isset($_SESSION['last_regulamin']))
				{
					echo "checked";
					unset ($_SESSION['last_regulamin']);
				}
			?>/> Akceptuje regulamin </br>
			</label>
			
			<?php
				if(isset($_SESSION['error_regulamin']))
				{
					echo '<div class="error">'.$_SESSION['error_regulamin'].'</div>';
					unset($_SESSION['error_regulamin']);
				}
			?>
			
			<div class="g-recaptcha" data-sitekey="6LchZQwbAAAAANyYL4lYPFmybfAAL5uELB_9TMd4"></div>
			
			<?php
				if(isset($_SESSION['error_captcha']))
				{
					echo '<div class="error">'.$_SESSION['error_captcha'].'</div>';
					unset($_SESSION['error_captcha']);
				}
			?>
			
			</br> <input type="submit" value="Zatwierdz" />
			
			
	 </form>
	
</body>
</html>