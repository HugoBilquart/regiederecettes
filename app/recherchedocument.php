
        
<?php
    include('db_connex.php');
    include('app/functions.php');
    include('functions.php');

            //Pas de type de document indiqué
            if(empty($_POST['type'])) {
                echo '<p class="failed">Veuillez indiquer quel type de formulaire vous cherchez</p>';
            }
            //Type de document indiqué
            else {
                switch ($_POST['type']) {
                    //DEBUT Obtenir un reçu
                    case 1:
                        if(empty($_POST['date']) && empty($_POST['nom'])) {
                            echo '<p class="failed">Pour obtenir un reçu, veuillez saisir une date ou un nom d\'étudiants</p>';
                        }
                        else {
                            //Si date indiquée et nom d'étudiant pas indiqué
                            if(!empty($_POST['date']) AND empty($_POST['nom'])) {
                                $date_db = date('Y-m-d',strtotime($_POST['date']));
                                $req = 'SELECT * FROM r_transactions WHERE date_saisie LIKE "'.$date_db.'%"';
                            }
                            //Si date pas indiquée et nom d'étudiant indiqué
                            else if(empty($_POST['date']) AND !empty($_POST['nom'])) {
                                $req = 'SELECT * FROM r_transactions WHERE nom LIKE "%'.$_POST['nom'].'%"';
                            }
                            //Si date indiquée et nom d'étudiant indiqué
                            else {
                                $date_db = date('Y-m-d',strtotime($_POST['date']));

                                $req = 'SELECT * FROM r_transactions WHERE date_saisie LIKE "'.$date_db.'T%" AND nom LIKE "%'.$_POST['nom'].'%"';
                            }
                            $reponse = $connex_pdo->query($req);
                            $count = $reponse->rowCount();
                            if($count > 0) {
                                echo '<p class="success">'.$count.' transaction(s) trouvée(s)</p>';
                                $infos = $reponse->fetchAll();
                                foreach ($infos as $key => $value) {
                                    $results = date('Y-m-d',strtotime($infos[$key]['date_saisie']));

                                    $a_text = '#'.$infos[$key]['id'].' '.$infos[$key]['nom'].' - '.$infos[$key]['objet'].' - '.$results;
                                    echo '<p><a href="regie/recu.php?n='.$infos[$key]['id'].'" target="_blank">'.$a_text.'</a></p>';
                                }
                            }
                            else {
                                echo '<p class="failed">Aucune transactions ne correspond à vos critères</p>';
                            }
                        }
                        break;
                    //FIN Obtenir un reçu

                    //DEBUT Obtenir un récapitulatif
                    case 2:
                        //Si date non-renseignée
                        if(empty($_POST['date'])) {
                            echo '<p class="failed">Pour obtenir un récapitulatif, veuillez saisir une date</p>';
                        }
                        else {
                            //Periode (jour, mois, année, période)
                            switch($_POST['periode']) {
                                case 1:
                                    //Periode d'un jour
                                    $date_db = date('Y-m-d',strtotime($_POST['date']));
                                    $date_recap = date('w d n Y',strtotime($_POST['date']));
                                    $date_alpha = dateEnLettres($date_recap);

                                    $date_erreur = ' le '.$date_alpha;
                                    $date_lien = 'du '.$date_alpha;
                                    break;
                                case 2:
                                    //Periode d'un mois
                                    $date_db = date('Y-m',strtotime($_POST['date']));
                                    $date_alpha = moisEnLettres(date("n",strtotime($_POST['date']))).' '.date('Y',strtotime($_POST['date']));
                                
                                    $date_erreur = ' en '.$date_alpha;
                                    if(date("m",strtotime($date_db)) == 8 || date("m",strtotime($date_db)) == 10)
                                        $date_lien = "d'".$date_alpha;
                                    else 
                                        $date_lien = 'de '.$date_alpha;
                                    $date_erreur = ' en '.$date_alpha;
                                    break;
                                case 3:
                                    //Période d'une année
                                    $date_db = date('Y',strtotime($_POST['date']));
                                    $date_alpha = $date_db;

                                    $date_erreur = ' en '.$date_alpha;
                                    $date_lien = 'de '.$date_alpha;
                                    break;
                                case 4:
                                    //Période précise
                                    $date_parts = explode(';',$_POST['date']);
                                    $date_db = date('Y-m-d',strtotime($date_parts[0])).':'.date('Y-m-d',strtotime($date_parts[1]));
                                    
                                    //Phrase date
                                    $date_alpha = "du ".date('d/m/Y',strtotime($date_parts[0]))." au ".date('d/m/Y',strtotime($date_parts[1]));
                                    $date_lien = $date_alpha;
                                    $date_erreur = $date_alpha;

                                    break;
                            }
                            //$date_db = date('Ymd',strtotime($_POST['date']));
                            
                            if($_POST['periode'] == 4) {
                                //Si période précise, requête BETWEEN
                                $req = 'SELECT id,nom,objet FROM r_transactions WHERE date_saisie BETWEEN "'.$date_parts[0].' 00:00:00" AND "'.$date_parts[1].' 23:59:59"';
                            }
                            else {
                                //Sinon, requête LIKE
                                $req = 'SELECT id,nom,objet FROM r_transactions WHERE date_saisie LIKE "'.$date_db.'%"';
                            }

                            $title = 'Récapitulatif des transactions ';
                            $title_error = "Aucune transaction ";

                            $lien = 'regie/recap.php?date='.$date_db;

                            $req = $req.' AND `objet` = "'.$_POST['objet'].'" AND `type` = "'.$_POST['moyen_paiement'].'"';

                            //Titre du lien + objet
                            $title = $title.'pour '.$_POST['objet'].' ';

                            //Titre du lien si aucune transaction trouvée + objet
                            $title_error = $title_error.'pour '.$_POST['objet'].' ';

                            //Si paiement en Numéraire
                            if($_POST['moyen_paiement'] == 'Numéraire') {
                                //Titre du lien + moyen de paiement
                                $title = $title.'règlées en '.$_POST['moyen_paiement'].' ';

                                //Titre du lien si aucune transaction trouvée + moyen de paiement
                                $title_error = $title_error.'règlées en '.$_POST['moyen_paiement'].' ';
                            }
                            else {
                                //Titre du lien + moyen de paiement
                                $title = $title.'règlées par '.$_POST['moyen_paiement'].' ';

                                //Titre du lien si aucune transaction trouvée + moyen de paiement
                                $title_error = $title_error.'règlées par '.$_POST['moyen_paiement'].' ';
                            }

                            //URL du recapitulatif
                            //Ajout de _ pour l'objet dans l'URL
                            $lien = $lien.'&objet='.str_replace(' ','_',$_POST['objet']).'&moyen='.$_POST['moyen_paiement'];
                            //$lien = $lien.'&moyen='.$_POST['moyen_paiement'];

                            $reponse = $connex_pdo->query($req.' AND statut = 1');
                            $count = $reponse->rowCount();
                            
                            if($count > 0) {
                                ?>
                                    <i class="fas fa-file-pdf"></i>
                                    <a class="btn btn-link" href="<?php echo $lien; ?>" target="_blank"><?php echo $title.' '.$date_lien.' - '.$count; ?> transaction(s)</a>
                                <?php
                                
                            }
                            else {
                                echo '<p class="failed">'.$title_error.' n\'a été saisie '.$date_erreur.'</p>';
                            }
                        }
                        break;
                    //FIN Obtenir un récapitulatif
                }
            }
?>