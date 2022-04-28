<?php 
	session_start();
	if (!empty($_POST['email']) AND !empty($_POST['password'])) {
		
		// Connexion à la BDD
		require_once('src/connexion.php');

		//Variables
		$email    = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);

		// L'adresse email est-elle correcte ?
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				
				header('location : index.php?error=true&message= Votre adresse email est invalide !');
				exit();
			}

		// Chiffrement du mot de passe
			$password = "aq1".sha1($_POST['password'."123"])."25";

			// L'adresse email est-elle déjà utilisée ?
			$requette = $bdd->prepare('SELECT COUNT(*) AS numbreEmail FROM user WHERE email = ?'); 
			$requette->execute([$email]);

			while ($emailVerification = $requette->fetch()) {
				
				if ($emailVerification['numbreEmail'] != 1) {
					
					header('location : index.php?error=true&message=Imposible de vous authifier correctement !');
					exit();
				}
			}

			// Connexion de l'utilisateur
			$requette = $bdd->prepare('SELECT * FROM user WHERE email = ?');
			$requette->execute([$email]);

			while($user = $requette->fetch()) {

				if ($password == $user['password']) {
					
					$_SESSION['connect'] = 1;
					$_SESSION['email']   = $user['email'];

					header('location : index.php?success=1');
					exit();

				}else{
					header('location : index.php?error=true&message=Imposible de vous authifier correctement !');
					exit();	
				}
			}
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

				<?php if(isset($_SESSION['connect'])) { ?>

					<h1>Bonjour !</h1>
					<?php
					if(isset($_GET['success'])){
						echo'<div class="alert success">Vous êtes maintenant connecté.</div>';
					} ?>
					<p>Qu'allez-vous regarder aujourd'hui ?</p>
					<small><a href="logout.php">Déconnexion</a></small>

				<?php } else { ?>
					<h1>S'identifier</h1>

					<?php if(isset($_GET['error'])) {

						if(isset($_GET['message'])) {
							echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
						}

					} ?>

					<form method="post" action="index.php">
						<input type="email" name="email" placeholder="Votre adresse email" required />
						<input type="password" name="password" placeholder="Mot de passe" required />
						<button type="submit">S'identifier</button>
						<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
					</form>
				

					<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
				<?php } ?>
		</div>
	</section>

	<?php require_once('src/footer.php'); ?>
</body>
</html>