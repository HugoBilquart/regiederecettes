<h2>Génerer un reçu</h2>
<p>Remplissez le formulaire pour obtenir un reçu</p>

<?php $file = fopen('listeEtudiants.txt','r'); ?>

<form id="form-document" name="recu" method="POST">
    <div class="form_part">
        <p class="form-part-title"><b>Filtre</b></p>

        <p><b><label for="parDate">Par date</label></b></p>
        <div class="input-group date form_date col-md-5" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
            <input class="form-control" id="parDate" type="text" value="" readonly>
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        </div>
        <input type="hidden" id="dtp_input1" name="date">
        <br/>
        <p><b><label for="parNom">Par nom d'étudiant</label></b></p>
        <input type="text" list="etudiants" class="form-control input-text" id="parNom" name="nom" placeholder="Chercher par nom">
        <datalist id="etudiants">
            <?php
                //Liste des étudiants
                while(! feof($file)) {
                    $currentLine = fgets($file);
                    ?>
                        <option value="<?php echo $currentLine; ?>"><?php echo $currentLine; ?></option>
                    <?php
                }
                fclose($file);
            ?>
        </datalist>

        <p class="indications">Pour obtenir un reçu de saisie, indiquer la date et/ou le nom</p>
    </div>
    <br/>
    <div class="form_part submit_part">
        <button type="button" class="btn btn-primary submit" id="search">Rechercher</button>
    </div>
</form>

<br/>

<div class="form_part" id="results-area">
    <p class="form-part-title"><b>Résultats</b></p>
    <div class="col-sm-" id="results">

    </div>
</div>

<hr>

<div class="return text-center">
    <a href="regiederecettes.php" class="btn btn-default">
        <i class="fa fa-bars"></i> Retourner au menu Regie de recettes
    </a>
</div>