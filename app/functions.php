<?php
	function DBConnection() {
		$db = new PDO('sqlite:db/regie.sqlite');
		if($db) {
			return $db;
		}
		else {
			echo '<p class="loginFailed">//Database connection... FAILED';
		}
	}

	function fetchTable($req) {
		$db = DBConnection();
		$results = $db->query($req);	
		echo '<table class="results_table" id="results_table" name="results_table"><tr>';
		echo "	<th class='results_table_index index_id'><a href=".redirectURL('id')." title='Trier par N°'>N°</a></th>
				<th class='results_table_index'><a href=".redirectURL('nom')." title='Trier par nom d`étudiant'>Nom</a></th>
				<th class='results_table_index'><a href=".redirectURL('objet')." title='Trier par objet de transaction'>Objet</a></th>
				<th class='results_table_index'><a href=".redirectURL('moyen')." title='Trier par moyen de paiement'>Moyen de paiement</a></th>
				<th class='results_table_index'><a href=".redirectURL('montant')." title='Trier par montant'>Montant</a></th>
				<th class='results_table_index'><a href=".redirectURL('statut')." title='Trier par statut'>Statut</a></th>";
		echo '</tr>';
        $tab = $results->fetchAll(PDO::FETCH_ASSOC);
        if($tab) {
            $i = 1;
            foreach ($tab as $key => $value) {
                echo '<tr id="'.$tab[$key]['id'].'" class="results_table_result" name="'.$i.'">
                        <td class="id_area">'.$tab[$key]['id'].'</td>
                        <td>'.$tab[$key]['nom'].'</td>';
                        if($tab[$key]['objet'] == "Hébergements") {
                            echo '<td>'.$tab[$key]['objet'].' ( '.$tab[$key]['date_arrivee'].' → '.$tab[$key]['date_depart'].' )</td>';
                        }
                        else {
                            echo '<td>'.$tab[$key]['objet'].'</td>';
                        }
                        echo '<td>'.$tab[$key]['moyen'].'</td>
                        <td>'.$tab[$key]['montant'].' €</td>';
                        switch ($tab[$key]['statut']) {
                            case 'Validée':
                                echo '<td>✔</td>';
                                break;
                            case 'Annulée':
                                echo '<td>✖</td>';
                                break;
                            default:
                                echo '<td>A traiter</td>';
                                break;
                        }
                echo '</tr>';
                $i++;
            }
        }
        else {
            echo '<tr><td colspan="6" class="empty_table">Aucune transaction trouvée</td></tr>';
        }
        echo '</table>';
	}

	function montantLettre($number) {

		$convert = explode('.', $number);
	    $num[17] = array('zero', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit',
	                     'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize');
	                      
	    $num[100] = array(20 => 'vingt', 30 => 'trente', 40 => 'quarante', 50 => 'cinquante',
	                      60 => 'soixante', 70 => 'soixante-dix', 80 => 'quatre-vingt', 90 => 'quatre-vingt-dix');
	                
	    
	    
	    echo $chiffre;

	    for($i = 0 ; $i < strlen($convert[0]) ; $i++) {
	    	$chiffre[$i] = substr($convert[0], $i);
	    	echo $chiffre[$i];	
	    	echo ",";
	    }


	    //echo $convert[1];


	        
	return $number;

	}

    function optionsTable($req) {
        $db = DBConnection();
        $results = $db->query($req);
        $tab = $results->fetchAll(PDO::FETCH_ASSOC);
        //$i = 1;
        foreach ($tab as $key => $value) {
            echo '<option value="'.$tab[$key]['id'].'">N°'.$tab[$key]['id'].' | '.$tab[$key]['nom'].' - '.$tab[$key]['objet'].'</option>';
            //$i++;
        }

    }

    function redirectURL($orderby) {
        if(empty($_GET['traitee'])) {
            if(isset($_GET['orderby'])) {
                if(isset($_GET['desc'])) {
                    if($_GET['orderby'] == $orderby) {
                        return 'regiederecettes.php?action=1&traitee=0&orderby='.$orderby;
                    }
                    else {
                        return 'regiederecettes.php?action=1&traitee=0&orderby='.$orderby;
                    }
                }
                else if ($_GET['orderby'] == $orderby) {
                    return 'regiederecettes.php?action=1&traitee=0&orderby='.$orderby.'&desc';
                }
                else {
                    return 'regiederecettes.php?action=1&traitee=0&orderby='.$orderby;
                }
            }
            else {
                return 'regiederecettes.php?action=1&traitee=0&orderby='.$orderby;
            }
        }
        else {
            if(isset($_GET['orderby'])) {
                if(isset($_GET['desc'])) {
                    if($_GET['orderby'] == $orderby) {
                        return 'regiederecettes.php?action=1&traitee='.$_GET['traitee'].'&orderby='.$orderby;
                    }
                    else {
                        return 'regiederecettes.php?action=1&traitee='.$_GET['traitee'].'&orderby='.$orderby;
                    }
                }
                else if ($_GET['orderby'] == $orderby) {
                    return 'regiederecettes.php?action=1&traitee='.$_GET['traitee'].'&orderby='.$orderby.'&desc';
                }
                else {
                    return 'regiederecettes.php?action=1&traitee='.$_GET['traitee'].'&orderby='.$orderby;
                }
            }
            else {
                return 'regiederecettes.php?action=1&traitee='.$_GET['traitee'].'&orderby='.$orderby;
            } 
        }
    }

	function int2alpha($number, $root = true)
	{
    $output = '';
    $number = (int)$number;
    if($number >= 1000)
    {
        $num_arr = array();
        for($i = strlen("$number"); $i > 0; $i -= 3);
        {
            $j = ($i > 3) ? $i - 3 : 0;
            array_unshift($num_arr, substr("$number", $j, 3));
        }
        $num_arr = array_map(create_function('$a', 'return int2alpha($a, false);'), $num_arr);
        $output = '';
        while(count($num_arr) > 0)
        {
            $output .= ' ' . array_shift($num_arr);
            if(count($num_arr) > 0)
            {
                switch(count($num_arr) % 3)
                {
                    case 1:
                        $output .= ' mille';
                        break;
 
                    case 2:
                        $output .= ' million';
                        break;
 
                    default:
                        $output .= ' milliard';
                }
            }
        }
    }
    elseif($number >= 100)
    {
        $centaines = int2alpha($number / 100);
        $reste = int2alpha($number % 100);
        $output = implode(' ', array(($centaines == 'un') ? 'cent' : "$centaines cent", $reste));
    }
    elseif($number > 80)
    {
        $number -= 80;
        $output = 'quatre-vingt-' . int2alpha($number);
    }
    elseif($number == 80)
    {
        $output = 'quatre-vingt';
    }
    elseif($number > 61)
    {
        $number -= 60;
        $output = 'soixante-' . int2alpha($number);
    }
    elseif($number >= 20)
    {
        $dixaine = $number / 10;
        $unite = $number % 10;
        switch($dixaine)
        {
            case 2:
                $output = 'vingt';
                break;
 
            case 3:
                $output = 'trente';
                break;
 
            case 4:
                $output = 'quarante';
                break;
 
            case 5:
                $output = 'cinquante';
                break;
 
            case 6:
                $output = 'soixante';
                break;
        }
        switch($unite)
        {
            case 0:
                break;
 
            case 1:
                $output .= ' et un';
                break;
             
            default:
                $output .= "-$unite";
        }
    }
    elseif($number > 16)
    {
        $output = 'dix-'.int2alpha($number % 10);
    }
    else
    {
        switch($number)
        {
            case 0:
                $output = '';
                break;
            
            case 1:
                $output = 'un';
                break;
 
            case 2:
                $output = 'deux';
                break;
 
            case 3:
                $output = 'trois';
                break;
 
            case 4:
                $output = 'quatre';
                break;
 
            case 5:
                $output = 'cinq';
                break;
 
            case 6:
                $output = 'six';
                break;
 
            case 7:
                $output = 'sept';
                break;
 
            case 8:
                $output = 'huit';
                break;
 
            case 9:
                $output = 'neuf';
                break;
 
            case 10:
                $output = 'dix';
                break;
 
            case 11:
                $output = 'onze';
                break;
             
            case 12:
                $output = 'douze';
                break;
 
            case 13:
                $output = 'treize';
                break;
 
            case 14:
                $output = 'quatorze';
                break;
 
            case 15:
                $output = 'quinze';
                break;
 
            case 16:
                $output = 'seize';
                break;
        }
    }
    return $output;
}
 
function dec2alpha($number)
{
    $number = (int)$number;
    if($number >= 10 and ($number % 10) == 0)
        $number /= 10;
    if($number > 10)
        //return int2alpha($number) . ' centièmes';
    	return int2alpha($number);
    /*elseif($number > 0)
        return int2alpha($number) . 'dizièmes';*/
    else
        return '';
}
 
function float2alpha($number)
{
    $number *= 100;
    $number = (int) $number;
    $entier = int2alpha($number / 100);
    $dec = dec2alpha($number % 100);
    if($dec == '')
        return $entier;
    elseif($entier == 'zéro')
        return $dec;
    else
        return "$entier euros et $dec centimes";
}

function dateEnLettres($date) {
    $jour = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
    $mois = array(
        1 => 'Janvier',
        2 => 'Février',
        3 => 'Mars',
        4 => 'Avril',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juillet',
        8 => 'Août',
        9 => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Décembre'
    );

    $date_parts = explode(' ', $date);

    $jour_alpha = $jour[$date_parts[0]];

    if($date_parts[1] < 10) {
        $date_parts[1] = ltrim($date_parts[1],0);
    } 
    $mois_alpha = $mois[$date_parts[2]];

    return $jour_alpha.' '.$date_parts[1].' '.$mois_alpha.' '.$date_parts[3];
}
?>