<?php session_start();
include('app/functions.php');

if($_SESSION) {
    include('db_connex.php');
    //Fichier liste des Etudiants
    $file = fopen('listeEtudiants.txt','r');

    $req_count = $connex_pdo->query('SELECT count(*) + 1 AS count FROM r_transactions');
    $numDisplay = $req_count->fetchColumn();

    if($numDisplay < 10) {
        $numDisplay = '00'.$numDisplay;
    }
    else if($numDisplay < 100) {
        $numDisplay = '0'.$numDisplay;
    }


    if($_POST) {
        $ts = 'Saisie le '.date('d/m/Y \à H:i');
    }
    else {
        $ts = '';
    }
}
else {
    redirectToIndex();
}


function createDate($date) {
    $tabMois = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre');
    $newDateParts = explode(" ", $date);
    $newDateMonth = array_search($newDateParts[1], $tabMois) + 1;
    if($newDateMonth < 10) $newDateMonth = '0'.$newDateMonth;

    $results = $newDateParts[2].'-'.$newDateMonth.'-'.$newDateParts[0];    

    return $results;
} 
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Extranet du personnel de l'ENSA Limoges">
        <title>Regie de recettes - Nouvelle transaction</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="shortcut icon" type="image/png" href="img/favicon.png" />
        <!-- SELECTEUR DATE ET HEURE -->
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
                    <h1>Régie de recettes - Nouvelle transactions</h1>
                    <p>Veuillez renseigner l'ensemble des champs de ce formulaire, <b>en respectant bien les indications en rouge !</b></p>
                    

                    <?php 
                    if($_SESSION['a_transactions'] != 1) {
                        ?>
                            <hr>
                            <p class="failed">Vous ne pouvez pas accéder à cette page.</p>
                        <?php
                    }
                    else {
                        ?>
                        <p class="indications">Toute transaction incorrectement saisie ne pourra pas être prise en compte !</p>
                        <hr>
                        
                        <!-- DEBUT Zone de traitement -->
                        <div class="form_part row">
                            <p class="form-part-title"><b>Informations sur la transaction</b></p>
                            <p><b>Transaction n°<?php echo $numDisplay; ?></b></p>
                            <p><b>Saisie par ⬜⬜⬜⬜<?php echo $_SESSION['nom']; ?></b></p>
                            <hr>
                            
                            <p><?php echo $ts; ?></p>
                            <?php
                                if($_POST) {
                                    if(empty($_POST['civ']) OR empty($_POST['etu']) OR empty($_POST['objet']) OR empty($_POST['objet']) OR empty($_POST['type']) OR empty($_POST['montant'])) {
                                        ?>
                                            <p class="failed">Erreur de saisie : La transaction n'a pas été saisie de manière conforme (civilité, nom, objet, type de paiement et montant)</p>
                                        <?php
                                    }
                                    else {
                                        //Precision du paiement si paiement par chèque
                                        if($_POST['type'] == 'Chèque') {
                                            //Verification de la présence des détails du paiement par chèque
                                            if(empty($_POST['num_cheque']) || empty(strip_tags($_POST['nom_banque'])) || empty(strip_tags($_POST['nom_cheque']))) {
                                                ?>
                                                <p class="failed">Erreur de saisie : Pour règler par chèque, indiquer le numéro de chèque, le nom de la banque et le nom du porteur du chèque.</p>
                                                <?php
                                            }
                                            else {
                                                $detailPaiement = $_POST['num_cheque'].';'.$_POST['nom_banque'].';'.$_POST['nom_cheque'];
                                            }
                                        }
                                        //Pas de précision pour un autre type de paiement
                                        else {
                                            $detailPaiement = '';
                                        }

                                        //Requête d'enregistrement d'un hébergement
                                        if($_POST['objet'] == "Hébergements") {
                                            //Verification de la présence des dates d'hebergement
                                            if(empty($_POST['date1']) OR empty($_POST['date2'])) {
                                                ?>
                                                    <p class="failed">Erreur de saisie : Les dates d'hébergements n'ont pas été saisies</p>
                                                <?php
                                            }
                                            else {
                                                ?>
                                                    <p class="success">La transaction est conformément saisie</p>
                                                <?php
                                                $date1 = createDate($_POST['date1']);
                                                $date2 = createDate($_POST['date2']);
                                                $detailObjet = "";
                                            }
                                        }
                                        //Requête d'enregistrement d'un divers
                                        else if($_POST['objet'] == 'Autres Recettes') {
                                            //Verification de la présence de la précision de l'objet
                                            if(empty(strip_tags($_POST['detail-objet']))) {
                                                ?>
                                                    <p class="failed">Erreur de saisie : Veuillez préciser l'objet de votre transaction</p>
                                                <?php
                                            }
                                            else {
                                                ?>
                                                    <p class="success">La transaction est conformément saisie</p>
                                                <?php
                                                $date1 = '';
                                                $date2 = '';
                                                $detailObjet = strip_tags($_POST['detail-objet']);
                                            }
                                        }
                                        //Enregistrement d'un autre objet
                                        else {
                                            ?>
                                                <p class="success">La transaction est conformément saisie</p>
                                            <?php
                                            $date1 = '';
                                            $date2 = '';
                                            $detailObjet = '';
                                        }

                                        //Enregistrement de la transaction
                                        $connex_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                        $req_transaction = $connex_pdo->prepare("INSERT INTO r_transactions (`date_saisie`,`nom_agent`,`civ`,`nom`,`objet`,`objet_detail`,`date_arrivee`,`date_depart`,`type`,`paiement_detail`,`montant`) VALUES (:dateSaisie,:agent,:civ,:nom,:objet,:objetDetail,:dateArrivee,:dateDepart,:paiement,:paiementDetail,:montant)");
                                        $reponse = $req_transaction->execute(array(
                                            ":dateSaisie" => date("Y-m-d H:i:s"),
                                            ":agent" => $_SESSION['nom'],
                                            ":civ" => $_POST['civ'],
                                            ":nom" => $_POST['etu'],
                                            ":objet" => $_POST['objet'],
                                            ":objetDetail" => $detailObjet,
                                            ":dateArrivee" => $date1,
                                            ":dateDepart" => $date2,
                                            ":paiement" => $_POST['type'],
                                            ":paiementDetail" => $detailPaiement,
                                            ":montant" => $_POST['montant']
                                        ));
                                        //Confirmation
                                        if($reponse) {
                                            ?>
                                                <p class="success">Transaction enregistrée avec succès.</p>
                                            <?php
                                        }
                                        else { 
                                            ?> 
                                                <p class="failed">Echec de l'enregistrement de la transaction</p>
                                            <?php
                                        }
                                    }
                                }
                            ?>
                        </div>
                        <!-- FIN Zone de traitement -->

                        <br/>
                        
                        <!-- DEBUT FORMULAIRE -->
                        <form id="transaction-form" name="transaction" method="POST">
                            <div class="form_part row" id="etu_part">
                                <p class="form-part-title"><b>Partie versante</b></p>
                                <p><b><label for="civ">Civilité</label></b>
                                <select name="civ" id="civ" class="type">
                                    <option value="" disabled selected hidden>Selectionner</option>
                                    <option value="M.">M.</option>
                                    <option value="Mme.">Mme.</option>
                                </select>

                                <p><b><label for="etu">Etudiant</label></b>
                                <input type="text" list="etudiants" id="etu" name="etu" size="50" placeholder="Saisissez le nom" class="form-control input-text"></p>
                                <datalist id="etudiants">
                                    <?php
                                        while(! feof($file)) {
                                            $currentLine = fgets($file);
                                            ?>
                                                <option value="<?php echo $currentLine; ?>"><?php echo $currentLine; ?></option>
                                            <?php
                                        }
                                        fclose($file);
                                    ?>
                                </datalist>
                                <p id="check_etu" class="failed"></p>
                            </div>
                            <br/>
                            <div class="form_part row" id="objet_part">
                                <p class="form-part-title"><b>Objet</b></p>
                                <div class="col-md-">
                                    <select id='objet' name="objet" class="type" onchange="objet_transaction()">
                                        <option value="" disabled selected hidden>Selectionner</option>
                                        <?php 
                                            //Affichage de certains OBJETS selon l'UTILISATEUR
                                            switch($_SESSION['nom']) {
                                                case 'comptable':
                                                    ?>
                                                    <option value="Concours d'Entrée">Concours d'Entrée</option>
                                                    <option value="Droits d'Inscription">Droits d'Inscription</option>
                                                    <option value="Droit d'Inscription Périscolaire">Droit d'Inscription Périscolaire</option>
                                                    <option value="Hébergements">Hébergements</option>
                                                    <option value="Infographie">Infographie</option>
                                                    <option value="Autres Recettes">Autres Recettes</option>
                                                    <?php
                                                    break;

                                                case 'sgénérale':
                                                    ?>
                                                    <option value="Concours d'Entrée">Concours d'Entrée</option>
                                                    <option value="Droits d'Inscription">Droits d'Inscription</option>
                                                    <option value="Droit d'Inscription Périscolaire">Droit d'Inscription Périscolaire</option>
                                                    <option value="Hébergements">Hébergements</option>
                                                    <option value="Infographie">Infographie</option>
                                                    <option value="Autres Recettes">Autres Recettes</option>
                                                    <?php
                                                    break;

                                                case 'informatique':
                                                    ?>
                                                    <option value="Infographie">Infographie</option>
                                                    <?php
                                                    break;

                                                case 'technique':
                                                    ?>
                                                    <option value="Infographie">Infographie</option>
                                                    <?php
                                                    break;

                                                case 'secretaire':
                                                    ?>
                                                    <option value="Droit d'Inscription Périscolaire">Droit d'Inscription Périscolaire</option>
                                                    <?php
                                                    break;

                                                case 'logistique':
                                                    ?>
                                                    <option value="Hébergements">Hébergements</option>
                                                    <?php
                                                    break;
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-" id="objet-details">
                                    <label for="date1" class="date-label">Date d'arrivée</label>
                                    <div class="input-group date form_date col-md-5 datePourHeberg" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="dd MM yyyy">
                                        <input class="form-control" id="date1" name="date1" type="text" value="" readonly disabled onchange="objetDate()"> 
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        <input type="hidden" id="dtp_input1" name="date1" value="">
                                    </div>

                                    <label for="date2" class="date-label">Date de départ</label>
                                    <div class="input-group date form_date col-md-5 datePourHeberg" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="dd MM yyyy">
                                        <input class="form-control" id="date2" name="date2" type="text" value="" readonly disabled onchange="objetDate()">
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        <input type="hidden" id="dtp_input2" name="date2" value="">
                                    </div>
                                    
                                    <label for="detail-objet" class="precision-label">Précision sur l'objet</label>
                                    <input type="text" id="detail-objet" name="detail-objet" size="50" placeholder="Saisissez votre objet" class="form-control input-text">
                                </div>

                                <p class="indication">Si vous selectionnez l'objet <b>Hébergements</b>, renseignez les dates</p>
                                <p class="indication">Si vous selectionnez l'objet <b>Autres Recettes</b>, précisez le dans la zone de texte prévue</p>
                                <p class="indication">Cliquez dans la zone de saisie pour afficher le calendrier de sélection. Sur le calendrier cliquez à gauche ou à droite du mois en cours pour changer de mois.</p>
                                <p id="check_obj" class="check failed"></p>
                            </div>
                            <br/>
                            <div class="form_part row" id="paiement_part">
                                <p class="form-part-title"><b>Paiement</b></p>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <input type="radio" name="type" value="Numéraire">
                                        <label class="custom-control-label">Numéraire</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="radio" name="type" value="Chèque">
                                        <label class="custom-control-label">Chèque</label>
                                    </div>
                                    <div class="col-sm-2">
                                    <input type="radio" name="type" value="Virement">
                                            <label class="custom-control-label">Virement</label>
                                    </div>                            
                                    <div class="col-sm-2">
                                        <input type="radio" name="type" value="CB">
                                        <label class="custom-control-label">Carte Bancaire</label>
                                    </div>

                                    <div class="col-sm-12" id="detail-paiement">
                                        <p><b></b></p>
                                        <div class="col-sm-3 detail-paiement-cheque">
                                            <label for="num_cheque" class="precision-paiement-label detail-paiement-label">Numéro de chèque</label>
                                            <input type="text" id="num_cheque" name="num_cheque" size="30" placeholder="Numéro du chèque" class="form-control">
                                        </div>
                                        <div class="col-sm-3 detail-paiement-cheque"> 
                                            <label for="nom_banque" class="precision-paiement-label detail-paiement-label">Nom de la banque</label>
                                            <input type="text" id="nom_banque" name="nom_banque" size="30" placeholder="Nom de la banque" class="form-control input-text">
                                        </div>
                                        <div class="col-sm-3 detail-paiement-cheque"> 
                                            <label for="nom_cheque" class="precision-paiement-label detail-paiement-label">Nom du porteur </label>
                                            <input type="text" id="nom_cheque" name="nom_cheque" size="30" placeholder="Nom du porteur du chèque" class="form-control input-text">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="detail-paiement-label" for="input_montant">Montant</label>
                                            <input class="form-control" type="number" name="montant" id="input_montant" step="0.01" min="0" title="Utiliser un point pour les décimales" required>
                                        </div>
                                    </div>
                                </div>
                                <p class="indication">Sélectionnez un moyen de paiement et saisissez le montant</p>
                                <p class="indication">Pour les paiement par chèque, renseignez le numéro du chèque, la banque et le nom du porteur</p>
                                <p class="indication">Pour l'objet "Hébergements", le montant sera défini automatiquement</p>
                                <p class="indication">Si le montant avec une virgule n'est pas validé, essayez avec un point et inversement</p>
                                <p id="check_paie" class="check failed"></p>
                            </div>
                            <br/>
                            <div class="form_part row submit_part" style="text-align: center;">
                                <input type="button" class="btn btn-light" name="check" value="Verifier" id="checkform">
                                <input type="button" class="btn btn-light" name="check" value="Recommencer" onclick="location.reload();">
                                <input type="submit" class="btn btn-primary submit" value="Envoyer" id="submit" disabled>
                            </div>
                            <p id="endcheck" class="indication">Vous devez vérifier la transaction avant de la soumettre</p>
                        </form>
                        <!-- FIN FORMULAIRE -->
                    <?php
                    }
                    ?>
                    <?php include '_pieddepagebis.php'; ?>
                </div>
            </main>
    </div>
            <!-- fin contenu page -->

            <!-- js pour le menu -->
        <script src='js/jquery.js'></script>
        <!--script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script src="js/custom.js"></script-->
        <script src="dateheureselecteur/bootstrap-datetimepicker.js"></script>
        <script src="dateheureselecteur/bootstrap-datetimepicker.fr.js"></script>
        <script>
            const prixNuit = 25;
            var mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'];
            var nb_checkbox = 0;
            
            function newDate(value) {
                var dateParts = value.split(" ");
                var dateMois = mois.indexOf(dateParts[1]) + 1;
                var date = new Date(dateParts[2] + '-' + dateMois + '-' + dateParts[0]);
                return date;
            }

            function objetDate() {
                $date1 = $('#date1').val();
                $date2 = $('#date2').val();

                if($date1 && $date2) {
                    var date1 = newDate($('#date1').val());
                    var date2 = newDate($('#date2').val());

                    var difference = dateDiffInDays(date1, date2);

                    if(difference <= 0) {
                        $('#check_obj').text("Veuillez saisir des dates cohérentes");

                        document.getElementById('input_montant').value = null; 
                    }
                    else if(difference == 1) {
                        $('#check_obj').text("Régler un hebergement de " + difference + " jour");

                        document.getElementById('input_montant').value = difference * prixNuit;
                    }
                    else {
                        $('#check_obj').text("Régler un hebergement de " + difference + " jours");

                        document.getElementById('input_montant').value = difference * prixNuit;  
                    }
                }
            }

            function dateDiffInDays(a, b) {
                const _MS_PER_DAY = 1000 * 60 * 60 * 24;

                const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
                const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

                return Math.floor((utc2 - utc1) / _MS_PER_DAY);
            }

            function objet_transaction() {
                var objet = document.getElementById('objet');
                if(objet.value == "Hébergements" || objet.value == "Autres Recettes") {
                    $('#objet-details').css('display','block');
                    if(objet.value == "Hébergements") {
                        //Afficher les titres et input des dates
                        $('.date-label').css('display','block');
                        $('.datePourHeberg').css('visibility','visible');

                        //Cacher le titre et l'input precision
                        $('.precision-label').css('display','none');
                        $('#detail-objet').css('display','none');

                        //Bloquer l'input du montant
                        $('#input_montant').attr('readonly','true');
                        $('#input_montant').css('background-color','#b2b2b2');

                        objetDate();
                    }
                    if(objet.value == "Autres Recettes") {
                        //Cacher les dates
                        $('.date-label').css('display','none');
                        $('.datePourHeberg').css('visibility','collapse');

                        //Afficher l'input precision
                        $('.precision-label').css('display','block');
                        $('#detail-objet').css('display','block');

                        //Rendre input montant utilisable
                        $('#input_montant').removeAttr('readonly');
                        $('#input_montant').css('background-color','#FFFFFF');
                    }
                }
                else {
                    //Cacher les dates et precisions
                    $('#objet-details').css('display','none');

                    $('.precision-label').css('display','none');
                    $('#detail-objet').css('display','none');

                    $('.date-label').css('display','none');
                    $('.datePourHeberg').css('visibility','collapse');

                    //Rendre input montant utilisable
                    $('#input_montant').removeAttr('readonly');
                    $('#input_montant').css('background-color','#FFFFFF');
                }
            }

            $('[name="type"]').click(function(){
                console.log($(this).val());
                if($(this).val() == 'Chèque') {
                    $('.detail-paiement-cheque').css('display','block');
                }
                else {
                    $('.detail-paiement-cheque').css('display','none');
                }

            });

            $('[name="montant"]').change(function(){
                parseFloat($(this).val()).toFixed(2);
                console.log($('[name="montant"]').val());
            });

            function disableSubmitButton() {
                $('.form_part').css('border','none');
                $('.check').text('');
                document.getElementById('submit').setAttribute('disabled','true');
            }

            function enableSubmitButton() {
                document.getElementById('submit').removeAttribute('disabled');
            }

            $('#checkform').click(function(){
                $errorCount = 0;
                $('.check').text('');
                $('.form_part').css('border','');
                $type = $('input[name="type"]:checked');
            

                if($('[name="civ"]').val() == null || $('[name="etu"]').val() == '') {
                    $('#check_etu').text("Veuillez renseigner la civilité et le nom de l'étudiant");
                    $('#etu_part').css('border','solid red');
                    $errorCount++;
                }
                else {
                    $('#check_etu').text("");
                    $('#etu_part').css('border','solid green');
                }

                if($('[name="objet"]').val() != "") {
                    if($('[name="objet"]').val() == 'Hébergements') {
                        if($('#date1').val() == '' || $('#date2').val() == '') {
                            $('#check_obj').text(`Veuillez renseigner les dates pour l'objet "Hébergements"`);
                            $('#objet_part').css('border','solid red');
                            $errorCount++;
                        }
                        else {
                            var difference = dateDiffInDays(newDate($('#date1').val()), newDate($('#date2').val()));
                            if(difference <= 0) {
                                $('#check_obj').text("Veuillez saisir des dates cohérentes");
                                $errorCount++;
                            }
                            else if(difference == 1) {
                                $('#check_obj').text("Régler un hebergement d'un jour");
                                $('#objet_part').css('border','solid green');
                            }
                            else {
                                $('#check_obj').text("Régler un hebergement de " + difference + " jours");
                                $('#objet_part').css('border','solid green'); 
                            }
                        }
                    }
                    else if($('[name="objet"]').val() == 'Autres Recettes') {
                        if($('#detail-objet').val() == '') {
                            $('#check_obj').text(`Veuillez préciser l'objet de votre transaction`);
                            $('#objet_part').css('border','solid red');
                            $errorCount++;
                        }
                        else {
                            $('#check_obj').text("");
                            $('#objet_part').css('border','solid green'); 
                        }
                    }
                    else {
                        if($('#date1').val() != '' || $('#date2').val() != '') {
                            $('#check_obj').text(`Les dates saisies ne seront prises en comptes que si vous sélectionnez l'objet "Hébergements"`);
                            $('#objet_part').css('border','solid orange');
                        }
                        else {
                            $('#objet_part').css('border','solid green');
                        }
                    }
                }
                else {
                    $('#check_obj').text("Veuillez renseigner l'objet de la transaction");
                    $('#objet_part').css('border','solid red');
                    $errorCount++;
                }

                if($type.val() == '') {
                    $('#check_paie').text("Veuillez renseignez le moyen de paiement");
                    $('#paiement_part').css('border','solid red');
                    if($('[name="montant"]').val() == '') {
                        $('#check_paie').text("Veuillez renseignez le moyen de paiement et le montant");
                        $('#paiement_part').css('border','solid red');
                        
                    }
                    $errorCount++;
                }
                else {
                    if($type.val() == "Chèque") {
                        if($('input[name="nom_banque"]').val() == '' || $('input[name="num_cheque"]').val() == '' || $('input[name="nom_cheque"]').val() == '') {
                            $('#check_paie').text("Pour règler par chèque, renseignez son numéro, le nom de la banque et le nom du porteur");
                            $('#paiement_part').css('border','solid red');
                            $errorCount++;
                        }
                    }
                    if($('[name="montant"]').val() == '') {
                        $('#check_paie').text("Renseignez le montant de la transaction");
                        $('#paiement_part').css('border','solid red');
                        $errorCount++;
                    }
                    else if($('[name="montant"]').val() == 0){
                        $('#check_paie').text("Renseignez un montant valide");
                        $('#paiement_part').css('border','solid red');
                        $errorCount++;
                    }
                    else {
                        $('#paiement_part').css('border','solid green'); 
                    }
                }
                

                if($errorCount > 0) {
                    $('#submit').attr('disabled','true');
                }
                else {
                    $('#submit').removeAttr('disabled');
                }
            });

            $('select').change(function(){
                disableSubmitButton();
                $('.check_result').text('');
            });

            $('input').change(function(){
                disableSubmitButton();
            });

            $('').change(function(){
                disableSubmitButton();
            });

            $('input').keyup(function(){
                disableSubmitButton();
            });



            $(document).ready(function(){
            $('body').show();
            });

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

        </script>
    </body>
</html>