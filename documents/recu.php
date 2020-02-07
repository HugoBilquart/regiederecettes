<?php
	require('../fpdf/fpdf.php');

	$pdf = new FPDF('P','mm','A4');
	include('../db_connex_pdf.php');

	$req = 'SELECT * FROM r_transactions WHERE id="'.$_GET["n"].'"';
	$results = $connex_pdo->query($req);
	$tab = $results->fetch();
	if(empty($tab)) {
		?>
			<p>Aucune transaction trouvée pour le n°<?php echo $_GET["n"]; ?></p>
		<?php
	}
	else {
		//Verifie que la transaction concernée est bien validée
		if($tab['statut'] != 1) {
			?>
			<p>La transaction doit être validée pour obtenir un reçu</p>
			<?php
		}
		else {
			if($tab['id'] < 10) $tab['id'] = '00'.$tab['id'];
			else if($tab['id'] < 100) $tab['id'] = '0'.$tab['id'];

			$fmt = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
			
			if(strpos($tab['montant'], '.') == false) {
			    $tab['montant'] = number_format($tab['montant'], 2, '.', '');
			}

			$montant_parts = explode('.', $tab['montant']);

			//CENTIMES
			if($montant_parts[1] == 0) {
				$centimes = "";
			}
			else if($montant_parts[1] == 1) {
				$centimes = $fmt->format($montant_parts[1]).' centime';
			}
			else {
				$centimes = $fmt->format($montant_parts[1]).' centimes';
			}

			//UNITES
			if($montant_parts[0] == 0) {
				$unites = '';
			}
			else if($montant_parts[0] == 1) {
				$unites = $fmt->format($montant_parts[0]).' euro';
			}
			else {
				$unites = $fmt->format($montant_parts[0]).' euros';
			}

			
			
			//MONTANT FINAL
			if($unites == '') {
				$montant_lettre = $centimes;
			}
			else if($centimes == '') {
				$montant_lettre = $unites;
			}
			else {
				$montant_lettre = $unites.' et '.$centimes;
			}

			if($tab['objet'] == 'Hébergements') {
				$date1 = date_create($tab['date_arrivee']);
				$date2 = date_create($tab['date_depart']);

				$difference = date_diff($date1,$date2)->format('%d');
				if($difference == 1) {
					$nbNuit = '1 nuit';
				}
				else {
					$nbNuit = $difference.' nuits';
				}


				$tab['objet'] = $tab['objet'].' : '.$nbNuit;
			}
			else if($tab['objet'] == 'Autres Recettes') {
				$tab['objet'] = 'Autres Recettes : '.$tab['objet_detail'];
			}

			$date_saisie = 'le '.date('d/m/Y \à H:i:s',strtotime($tab['date_saisie']));

			$pdf->SetAutoPageBreak(True);

			$pdf->SetMargins(10,-10,10);

			$date = date("d-m-Y");
			$pdf-> SetTitle(utf8_decode('Reçu n° '.$tab['id'].''));
			$pdf-> AddPage();

			for($i = 0 ; $i < 3 ; $i++) {
				$pdf->SetFont('Arial','B',10);
				$pdf->Ln(20);


				$pdf->MultiCell(0,10,utf8_decode('Reçu 
N° '.$tab['id'].''),1,'L',0);
				$pdf->Image('images/logo.png',50,13+($i*100),26.6,15.7,'PNG');
				$pdf->Image('images/logo2.png',85,13+($i*100),60,15.7,'PNG');
				$pdf->Image('images/carre_blanc.png',180,10+($i*100),20,20,'PNG');
				$pdf->Ln(0);

				$pdf->MultiCell(0,10,utf8_decode("Montant total 			
(en toute lettre) :"),1,'L',0);
				$pdf->Ln(0);

				$pdf->SetFont('Courier','',10);
				$pdf->MultiCell(0,-10,utf8_decode($montant_lettre.'
		 '.$tab['montant'].' EUR , '.strtolower($tab['type'])),0,'R',0);
				$pdf->Ln(20);

				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(0,10,utf8_decode("De :"),1,0,'L');


				$pdf->SetFont('Courier','',10);
				$pdf->Cell(0,10,utf8_decode($tab['nom']),0,0,'R');
				$pdf->Ln(10);

				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(0,10,utf8_decode("Désignation des produits :"),1,0,'L');


				$pdf->SetFont('Courier','',10);
				$pdf->Cell(0,10,utf8_decode($tab['objet']),0,0,'R');
				$pdf->Ln(10);

				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(0,10,utf8_decode("Lieu, Date :"),1,0,'L');


				$pdf->SetFont('Courier','',10);
				$pdf->Cell(0,10,utf8_decode('[........], '.$date_saisie.''),0,0,'R');
				$pdf->Ln(10);
				switch ($i) {
					case 0:
						$pdf->Cell(0,5,utf8_decode('Exemplaire client'),0,0,'L');
						break;
					case 1:
						$pdf->Cell(0,5,utf8_decode('Exemplaire comptable'),0,0,'L');
						break;
					case 2:
						$pdf->Cell(0,5,utf8_decode('Exemplaire archive'),0,0,'L');
						break;
				}

				$pdf->Ln(5);

				$pdf->Image('images/ligne_decoupe.png',-50,100+($i*100),280,0.5,'PNG');
			
				$pdf->Ln(5);
			}
		$pdf->Output('I',utf8_decode("Reçu de la transaction n°".$tab['id']));
		}
	}
/*	

	if($tab['montant'] == 1) $montant_lettre = $fmt->format($tab['montant']).' euro';
	else $montant_lettre = $fmt->format($tab['montant']).' euros';*/


	
?>

