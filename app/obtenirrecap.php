<h2>Obtenir un récapitulatif des transactions</h2>
<p>Remplissez le formulaire pour obtenir le récapitulatif que vous souhaitez</p>

<form id="form-document" name="recap" method="POST">
    <!-- DEBUT Type de recapitulatif -->
    <div class="form_part row">
        <p class="form-part-title"><b>Type de récapitulatif</b></p>

        <div class="col-xs-2">
            <label for="type-q">Quotidien</label>
            <input type="radio" name="type" id="type-q" value="1">
        </div>
        <div class="col-xs-2">
            <label for="type-m">Mensuel</label>
            <input type="radio" name="type" id="type-m" value="2">
        </div>
        <div class="col-xs-2">
            <label for="type-a">Annuel</label>
            <input type="radio" name="type" id="type-a" value="3">
        </div>
        <div class="col-xs-2">
            <label for="type-p">Période précise</label>
            <input type="radio" name="type" id="type-p" value="4">
        </div>
    </div>
    <!-- FIN Type de recapitulatif -->

    <br/>

    <!-- DEBUT Date(s) -->
    <div class="form_part row">
        <p class="form-part-title"><b>Date</b></p>
        <div id="filtre-date">
            <p id="filtre-jour-label" class="filtre-label"><b><label for="parDate">Jour</label></b></p>
            <p id="filtre-mois-label" class="filtre-label"><b><label for="parMois">Mois</label></b></p>
            <p id="filtre-annee-label" class="filtre-label"><b><label for="parAnnee">Année</label></b></p>
            <p id="filtre-periode-label-1" class="filtre-label"><b><label for="parPeriode">Période - Debut</label></b></p>
            
            <div id="filtre" class="input-group date form_date filtre col-md-5" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
                <input class="form-control date-input" id="" type="text" value="" placeholder="Indiquer la date" readonly>
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                <input type="hidden" id="dtp_input1" name="date">
            </div>

            <p id="filtre-periode-label-2" class="filtre-label"><b><label for="parPeriode2">Période - Fin</label></b></p>
            <div id="filtre2" class="input-group date form_date filtre col-md-5" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                <input class="form-control date-input" id="parPeriode2" type="text" value="" placeholder="Indiquer la date" readonly>
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                <input type="hidden" id="dtp_input2" name="date2">
            </div>
        </div>
    </div>
    <!-- FIN Date(s) -->

    <br/>

    <!-- DEBUT Filtres (objet et moyen de paiement) -->
    <div class="form_part row">
        <p class="form-part-title"><b>Filtres</b></p>
        <div class="col-md- type">
            <label for="objet">Objet de la transaction</label>
            <select id="objet" name="filtre-objet" class="type">
                <option value="" selected>Selectionner</option>
                <option value="Concours d'Entrée">Concours d'Entrée</option>
                <option value="Droits d'Inscription">Droits d'Inscription</option>
                <option value="Droit d'Inscription Périscolaire">Droit d'Inscription Périscolaire</option>
                <option value="Hébergements">Hébergements</option>
                <option value="Infographie">Infographie</option>
                <option value="Autres Recettes">Autres Recettes</option>
            </select>
        </div>
        <br/>
        <div class="col-md-">
            <label for="type-paiement">Moyen de paiement</label>
            <select id="type-paiement" name="filtre-type" class="type">
                <option value="" selected>Selectionner</option>
                <option value="Numéraire">Numéraire</option>
                <option value="Chèque">Chèque</option>
            </select>
        </div>
        <p class="indications">Utilisez obligatoirement les deux filtres.</p>
    </div>
    <!-- FIN Filtres (objet et moyen de paiement) -->

    <br/>

    <div class="form_part row submit_part">
        <button type="button" class="btn btn-primary submit" id="search">Rechercher</button>
    </div>
</form>

<br/>

<!-- DEBUT Resultat de la recherche (Complété par JS) -->
<div class="form_part row" id="results-area">
    <p class="form-part-title"><b>Résultats</b></p>
    <div class="col-sm-" id="results"></div>
</div>
<!-- FIN Resultat de la recherche -->

<hr>

<div class="return">
    <a href="regiederecettes.php" class="btn btn-default">
        <i class="fa fa-bars"></i> Retourner au menu Regie de recettes
    </a>
</div>

<script>
    $('input[name="type"]').change(function(){
        $typeRecap = $(this).val();

        $inputDate = $('input[name="date"]');

        //Afficher le/les selecteur(s) de date
        $('#filtre').css('visibility','visible');
        
        switch($typeRecap) {
            case '1':
                //Récapitulatif du jour

                //Afficher le bon label et masquer les autres
                $('#filtre-jour-label').css('display','block'); // <--
                $('#filtre-mois-label').css('display','none');
                $('#filtre-annee-label').css('display','none');
                $('#filtre-periode-label-1').css('display','none');
                $('#filtre-periode-label-2').css('display','none');

                //Modifier l'ID
                $('input.date-input').prop('id', 'parDate');

                //Affecter les classes correspondantes
                $('#filtre').removeClass("form_month form_year");
                $('#filtre').toggleClass("form_date");

                //Masquer le 2ème selecteur de date
                $('#filtre2').css('visibility','collapse');

                //Regler le selecteur de date
                $('#filtre').attr('data-date-format', 'dd MM yyyy');
                $('#filtre').attr('data-link-format', 'yyyy-mm-dd');

                //Reinitialise le selecteur de date
                $('.form_date').datetimepicker("remove");
                $('.form_date').datetimepicker($days_options);
                $('.form_date').removeData();
                break;

            case '2':
                //Récapitulatif du mois

                //Afficher le bon label et masquer les autres
                $('#filtre-jour-label').css('display','none');
                $('#filtre-mois-label').css('display','block'); // <--  
                $('#filtre-annee-label').css('display','none');
                $('#filtre-periode-label-1').css('display','none');
                $('#filtre-periode-label-2').css('display','none');

                //Modifier l'ID
                $('input.date-input').prop('id', 'parMois');

                //Affecter les classes correspondantes
                $('#filtre').removeClass( "form_date form_year" );
                $('#filtre').addClass( "form_month" );

                //Masquer le 2ème selecteur de date
                $('#filtre2').css('visibility','collapse');

                //Regler le selecteur de date
                $('#filtre').attr('data-date-format', 'yyyy-mm');
                $('#filtre').attr('data-link-format', 'yyyy-mm');

                //Reinitialise le selecteur de date
                $('.form_month').datetimepicker("remove");
                $('.form_month').datetimepicker($months_options);
                $('.form_month').removeData();
                break;
            
            case '3':
                //Récapitulatif de l'annee

                //Afficher le bon label et masquer les autres
                $('#filtre-jour-label').css('display','none');
                $('#filtre-semaine-label').css('display','none');
                $('#filtre-mois-label').css('display','none');
                $('#filtre-annee-label').css('display','block'); // <-- 
                $('#filtre-periode-label-1').css('display','none');
                $('#filtre-periode-label-2').css('display','none');

                //Modifier l'ID
                $('input.date-input').prop('id', 'parAnnee');

                //Affecter les classes correspondantes
                $('#filtre').addClass( "form_year" );
                $('#filtre').removeClass( "form_date form_month" );
                
                //Masquer le 2ème selecteur de date
                $('#filtre2').css('visibility','collapse');

                //Regler le selecteur de date
                $('#filtre').attr('data-date-format', 'yyyy');
                $('#filtre').attr('data-link-format', 'yyyy');

                //Reinitialise le selecteur de date
                $('.form_year').datetimepicker("remove");
                $('.form_year').datetimepicker($years_options);
                $('.form_year').removeData();
                break;

            case '4':
                //Récapitulatif d'une periode précise

                //Afficher le bon label et masquer les autres
                $('#filtre-jour-label').css('display','none');
                $('#filtre-mois-label').css('display','none');
                $('#filtre-annee-label').css('display','none');
                $('#filtre-periode-label-1').css('display','block'); // <-- 
                $('#filtre-periode-label-2').css('display','block'); // <-- 

                //Modifier l'ID
                $('input.date-input').prop('id', 'parPeriode');

                //Affecter les classes correspondantes
                $('#filtre').removeClass("form_month form_year");
                $('#filtre').toggleClass("form_date");

                //AFFICHER le 2ème selecteur de date
                $('#filtre2').css('visibility','visible');

                //Regler le 1er selecteur de date
                $('#filtre').attr('data-date-format', 'dd MM yyyy');
                $('#filtre').attr('data-link-format', 'yyyy-mm-dd');

                //Reinitialise le 1er selecteur de date
                $('.form_date').datetimepicker("remove");
                $('.form_date').datetimepicker($days_options);
                $('.form_date').removeData();
                break;
        }
    });
</script>