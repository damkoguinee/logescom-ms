<fieldset style="margin-top: 10px;"><legend>Selectionnez le motif</legend> 
    <div class="choixg"> 
        <div class="optiong">
            <a href="dec.php?retrait=<?='decaissement client';?>&frais">
            <div class="descript_optiong">RETRAITS</div></a>
        </div>

        <div class="optiong">
            <a href="decdepense.php?depense=<?='depenses';?>">
            <div class="descript_optiong">DEPENSES</div></a>
        </div>

        <div class="optiong">
            <a href="decpersonnel.php?depense=<?='depenses';?>">
            <div class="descript_optiong">SALAIRES</div></a>
        </div> <?php 
        if ($user['statut'] != 'superviseur') {?>

            <div class="optiong">
                <a href="reduction.php?recette">
                <div class="descript_optiong">Reduction</div></a>
            </div>

            <div class="optiong">
                <a href="editionfacture.php?recette">
                <div class="descript_optiong">Saisir Facture</div></a>
            </div> <?php 
        } ?>      
    </div>

</fieldset>