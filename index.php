<?php session_start(); ?> 

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="js/jquery.js"></script>
	<title>Regie de recettes - Authentification</title>
</head>
<body>
	<div class="login-page">
		<div class="form">
	        <form class="login-form" method="POST" id="login_content">
				<?php
					if($_SESSION) {
						?>
					        <p>Vous revoila <?php echo $_SESSION['prenom'].' '.$_SESSION['nom']; ?></p>
					        <p>Vous êtes déjà connecté</p>
					        <br><br>
					        <a href="accueil.php"><p>Cliquez ici pour accéder à l'intranet</p></a>
					    <?php
					}
					else {
						if($_POST) {
							if(empty($_POST['nom']) OR empty($_POST['passe'])) {
								?>
								<img src="img/icons/denied.png" alt="Accès refusé" class="access-icon"><p>Accès refusé</p>
				                <p>Identifiez vous !</p>
				                <br>
				                <a href="index.php"><p>Cliquez ici pour réessayer</p></a>
								<?php
							}
							else {
								include('db_connex.php');
								include('hash.php');
								$pass = crypt($_POST['passe'],$hash);
								$req = 'SELECT * FROM r_personnel WHERE `nom` = "'.$_POST["nom"].'" AND `code` = "'.$pass.'"';
								//echo $req;
								$request_login = $connex_pdo->query($req);
								$infos = $request_login->fetch();

								if(empty($infos)) {
									?>
									<img src="img/icons/denied.png" alt="Accès refusé" class="access-icon">
									<p class="failed">Accès refusé</p>
					                <p class="failed">Erreur sur le nom ou le mot de passe</p>
					                <br>
					                <?php include('loginForm.html'); ?>
					                
									<?php
								}
								else {
									$_SESSION['id'] = $infos['id'];
									$_SESSION['nom'] = $infos['nom'];
									$_SESSION['prenom'] = $infos['prenom'];
									$_SESSION['fonction'] = $infos['fonction'];
									$_SESSION['a_transactions'] = $infos['a_transactions'];
									$_SESSION['a_regie'] = $infos['a_regie'];

									//var_dump($_SESSION);

									?>
									<img src="img/icons/approved.png" alt="Accès refusé" class="access-icon">
									<p class="success">Accès accordé</p>
					                <a href="accueil.php">Cliquer ici pour accéder à l'application</a>
									<?php
								}
							}
						}	
						else {
							include('loginForm.html');
						}
					}
				?>
			</form>
      	</div>
	</div>
</body>
</html>