<?php session_start();
    include('functions.php');
    //include('regie/functions.php');
    if($_SESSION) {
        if (isset($_SESSION['id']))   // si valeur dans sessions alors on affiche la page
        {
            include('db_connex.php');
            $numpers=$_SESSION['id'];
            $nom=$_SESSION['nom'];
            $prenom=$_SESSION['prenom'];
        }
        else {
            redirectToIndex();
        }
    }
    else {
        redirectToIndex();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Regie de recettes - Accueil</title>
            <link rel="stylesheet" href="css/bootstrap.min.css">
            <link rel="stylesheet" href="css/font-awesome.min.css">
            <link rel="stylesheet" href="css/style.css">
            <link rel="shortcut icon" type="image/png" href="img/favicon.png" />
            <script src='js/jquery.js'></script>

            <link href="dateheureselecteur/bootstrap.min.css" rel="stylesheet" media="screen">
            <link href="dateheureselecteur/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
        </head>
    </head>
    <body>
        <div class="page-wrapper chiller-theme toggled">
            <?php include '_menu.php'; ?>

            <!-- Contenu page  -->
            <main class="page-content">
                <div class="container-fluid">
                    <img src="img/entete.jpg" style="max-width: 1000px; min-width: 500px;" alt="Image d'illustration" />
                    <h1>Régie de recettes - Accueil</h1>
                    <p>En temps que <?php echo $_SESSION['fonction'] ?>, vous pouvez utiliser cet outil pour :</p>
                    <ul>
                        <?php 
                            if($_SESSION['a_transactions'] == 1) {
                                ?>
                                <li>Saisir une transaction étudiant pour les objets :
                                    <ul>
                                        <?php
                                        switch($_SESSION['nom']) {
                                            case 'comptable':
                                                ?>
                                                <li>Concours d'Entrée</li>
                                                <li>Droits d'Inscription</li>
                                                <li>Droit d'Inscription Périscolaire</li>
                                                <li>Hébergements</li>
                                                <li>Infographie</li>
                                                <li>Autres Recettes</li>
                                                <?php
                                                break;

                                            case 'sgénérale':
                                                ?>
                                                <li>Concours d'Entrée</li>
                                                <li>Droits d'Inscription</li>
                                                <li>Droit d'Inscription Périscolaire</li>
                                                <li>Hébergements</li>
                                                <li>Infographie</li>
                                                <li>Autres Recettes</li>
                                                <?php
                                                break;

                                            case 'informatique':
                                                ?>
                                                <li>Infographie</li>
                                                <?php
                                                break;

                                            case 'technique':
                                                ?>
                                                <li>Infographie</li>
                                                <?php
                                                break;

                                            case 'secretaire':
                                                ?>
                                                <li>Droit d'Inscription Périscolaire</li>
                                                <?php
                                                break;

                                            case 'logistique':
                                                ?>
                                                <li value="Hébergements">Hébergements</li>
                                                <?php
                                                break;
                                        }
                                        ?>
                                    </ul>
                                </li>
                                <?php
                            }
                        ?>
                        <br/>
                        <?php
                            if($_SESSION['a_regie'] == 1) {
                                ?>
                                    <li>Consulter les transactions saisies</li>

                                    <li>Valider ou annuler une transaction</li>

                                    <li>Générer un reçu ou un récapitulatif des transactions</li>
                                <?php
                            }
                        ?>
                    </ul>
                    <?php include '_pieddepagebis.php'; ?>
                </div>
            </main>
        </div>
    </body>
</html>