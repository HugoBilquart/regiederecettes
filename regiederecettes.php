<?php session_start();
    include('functions.php');
    include('app/functions.php');
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
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Regie de recettes - Gestion des transactions</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="shortcut icon" type="image/png" href="img/favicon.png" />
        <script src='js/jquery.js'></script>

        <link href="dateheureselecteur/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="dateheureselecteur/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    </head>

    <body>

        <div class="page-wrapper chiller-theme toggled">
        
            <?php include '_menu.php'; ?>

            <!-- Contenu page  -->
            <main class="page-content">
                <div class="container-fluid">
                        <img src="img/entete.jpg" style="max-width: 1000px; min-width: 500px;" alt="Image d'illustration" />
                        <h1>Régie de recettes</h1>
                        <p>Gestion des transactions et génération de documents</p>
                        <hr>
                        <?php
                            if($_SESSION['a_regie'] != 1) {
                        ?>
                                <p class="failed">Vous ne pouvez pas accéder à cette page.</p>
                        <?php
                            }
                            else if(!isset($_GET['action'])) {
                        ?>
                                <h2>Que voulez vous faire ?</h2>
                                <table class="regie_tableau">
                                    <tr>
                                        <td>
                                            <figure>
                                                <a href="regiederecettes.php?action=1&traitee=0">
                                                    <img src="img/icons/icone_modifier.png" class="regie" alt="icone-modifier">
                                                </a>
                                                <figcaption>Gérer les transactions etudiants</figcaption>
                                            </figure>
                                        </td>
                                        <td>
                                            <figure>
                                                <a href="regiederecettes.php?action=2">
                                                    <img src="img/icons/recu.png" class="regie" alt="icone-reçu">
                                                </a>
                                                <figcaption>Generer un reçu</figcaption>
                                            </figure>
                                        </td>
                                        <td>
                                            <figure>
                                                <a href="regiederecettes.php?action=3">
                                                    <img src="img/icons/pdf.png" class="regie" alt="icone-recapitulatif">
                                                </a>
                                                <figcaption>Obtenir un récapitulatif</figcaption>
                                            </figure>
                                        </td>
                                    </tr>
                                </table>
                        <?php
                            }
                            else {
                                switch ($_GET['action']) {
                                    case 1:
                                        include('app/results.php');
                                        break;
                                    case 2:
                                        include('app/genererrecu.php');
                                        break;
                                    case 3:
                                        include('app/obtenirrecap.php');
                                        break;
                                    default:
                                        include('app/documents.php');
                                        break;
                                }
                            }
                        ?>

                        <?php include '_pieddepagebis.php'; ?>
                </div>
            </main>
            <!-- fin contenu page -->

        </div>

            <!-- js pour le menu -->
        <script src="dateheureselecteur/bootstrap.min.js"></script>
        <script src="dateheureselecteur/bootstrap-datetimepicker.js"></script>
        <script src="dateheureselecteur/bootstrap-datetimepicker.fr.js"></script>
        <script>
            $('.form_datetime').datetimepicker({
                language:  'fr',
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0,
                showMeridian: 1
            });
            $('.form_date').datetimepicker({
                language:  'fr',
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0
            });
            $('.form_time').datetimepicker({
                language:  'fr',
                weekStart: 1,
                todayBtn:  0,
                autoclose: 1,
                todayHighlight: 1,
                startView: 1,
                minView: 0,
                maxView: 1,
                forceParse: 0
            });

            $days_options = {
                language:  'fr',
                format: 'dd MM yyyy',
                minDate : '2000-01-01',
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0
            };
            $months_options = {
                language:  'fr',
                format: 'MM yyyy',
                minDate : '2000-01',
                todayBtn:  "Ce mois",
                autoclose: 1,
                todayHighlight: 1,
                startView: 3,
                minView: 3,
                forceParse: 0,
            };
            $years_options = {
                language:  'fr',
                format: 'yyyy',
                minDate : '2000',
                todayBtn:  "Cette Année",
                autoclose: 1,
                todayHighlight: 1,
                startView: 4,
                minView: 4,
                forceParse: 0,
            };



            $('#search').click(function(){
                $('#results-area').show();

                //$type = $('input[name="type"]:checked').val();
                //$type = $('#form-document').attr('name')
                switch ($('#form-document').attr('name')) {
                    case 'recu':
                        $type = 1;
                        break;
                
                    case 'recap':
                        $type = 2;
                        break;
                }

                if($type == 1) {
                    $date = $('input[name="date"]').val();
                    $nom = $('input[name="nom"]').val();
                    
                    $.post(
                        'recherchedocument.php',
                        {
                            type : $type,
                            date : $date,
                            nom : $nom
                        },
                        function(data) {
                            document.getElementById("results").innerHTML 
                                = data; 
                        },
                        'text'
                    );
                }
                else if($type == 2) {
                    $periode = $('input[name="type"]:checked').val();
                    if($periode == 4) {
                        $date = $('input[name="date"]:empty').val() + ';' + $('input[name="date2"]:empty').val();
                    }
                    else {
                        $date = $('input[name="date"]:empty').val();
                    }

                    $objet = $('#objet').val();
                    $moyen_paiement = $('#type-paiement').val();

                    if($objet == '' || $moyen_paiement == '') {
                        document.getElementById("results").innerHTML = "<p class='failed'>Indiquer l'objet et le moyen de paiement</p>";
                    }
                    else {
                        $.post(
                            'recherchedocument.php',
                            {
                                type : $type,
                                date : $date,
                                periode : $periode,
                                objet : $objet,
                                moyen_paiement : $moyen_paiement
                            },
                            function(data) {
                                document.getElementById("results").innerHTML 
                                    = data; 
                            },
                            'text'
                        );
                    }
                } 
            });

            function getRecap($type,$date,$periode) {
                console.log('Pqeruigsvfdo');
                
            }
        </script>
    </body>