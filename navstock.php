
<fieldset style="margin-top: 30px;"><legend></legend>
    <div class="choixstock"> 

        <div class="optiong">
            <a href="ajoutstock.php?listep">
            <div class="descript_optiong">Liste des Stocks</div></a>
        </div>

        <div class="optiong">
            <a href="ajout.php?listep">
            <div class="descript_optiong">Liste des Produits</div></a>
        </div>
        <?php 
        if ($user['statut'] != 'superviseur') { ?>

            <div class="optiong">
                <a href="categorie.php?listep">
                <div class="descript_optiong">Catégories</div></a>
            </div><?php 

            if ($_SESSION['level']>6) {?>

                <div class="optiong">
                    <a href="ajout.php?ajoutprod">
                    <div class="descript_optiong">Ajouter un Produit</div></a>
                </div>

                <div class="optiong">
                    <a href="listeprodfournisseur.php?fourn">
                    <div class="descript_optiong">Produits/fournisseurs</div></a>
                </div><?php 
            }
        } ?>
    </div>
</fieldset>