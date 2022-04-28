<?php 
	session_start();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	// on peut utiliser (isset ou !empty)
	if (isset($_POST['email']) AND isset($_POST['password']) AND isset($_POST['password_two'])) {
			//Connexion à lqa la BDD
			require_once('src/connexion.php');

			// Variables
			$email 			= htmlspecialchars($_POST['email']);
			$password 		= htmlspecialchars($_POST['password']);
			$password_two 	= htmlspecialchars($_POST['password_two']);

			// Les mots de passe sont-ils différents ?
			if ($password != $password_two) {
				
				header('location : inscription.php?error=true&message=Les deux mot de passe ne correspondent pas !');
				exit();
			}

			// L'adresse email est-elle correcte ?
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				
				header('location : inscription.php?error=true&message= Votre adresse email est invalide !');
				exit();
			}

			// L'adresse email est-elle déjà utilisée
			$requette = $bdd->prepare('SELECT COUNT(*) AS numbreEmail FROM user WHERE email = ?'); 
			$requette->execute([$email]);

			while ($emailVerification = $requette->fetch()) {
				
				if ($emailVerification['numbreEmail'] != 0) {
					
					header('location : inscription.php?error=true&message=adresse email déjà ulisée par un autre utilisateur !');
					exit();
				}
			}
			
			// Chiffrement du mot de passe
			$password = "aq1".sha1($_POST['password'."123"])."25";

			// Génération de secret
			$secret = sha1($email).time();
			$secret = sha1($email).time();

			// Ajouter un utilisateur
			$requette = $bdd->prepare('INSERT INTO user(email, password, secret) VALUES(?, ?, ?)');
			$requette->execute([$email, $password, $secret]);
			
			header('location: inscription.php?success=1');
			exit();		
	}
 ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>

	<?php require_once('src/header.php'); ?>
	
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>
			<?php 
			if (isset($_GET['error']) AND isset($_GET['message'])) {
				
				echo '<div class="alert error"> '.htmlspecialchars($_GET['message']).'</div>';
			}else if (isset($_GET['message'])) {
				
				echo '<div class="alert success"> Vous êtes désormais inscrit. <a href= "idex.php"> Connez-vous <a/>.</div>';
			}

			 ?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php require_once('src/footer.php'); ?>
</body>
</html>