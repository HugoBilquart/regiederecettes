<?php
	require('../fpdf/fpdf.php');

	include('../db_connex_pdf.php');
	include('../app/functions.php');
	include('../functions.php');
	
	
	define("CPTE_CONCOURS", 0000);
	define("CPTE_INSCRIPTION", 0000);
	define("CPTE_PERISCOLAIRE", 0000);
	define("CPTE_INFOGRAPHIE", 0000);
	define("CPTE_HEBERGEMENTS", 0000);
	define("CPTE_AUTRES", 0000);

	

	if(strlen($_GET['date']) == 21) {
		$date_parts = explode(':',$_GET['date']);
		$objet = str_replace('_',' ',$_GET['objet']);

		$req = 'SELECT * FROM r_transactions WHERE date_saisie BETWEEN "'.$date_parts[0].' 00:00:00" AND "'.$date_parts[1].' 23:59:59" AND objet = "'.str_replace('_',' ',$_GET['objet']).'" AND `type` = "'.$_GET['moyen'].'"';
		$req_amount = 'SELECT SUM(montant) AS total FROM r_transactions WHERE date_saisie BETWEEN "'.$date_parts[0].'" AND "'.$date_parts[1].'" AND `objet` = "'.str_replace('_',' ',$_GET['objet']).'" AND `type` = "'.$_GET['moyen'].'"';
	}
	else {
		$req = 'SELECT * FROM r_transactions WHERE date_saisie LIKE "'.$_GET['date'].'%" AND objet = "'.str_replace('_',' ',$_GET['objet']).'" AND `type` = "'.$_GET['moyen'].'"';
		$req_amount = 'SELECT SUM(montant) AS total FROM r_transactions WHERE date_saisie LIKE "'.$_GET['date'].'%" AND `objet` = "'.str_replace('_',' ',$_GET['objet']).'" AND `type` = "'.$_GET['moyen'].'"';
	}

	$req = $req.'AND statut = 1 ORDER BY nom_agent';
	$req_amount = $req_amount.' AND statut = 1';

	$results_req = $connex_pdo->query($req);
	$results_req_amount = $connex_pdo->query($req_amount);

	$countTransactions = $results_req->rowCount();
	$countAmount = $results_req_amount->fetchColumn();

	$line = $results_req->fetchAll();
	
	if(strlen($_GET['date']) == 4) {
		$date = 'de '.$_GET['date'];
	}
	else if(strlen($_GET['date']) == 7) {
		$year = date('Y',strtotime($_GET['date']));
		$month = date('n',strtotime($_GET['date']));

		$moisLettres = moisEnLettres($month);

		if($month == 8 || $month == 10)
			$date = "d'".$moisLettres.' '.$year;
		else 
			$date = 'de '.$moisLettres.' '.$year;
	}
	else if(strlen($_GET['date']) == 21) {
		$date_parts = explode(':',$_GET['date']);

		$date_recap = "du ".date('d/m/Y',strtotime($date_parts[0]))." au ".date('d/m/Y',strtotime($date_parts[1]));
		$date  = $date_recap;
	}
	else {
		$date_recap = date('w d n Y',strtotime($_GET['date']));
		$date  = 'du '.dateEnLettres($date_recap);

		//$date = 'du '.date('d m Y',strtotime($_GET['date']));
	}

	class Numeraire extends FPDF
	{
		function Header()
		{
			if($this->page == 1) {
				$this->Image('../img/docs/logo.png',10,15,26.6,15.7,'PNG');

				$this->SetFont('Arial','B',10);
				$this->Text(140,15,' - Infographie - ');
				$this->SetFont('Arial','',10);
				$this->Text(120,25,'Cpte : XXXX');

				$this->Ln(45);

				//DEBUT Titre
				$this->Cell(0,5,utf8_decode("BORDEREAU DE REMISE D'ESPECES EN EUROS A L'ENCAISSEMENT"),0,2,'C');
				$this->SetFont('Arial','B',10);
				$this->Cell(0,5,utf8_decode(mb_strtoupper("pour crédit d'un compte de dépôt en euros")),0,1,'C');
				$this->SetFont('Arial','I',10);
				$this->Cell(0,5,utf8_decode("(au crédit de votre compte sauf bonne fin)"),0,1,'C');
				$this->SetFont('Arial','',10);
				//FIN Titre

				$this->Ln(5);

				//DEBUT Informations
				$this->Cell(80,6,utf8_decode("CACHET DU POSTE"),'LTR',2,'C');

				$x = $this->GetX();
				$y = $this->GetY();
				
				$this->Cell(80,19,'','LBR',0,'C');

				$this->SetXY($x + 80 ,$y - 6);

				$this->Cell(40,6,utf8_decode("Date :"),'LTR',2,'C');
				$x = $this->GetX();
				$y = $this->GetY();

				$this->Cell(40,19,'','LBR',0,'C');

				$this->SetXY($x + 40 ,$y - 6);
				
				$this->Cell(50,6,utf8_decode("     Titulaire du compte : "),0,2,'L');
				$this->Cell(50,7,utf8_decode("     [________________________________]"),0,2,'L');


				
				$this->Cell(50,13,utf8_decode("        N° du compte à créditer"),0,1,'L');

				$this->Ln(-1);

				$this->Cell(120,25,utf8_decode("ESPECES"),1,0,'C');

				
				//DEBUT N° de compte

				//Position de 
				$x = $this->GetX();
				$y = $this->GetY();

				//Cellule comprenant le N° de compte
				$this->Cell(70,10,'',0,0,'C');

				$this->SetXY($x + 10 ,$y);

				$this->SetFont('Arial','B',10);

				$numeroCompte = array(0,0,0,0,0,0,0,0,0,0,0,'',0,0);
				for($i = 0 ; $i < 14 ; $i++) {
					//Retour à la position precedente avec décalage sur la droite
					$this->SetXY($x + 8 + ($i * 4) ,$y);
					if($i == 11) {
						//Espace, pas d'encadrement
						$this->Cell(4,5,utf8_decode($numeroCompte[$i]),0,0,'C');
					}
					else if($i == 13) {
						//Fin du N° de compte, retour à la ligne
						$this->Cell(4,5,utf8_decode($numeroCompte[$i]),'LBR',2,'C');
					}
					else {
						//Impression d'un chiffre du N° de compte
						$this->Cell(4,5,utf8_decode($numeroCompte[$i]),'LBR',0,'C');
					}
				}
				//FIN N° de compte

				$this->SetXY($x ,$y + 10);
				$this->SetFont('Arial','',10);
				$this->Cell(70,15,'',0,1,'C');
				$this->Cell(120,5,"",1,1,'C');
				//FIN Informations
			}
		}

		function Footer()
		{
			$this->SetY(-15);

			$this->SetFont('Arial','I','8');

			//$this->Cell(10,10,'Page '.$this->PageNo(),0,2,'C');

			$this->Cell(0,5,utf8_decode('[_____________________________________________________________________________]'),0,2,'L',0);

			$this->Cell(0,5,utf8_decode("Régie de recettes v5 - 2020 - Hugo BILQUART pour [__________]"),0,0,'L',0);

			$this->Cell(0,5,utf8_decode('Généré le '.date("d/m/Y, H:i")),0,1,'R',0);

			

			//$this->Cell(0,)
		}
	}

	class Cheque extends FPDF {
		function Header()
		{
			if($this->page == 1) {
				$this->Image('../img/docs/logo.png',10,15,26.6,15.7,'PNG');

				$this->SetFont('Arial','B',10);
				$this->Text(120,15,utf8_decode(' - '.str_replace('_',' ',$_GET['objet']).' - '));
				$this->SetFont('Arial','',10);
				$this->Text(120,25,'Cpte : 7062');

				$this->Ln(45);

				//DEBUT Titre
				$this->Cell(0,5,utf8_decode("BORDEREAU DE REMISE DE CHÈQUES EN EUROS À L'ENCAISSEMENT"),0,2,'C');
				$this->SetFont('Arial','B',10);
				$this->Cell(0,5,utf8_decode(mb_strtoupper("pour crédit d'un compte de dépôt en euros")),0,1,'C');
				$this->SetFont('Arial','I',10);
				$this->Cell(0,5,utf8_decode("(au crédit de votre compte sauf bonne fin)"),0,1,'C');
				$this->SetFont('Arial','',10);
				//FIN Titre

				$this->Ln(5);

				//DEBUT Informations
				
				$this->Cell(80,6,utf8_decode("CACHET DU POSTE"),'LTR',2,'C');

				$x1 = $this->GetX();
				$y1 = $this->GetY();

				$x = $this->GetX();
				$y = $this->GetY();
				
				$this->Cell(80,24,'','LBR',0,'C');

				$this->SetXY($x + 80 ,$y - 6);

				$this->Cell(40,6,utf8_decode("Date :"),'LTR',2,'C');
				$x = $this->GetX();
				$y = $this->GetY();

				$this->Cell(40,24,utf8_decode(date('d/m/Y')),'LBR',0,'C');

				$this->SetXY($x + 40 ,$y - 6);
				

				$this->Cell(60,10,utf8_decode("Titulaire du compte : ENSA DE"),'LTR',2,'C');
				$this->Cell(60,10,utf8_decode("LIMOGES - AUBUSSON"),'LBR',2,'C');

				//N° de compte
				$this->Cell(60,5,utf8_decode("N° du compte à créditer :"),'LTR',2,'C');
				$this->Cell(60,5,utf8_decode(""),'LR',2,'C');
				$this->Cell(60,5,utf8_decode("00001000197 06"),'LBR',1,'C');

				//Chèques moins de 5000 euros
				$this->SetXY($x1 ,$y1 + 24);
				$this->Cell(120,5,utf8_decode("Chèques"),'LTR',2,'C');
				$this->Cell(120,6,utf8_decode("<"),'LR',2,'C');
				$this->Cell(120,6,utf8_decode("5 000 euros"),'LR',2,'C');
				$x_rect = $this->GetX();
				$y_rect = $this->GetY();
				$this->Cell(120,7,utf8_decode(""),'LBR',0,'C');
				$this->Rect($x_rect + 110 , $y_rect,6,5,'');
			}
		}

		function Footer()
		{
			$this->SetY(-15);

			$this->SetFont('Arial','I','8');

			$this->Cell(0,5,'[_____________________________________________________________________________]',0,0,'L',0);


			$this->Cell(0,5,utf8_decode('Généré le '.date("d/m/Y, H:i")),0,1,'R',0);

			$this->Cell(0,5,utf8_decode("Régie de recettes v5 - 2020 - Hugo BILQUART pour [__________]"),0,0,'L',0);		

			$this->Cell(0,5,'page '.$this->page,0,1,'R',0);
		}
	}

	/** FIN CLASSES */

	//Si tous les paramètres sont renseignés dans l'URL (Objet de transaction + Moyen de paiement)
	if(isset($_GET['objet']) && isset($_GET['moyen']) && !empty($_GET['objet']) && !empty($_GET['moyen'])) {
		//DEBUT RECAP NUMERAIRE
		if($_GET['moyen'] == "Numéraire") {
			$pdf = new Numeraire('P','mm','A4');
	
			$pdf->SetAutoPageBreak(True);
	
			//On definie l'ecart de 10px de chaque coté
			$pdf->SetMargins(10,-10,10);
	
			$pdf->SetFont('Courier','',10);
	
			$pdf->SetTitle(utf8_decode('Récapitulatif des transactions '.$date));
			$pdf->AddPage();
	
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(15,15,utf8_decode('N°'),1,0,'C');
			$pdf->Cell(130,15,utf8_decode('Noms'),1,0,'C');
			$pdf->Cell(40,15,utf8_decode(''),1,1,'C');
	
			$pdf->SetFont('Arial','',10);
	
			$i = 1;
			foreach ($line as $key => $value) {
				$pdf->Cell(15,5,utf8_decode($i),1,0,'C');
	
				$nom = explode(' ',$line[$key]['nom']);
				$pdf->Cell(130,5,utf8_decode($nom[1]),1,0,'C');
				
				$pdf->Cell(40,5,iconv("UTF-8", "CP1252", $line[$key]['montant'].' €'),1,1,'C');
	
				$pdf->SetFillColor(255,255,255);
				$i++;
			}
	
			$pdf->SetFont('Courier','',8);
			$pdf->Cell(15,4,'',0,0,'C');
			$pdf->Cell(130,4,'(1) cocher la case correspondante',0,0,'L');
	
			//DEBUT Total
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(18,8,'TOTAL',1,0,'C');
			
			//Remplacement du point des decimales par une virgule
			$countAmount = str_replace('.', ',', $countAmount);

			$pdf->Cell(22,8,utf8_decode($countAmount),1,0,'C');
			//FIN Total
	
			$pdf->Output('I',utf8_decode("Bordereau XX - Infographie - ".$countAmount." € E $date"));
		}
		//FIN RECAP NUMERAIRE

		//DEBUT RECAP CHEQUE
		else if($_GET['moyen'] == 'Chèque' ) {
			$pdf = new Cheque('P','mm','A4');
			$pdf->SetAutoPageBreak(True);
	
			//On definie l'ecart de 10px de chaque coté
			$pdf->SetMargins(10,-10,10);
	
			$pdf->SetFont('Courier','',10);
	
			$pdf->SetTitle(utf8_decode('Récapitulatif des transactions '.$date));
			$pdf->AddPage();
	
			$pdf->Ln(10);
	
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(80,5,utf8_decode('Nombre de chèques :'),1,0,'C');
			$pdf->Cell(40,5,utf8_decode($countTransactions),1,0,'C');
			$pdf->Cell(20,5,'TOTAL :',1,0,'C');
			$pdf->Cell(40,5,iconv("UTF-8", "CP1252", $countAmount.' €'),1,1,'R');
	
			$pdf->Ln(5);	
	
	
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(5,8,utf8_decode('N°'),1,0,'C');
			$pdf->Cell(38,8,utf8_decode('NOMS'),1,0,'C');
			$pdf->Cell(37,8,utf8_decode('NOM DU TIREUR'),1,0,'C');
			$pdf->Cell(40,8,utf8_decode('ETABLISSEMENT TIRE'),1,0,'C');
	
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->Cell(20,4,utf8_decode('N° DU'),'LTR',2,'C');
			$pdf->Cell(20,4,utf8_decode('CHEQUE'),'LBR',0,'C');
			$pdf->SetXY($x + 20,$y);
			
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->Cell(40,4,utf8_decode('MONTANT DU'),'LTR',2,'C');
			$pdf->Cell(40,4,utf8_decode('CHEQUE EN EUROS'),'LBR',1,'C');
	
			$pdf->SetFont('Arial','',8);
	
			foreach ($line as $key => $value) {
				$detailPaiement = explode(';',$line[$key]['paiement_detail']);
	
				//Bordure en fonction de la position dans le tableau
				if($key == 0 && $countTransactions == 1) {
					//Encadrement complet si une seule transaction
					$border = 1;
				}
				else if($key == 0) {
					//Encadrement gauche, haut et droite si première transaction
					$border = 'LTR';
				}
				else if($key == count($line) - 1) {
					//Encadrement gauche, bas et droite si dernière transaction
					$border = 'LBR';
				}
				else {
					$border = 'LR';
				}
				$pdf->Cell(5,5,utf8_decode($key+1),$border,0,'C');
				$pdf->Cell(38,5,utf8_decode($line[$key]['nom']),$border,0,'L');
				$pdf->Cell(37,5,utf8_decode($detailPaiement[2]),$border,0,'L');
				$pdf->Cell(40,5,utf8_decode($detailPaiement[1]),$border,0,'L');
				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->Cell(20,5,utf8_decode($detailPaiement[0]),$border,0,'C');
				$pdf->Cell(40,5,utf8_decode(str_replace('.',',',$line[$key]['montant'])),$border,1,'R');
			}
	
			$pdf->SetXY($x,$y + 10);
	
			$pdf->SetFont('Arial','BI',10);
	
			$pdf->Cell(20,5,'TOTAL :',1,0,'C');
			$pdf->Cell(40,5,iconv("UTF-8", "CP1252", str_replace('.',',',$countAmount).' €'),1,0,'R');
	
			$pdf->Output('I',utf8_decode("Bordereau XX - Infographie - ".$countAmount." € E $date"));
		}
		//FIN RECAP CHEQUE
	}
	else {
		if(!isset($_GET['objet']) || empty($_GET['objet'])) {
			echo "ERREUR : Aucun objet de transaction n'a été transmis";
		}
		else if(!isset($_GET['moyen']) || empty($_GET['moyen'])) {
			echo "ERREUR : Aucun moyen de paiement n'a été transmis";
		}
	}
?>