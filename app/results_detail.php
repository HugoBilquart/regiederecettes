<?php 
    if($_POST) {
        if(isset($_POST['traitement'])) {
            switch ($_POST['traitement']) {
                case 'Valider':
                    $statut = 1;
                    break;
                
                case 'Annuler':
                    $statut = 2;
                    break;
            }

            $requete_traitement = $connex_pdo->prepare('UPDATE r_transactions SET statut = :statut, nom_traitement = :nom_traitement, date_traitement = :date_traitement ,commentaire = :commentaire WHERE id = :id');
            $requete_traitement->execute(array(
                "statut" => $statut,
                "nom_traitement" => $_SESSION['nom'],
                "date_traitement" => date('Y-m-d H:i:s'),
                "commentaire" => $_POST['commentaire'],
                "id" => $_GET['n']
            ));
        }
        else if(isset($_POST['rectifier'])) {
            $requete_traitement = $connex_pdo->prepare('UPDATE r_transactions SET statut = :statut, nom_traitement = :nom_traitement, date_traitement = :date_traitement ,commentaire = :commentaire WHERE id = :id');
            $requete_traitement->execute(array(
                "statut" => 0,
                "nom_traitement" => '',
                "date_traitement" => NULL,
                "commentaire" => '',
                "id" => $_GET['n']
            ));
        }
        
    }
?>

<form method="POST" action="regiederecettes.php?action=1&n=<?php echo $_GET['n']; ?>">
    <div class="form_part row" id="transaction">
        <p class="form-part-title text-center"><b>Informations de la transaction</b></p>
        <br/>
        <?php
            $requete = $connex_pdo->prepare('SELECT * FROM r_transactions WHERE id = :id');
            $requete->execute(array(
                'id' => $_GET['n']
            ));
            $transaction = $requete->fetch();
            if(empty($transaction)) {
                ?>
                    <p class='failed text-center'>Aucune transaction trouvée</p>
                <?php
            }
            else {
                //Informations de la transaction
                ?>
                    <ul>
                        <li><p>Transaction n°<?php echo $transaction['id']; ?></p></li>
                        <li><p>Saisie le <?php echo date_create($transaction['date_saisie'])->format('d/m/Y'); ?> par l'agent <?php echo $transaction['nom_agent']; ?></p></li>
                        <li><p><b>Etudiant : </b><?php echo $transaction['nom']; ?></p></li>
                        <?php
                        if($transaction['objet'] == 'Hébergements') {
                            $nbJour = date_diff(date_create($transaction['date_arrivee']),date_create($transaction['date_depart']))->format('%d');
                            if($nbJour > 1) {
                                ?>
                                    <li><p><b>Objet :</b> Hébergement pour 1 nuit</p></li>
                                <?php
                            }
                            else {
                                ?>
                                    <li><p><b>Objet :</b> Hébergement pour <?php echo $nbJour; ?> nuits</p></li>
                                <?php
                            }
                        }
                        else if($transaction['objet'] == "Autres Recettes") {
                            ?>
                                <li><p><b>Objet :</b> Autres Recettes : <?php echo $transaction['objet_detail'] ?></p></li>
                            <?php
                        }
                        else {
                            ?>
                                <li><p><b>Objet :</b> <?php echo $transaction['objet']; ?></p></li>
                            <?php
                        }

                        switch ($transaction['type']) {
                            case 'Numéraire':
                                $moyen = "en numéraire";
                                ?>
                                <li><p><b>Paiement :</b> <?php echo $transaction['montant'].' € '.$moyen; ?> </p></li>
                                <?php
                                break;

                            case 'Chèque':
                                $moyen = "par chèque";
                                $detail = explode(';',$transaction['paiement_detail']);
                                ?>
                                <li><p><b>Paiement :</b> <?php echo $transaction['montant']; ?> €</p></li>
                                <li><p><b>Chèque : </b><?php echo 'Chèque n°'.$detail[0].', '.$detail[1].', '.$detail[2]; ?></p></li>
                                <?php
                                break;
                            
                            default:
                                $moyen = "";
                                ?>
                                <li><p><b>Paiement :</b> <?php echo $transaction['montant'].' € '.$moyen; ?> </p></li>
                                <?php
                                break;
                        }
                        ?>
                        
                    </ul>
                    <?php
                ?>

                <?php
                    //Si la transaction est traitée
                    if($transaction['statut'] > 0) {
                        switch ($transaction['statut']) {
                            case 1:
                                $statut_str = "validée";
                                $t = "✔";
                                break;
                            
                            case 2:
                                $statut_str = "annulée";
                                $t = "✘";
                                break;
                        }
                        ?>
                            <hr>
                            <p class="form-part-title text-center"><b>Transaction <?php echo $statut_str.' '.$t; ?></b></p>
                            <p class="text-center">Traitée le <?php echo date_create($transaction['date_traitement'])->format('d/m/Y'); ?> par l'agent <?php echo $transaction['nom_traitement']; ?></p>
                        <?php
                            if($transaction['commentaire'] != '') {
                                ?>
                                    <p class="text-center">Note de traitement : <?php echo $transaction['commentaire']; ?></p>
                                <?php
                            }
                    }       
            }
        ?>

    </div>
    <br/>
    <div class="form_part row" id="transaction-traitement">
        <p class="form-part-title text-center"><b>Traitement</b></p>
        <?php 
            if($transaction['statut'] > 0) {
                ?>
                    <div class="text-center col-sm-">
                        <p><label>Commentaire :</label></p>
                        <p><?php echo $transaction['commentaire']; ?>
                    </div>
                    <br/>
                    <div class="text-center col-sm-" >
                        <input type="submit" class="btn btn-primary" name="rectifier" value="Rectifier">
                    </div>
                    <p class="indications text-center">Si vous rectifiez une transaction, le commentaire, le nom et la date de traitement seront effacés</p>
                <?php
            }
            else {
                ?>
                    <div class="text-center col-sm-" >
                        <p><label for="commentaire">Ajouter un commentaire <i>[facultatif]</i></label></p>
                        <textarea id="commentaire" name="commentaire" maxlength="255" resize="none" rows="4" cols="50"></textarea>
                    </div>
                    <br/>
                    <div class="text-center col-sm-">
                        <input type="submit" class="btn submit" name="traitement" value="Valider">
                        <input type="submit" class="btn submit" name="traitement" value="Annuler">
                    </div>
                <?php
            }
        ?>

    </div>
</form>

<hr>

<div class="return">
    <a href="regiederecettes.php?action=1&traitee=0" class="btn btn-default">
        <i class="fa fa-bars"></i> Retourner à la liste des transactions
    </a>
</div>