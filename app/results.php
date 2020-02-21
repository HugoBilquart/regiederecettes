<h2>Gérer les transactions étudiants</h2>

<?php
	//Traitement d'une transaction
	if(isset($_GET['n'])) {
		include('results_detail.php');
	}
	//Statut de traitement
	else if(isset($_GET['traitee'])) {
		?>
			<p class="indications">Cliquer sur une ligne pour traiter une transaction</p>
			<div class="form-part row" style="text-align: center;">
				<button type="button" class="gestion_link btn btn-primary" onclick="window.location.href='regiederecettes.php?action=1&traitee=0'">Voir les transactions à traiter</button>
				<button type="button" class="gestion_link btn btn-primary" onclick="window.location.href='regiederecettes.php?action=1&traitee=1'">Voir les transactions traitées</button>
			</div>
			<br/>
			<div class="row">
				<!-- DEBUT Tableau des transactions -->
				<table class="table table-bordered table-hover text-center" id="transactions">
					<thead class="thead-dark">
						<tr>
							<th scope="col">#</th>
							<th scope="col"><a href="<?php echo redirectURL('date_saisie'); ?>" title="Trier par date de saisie">Date de saisie</a></th>
							<th scope="col"><a href="<?php echo redirectURL('nom'); ?>" title="Trier par nom d'etudiant">Nom</a></th>
							<th scope="col"><a href="<?php echo redirectURL('objet'); ?>" title="Trier par objet">Objet</a></th>
							<th scope="col"><a href="<?php echo redirectURL('type'); ?>" title="Trier par moyen de paiement">Paiement</a></th>
							<th scope="col"><a href="<?php echo redirectURL('statut'); ?>" title="Trier par statut">Statut</a></th>
							<th scope="col">Commentaire</th>
						</tr>
					</thead>
					<tbody>
						<?php
							//Transactions à traiter
							if($_GET['traitee'] == 0) {
								//Trier par ...
								if(!empty($_GET['orderby'])) {
									//Décroissant si meme filtre selectionné
									if(isset($_GET['desc'])) {
										$requete = $connex_pdo->query('SELECT * FROM r_transactions WHERE statut = 0 ORDER BY '.$_GET['orderby'].' DESC');
									}
									else {
										$requete = $connex_pdo->query('SELECT * FROM r_transactions WHERE statut = 0 ORDER BY '.$_GET['orderby']);
									}
								}
								else {
									$requete = $connex_pdo->query('SELECT * FROM r_transactions WHERE statut = 0');
								}
							}
							//Transactions traitées
							else if($_GET['traitee'] == 1) {
								$requete = $connex_pdo->query('SELECT * FROM r_transactions WHERE statut = 1 OR statut = 2');
								if(!empty($_GET['orderby'])) {
									if(isset($_GET['desc'])) {
										$requete = $connex_pdo->query('SELECT * FROM r_transactions WHERE statut = 1 OR statut = 2 ORDER BY '.$_GET['orderby'].' DESC');
									}
									else {
										$requete = $connex_pdo->query('SELECT * FROM r_transactions WHERE statut = 1 OR statut = 2 ORDER BY '.$_GET['orderby']);
									}
								}
								else {
									$requete = $connex_pdo->query('SELECT * FROM r_transactions WHERE statut = 1 OR statut = 2');
								}
							}
							//Toutes les transactions
							else {
								$requete = $connex_pdo->query('SELECT * FROM r_transactions');
							}
							$transactions = $requete->fetchAll();
							if(count($transactions) == 0) {
								?>
									<td colspan="7">Aucune transaction trouvée</td>
								<?php
							}
							else {
								foreach ($transactions as $key => $value) {
									if($transactions[$key]['objet'] == "Hébergements") {
										$nbNuit = date_diff(date_create($transactions[$key]['date_arrivee']),date_create($transactions[$key]['date_depart']))->format('%d');
										if($nbNuit == 1) {
											$nbNuit = $nbNuit.' nuit';
										}
										else {
											$nbNuit = $nbNuit.' nuits';
										}

										$objet = $transactions[$key]['objet']." | ".$nbNuit;
									}
									else {
										$objet = $transactions[$key]['objet'];		
									}

									switch ($transactions[$key]['statut']) {
										case 0:
											$statut = 'Saisie';
											break;
										case 1:
											$statut = '✔';
											break;
										case 2:
											$statut = '✘';
											break;
									}
								?>
								<!-- Transaction -->
								<tr data-id='<?php echo $transactions[$key]['id']; ?>' data-statut="<?php echo $transactions[$key]['statut']; ?>">
									<td><?php echo $transactions[$key]['id']; ?></td>
									<td><?php echo date('d/m/Y',strtotime($transactions[$key]['date_saisie'])); ?></td>
									<td><?php echo $transactions[$key]['nom']; ?></td>
									<td><?php echo $objet; ?></td>
									<td><?php echo $transactions[$key]['type'].' | '.$transactions[$key]['montant'] .' €'; ?></td>
									<td><?php echo $statut; ?></td>
									<td><?php echo $transactions[$key]['commentaire']; ?></td>
								</tr>
								<?php
								}
							}
						?>
					</tbody>
				</table>
				<!-- FIN Tableau des transactions -->
			</div>

			<hr>
			
			<!-- RETOUR à la selection d'une rubrique -->
			<div class="return text-center">
				<a href="regiederecettes.php" class="btn btn-default">
					<i class="fa fa-bars"></i> Retourner au menu Regie de recettes
				</a>
			</div>
		<?php
	}
?>

<script>
	//Redirige vers la page de traitement d'une transaction au clique sur une ligne
	$('#transactions > tbody > tr').click(function() {
		window.location.href = 'regiederecettes.php?action=1&n=' + $(this).data("id");
	});
</script>


	