<div class="col-sm-12 col-md-2 pb-3" style="background-color:#2f2c2c; ">
    
    <div class="row mt-3">
        <div class="col" ><a style="width: 100%; " class="btn btn-light text-center fw-bold" href="editionfacturefournisseur.php">Factures des Fournisseurs</a></div>
    </div>

    <!-- <div class="row mt-3">
        <div class="col" ><a style="width: 100%; " class="btn btn-light text-center fw-bold" href="chequeespeces.php?cheques">Cheque/Espèces</a></div>
    </div> -->

    <?php 

    if ($user['statut'] != 'superviseur') { ?>

        <div class="row mt-3">
            <div class="col" ><a style="width: 100%; " class="btn btn-light text-center fw-bold" href="bon_commande.php">Bon de commande</a></div>
        </div> <?php 
    } ?>
</div>