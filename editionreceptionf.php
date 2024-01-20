<?php require 'headerv2.php';?>

<div class="container-fluid mx-0 px-0"><?php 

    if (isset($_GET['deletevers'])) {

        $id=$_GET['deletevers'];

        $numero=$_GET['idprod'];
        $depart=$_GET['depart'];
        $nomtabdep=$panier->nomStock($depart)[1];
        $qtitesup=$_GET['qtite'];
        $dateop=$_GET['dateop'];
        $bl=$_SESSION['bl'];

        $qtiteiniti=$DB->querys("SELECT quantite, prix_revient as pr FROM `".$nomtabdep."` WHERE idprod=?", array($numero));

        $prodcmd=$DB->querys("SELECT quantite, previent as pr FROM achat WHERE id_produitfac=? and numfact=?", array($numero, $bl));

        $qtiteinit=$qtiteiniti['quantite'];
        $pri=$qtiteiniti['pr'];

        $qtiteaug=$qtiteiniti['quantite']-$qtitesup;

        $prmoyen=$panier->espace(number_format(((($qtiteinit*$pri)-($prodcmd['quantite']*$prodcmd['pr']))/(($qtiteinit-$prodcmd['quantite']))),0,',','')); 

        $DB->insert("UPDATE `".$nomtabdep."` SET quantite = ?, prix_revient=? WHERE idprod= ?", array($qtiteaug, $prmoyen, $numero));

        $DB->delete('DELETE FROM stockmouv WHERE id = ?', array($id));

        $DB->delete('DELETE FROM achat WHERE id_produitfac=? and numfact=?', array($numero, $bl));?>

        <div class="alert alert-success">L'approvisionnement a été bien annulé</div><?php
    }


    if (isset($_POST['qtiteap'])) {

        $nomtab1=$panier->nomStock($_POST['departap'])[1];

        $idstock1=$panier->nomStock($_POST['departap'])[2];

        $lieuvente=$panier->nomStock($_POST['departap'])[3];

        $id=$panier->h($_POST['idap']);
        $pv=$panier->h($_POST['pv']);

        $designation=$panier->nomProduit($id);

        if (empty($_POST['pa'])) {
            $pra=0;
        }else{

            $pra=$panier->h($_POST['pa']);
        }

        if (empty($_POST['pr'])) {
            $pr=0;
        }else{

            $pr=$panier->h($_POST['pr']);
        }


        $bl=$_SESSION['bl'];


        $qtite=$panier->h($_POST['qtiteap']);

        $depart = $DB->querys("SELECT quantite as qtite, prix_revient as pr FROM `".$nomtab1."` WHERE idprod=?", array($id));

        $qtited=$depart['qtite']+$qtite;

        $qtitestock=$depart['qtite'];

        if ($qtitestock<0) {

        $qtitestock=0;
        }

        $previentstock=$depart['pr'];


        if (empty($previentstock)) {

            $qtitestock=0;
        }

        $pr=($qtite*$pr+$qtitestock*$depart['pr'])/($qtite+$qtitestock);

        if (!empty($pv)) {
        $DB->insert("UPDATE `".$nomtab1."` SET quantite= ?, prix_revient=?, prix_vente=? WHERE idprod = ?", array($qtited, $pr, $pv, $id));      
        }else{

        $DB->insert("UPDATE `".$nomtab1."` SET quantite= ?, prix_revient=? WHERE idprod = ?", array($qtited, $pr, $id));
        }
        $DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, idnomstock, coment, dateop) VALUES(?, ?, ?, ?, ?, ?, now())', array($id, 'recepf', 'entree', $qtite, $idstock1, $bl));

        $DB->insert('INSERT INTO achat (id_produitfac, numcmd, numfact, fournisseur, designation, quantite, taux, pachat, previent, pvente, etat, idstockliv, datefact, datecmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array($id, $bl, $bl, $_SESSION['idclient'], $designation, $qtite, 1, $pra, $pr, 0, 'livre', $lieuvente, $_SESSION['datef'])); ?>

        <div class="alert alert-success">Produit approvisionné avec sucèe!!!</div> <?php

    }?>

    <div class="row">

        <div class="col-sm-12 col-md-7 px-0">

            <table class="table table-hover table-bordered table-striped table-responsive text-center align-middle">

                <form method="GET" class="form">
                    <thead class="sticky-top bg-secondary  text-center"><?php 

                        if (isset($_GET['bl'])) {
                            $_SESSION['bl']=$_GET['bl'];
                            $_SESSION['idclient']=$_GET['idclient'];
                            $_SESSION['datef']=$_GET['datef'];
                        }?>

                        <tr>
                          <th colspan="8">
                            Approvisionnement de la facture BL N° <?=$_SESSION['bl'].' de '.$panier->nomClient($_SESSION['idclient']);?>
                            <a class="btn btn-info" href="editionfacturefournisseur.php">Liste des Factures</a>
                          </th>
                        </tr>

                        <tr>

                            <th colspan="7">
                                <div class="d-flex justify-content-between">
                                    <input class="form-control" type = "search" name = "terme" placeholder="rechercher un produit" onchange="this.form.submit()"/>
                                    <button class="btn btn-primary" type="submit">Rechercher</button>
                                </div>
                        </th>
                        </tr> 
                        <tr>
                            <th>N°</th>
                            <th>Référence</th>
                            <th>Qtité</th>
                            <th>P Achat</th>
                            <th>P Revient</th>
                            <th>P Vente</th>
                            <th>Magasin Reception</th>
                            <th></th>  
                        </tr>
                    </thead>
                </form>

                <tbody>

                    <?php
                    $tot_achat=0;
                    $tot_revient=0;
                    $tot_vente=0;
                    $qtiteR=0;
                    $qtiteS=0;

                    if (!isset($_GET['termeliste'])) {

                    if (isset($_GET['terme'])) {

                        if (isset($_GET["terme"])){

                            $_GET["terme"] = htmlspecialchars($_GET["terme"]); //pour sécuriser le formulaire contre les failles html
                            $terme = $_GET['terme'];
                            $terme = trim($terme); //pour supprimer les espaces dans la requête de l'internaute
                            $terme = strip_tags($terme); //pour supprimer les balises html dans la requête

                            $_SESSION['terme']=$terme;
                        }

                        if (isset($terme)){

                            $terme = strtolower($terme);
                            $products = $DB->query("SELECT * FROM productslist WHERE designation LIKE ? OR designation LIKE ? order by(designation)",array("%".$terme."%", "%".$terme."%"));
                        }else{

                        $message = "Vous devez entrer votre requete dans la barre de recherche";

                        }

                        if (empty($products)) {?>

                        <div class="alert alert-warning">Produit indisponible<a href="ajout.php">Ajouter le produit</a></div><?php

                        }

                    }else{

                        if (!empty($_SESSION['terme'])) {
                        
                        $products = $DB->query("SELECT * FROM productslist WHERE designation LIKE ? OR designation LIKE ? order by(designation)",array("%".$_SESSION['terme']."%", "%".$_SESSION['terme']."%"));

                        }else{

                        $products = $DB->query("SELECT * FROM productslist order by(designation) LIMIT 50");
                        }
                    }
                    }else{

                    $products = $DB->query("SELECT * FROM productslist WHERE id= ? order by(designation)",array($_GET['termeliste']));
                    }

                    if (!empty($products)) {

                        foreach ($products as $key=> $product){

                            if ($product->type=='paquet') {
                            $color='green';
                            }elseif ($product->type=='detail') {
                            $color='blue';
                            }else{
                            $color='';
                            }?>

                            <tr>
                                <td><?=$key+1;?></td>  

                                <td class="text-<?=$color;?>"><?= ucwords(strtolower($product->designation)); ?></td>

                                <form class="form" method="POST"><?php 
                                    if ($_SESSION['level']>6) {?>

                                    <td><input class="form-control" type="number" name="qtiteap" min="-100" /><input class="form-control" type="hidden" name="idap" value="<?=$product->id;?>"> <input class="form-control" type="hidden" name="bl" value="<?=$_SESSION['bl'];?>"></td><?php 
                                    }else{?>

                                    <td><input class="form-control" type="number" name="qtiteap" min="0" /><input class="form-control" type="hidden" name="idap" value="<?=$product->id;?>"></td><?php

                                    }?>

                                    <td><input class="form-control" type="text" name="pa" min="0" required /></td>
                                    <td><input class="form-control" type="text" name="pr" min="0" /></td>
                                    <td><input class="form-control" type="text" name="pv" min="0" /></td>

                                    <td>
                                    <select class="form-select" name="departap" required="">
                                        <option value="<?=$panier->nomStock($_SESSION['lieuvente'])[2];?>"><?=$panier->nomStock($_SESSION['lieuvente'])[0];?></option><?php 

                                        if ($_SESSION['level']>=6) {

                                          foreach($panier->listeStock() as $value){?>

                                            <option value="<?=$value->id;?>"><?=ucwords(strtolower($value->nomstock));?></option><?php

                                          }
                                        }?>

                                    </select>
                                    </td>

                                    <td><button class="btn btn-success" type="submit" name="validap" value="Approvisionner"onclick="return alerteT();" >Approv</td>
                                </form>                      


                            </tr><?php
                        }
                    }?>


                </tbody>
            </table>
        </div>

        <div class="col-sm-12 col-md-5 px-0 mx-0">
            <div class="container-fluid"  style="overflow: auto;">
                <table class="table table-hover table-bordered table-striped table-responsive text-center align-middle">

                    <thead class="sticky-top bg-secondary  text-center">

                        <tr><th colspan="6">Produits approvionnés facture BL N° <?=$_SESSION['bl'].' de '.$panier->nomClient($_SESSION['idclient']);?></th>

                        <tr>
                        <th>N°</th>
                        <th>Date</th>
                        <th>Référence</th>
                        <th>Qtité</th>
                        <th>Stock</th>
                        <th></th>
                        </tr>

                    </thead>

                    <tbody><?php
                
                        $cumulmontant=0;
                        $zero=0;

                        $products= $DB->query("SELECT *FROM stockmouv WHERE coment='{$_SESSION['bl']}' order by(dateop) desc");


                        $qtitetot=0;
                        foreach ($products as $keyd=> $product ){

                            $qtitetot+=$product->quantitemouv;?>

                            <tr>
                            <td><?= $keyd+1; ?></td>
                            <td><?= (new dateTime($product->dateop))->format("d/m/Y"); ?></td>
                            <td><?=$panier->nomProduit($product->idstock); ?></td>
                            <td><?=$product->quantitemouv; ?></td>
                            <td><?=$panier->nomStock($product->idnomstock)[0]; ?></td>

                            <td><a class="btn btn-danger" onclick="return alerteS();" href="?deletevers=<?=$product->id;?>&idprod=<?=$product->idstock;?>&dateop=<?=$product->dateop;?>&qtite=<?=$product->quantitemouv;?>&depart=<?=$product->idnomstock;?>">Annuler</a></td>
                            </tr><?php 
                        }?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="3">Totaux</th>
                            <th><?=$qtitetot;?></th>
                        </tr>
                    </tfoot>

                </table>
            </div>
            
        </div>    
    </div>
</div>

<?php require 'footer.php';?> 

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function(){
        $('#search-user').keyup(function(){
            $('#result-search').html("");

            var utilisateur = $(this).val();

            if (utilisateur!='') {
                $.ajax({
                    type: 'GET',
                    url: 'rechercheproduit.php?edition',
                    data: 'user=' + encodeURIComponent(utilisateur),
                    success: function(data){
                        if(data != ""){
                          $('#result-search').append(data);
                        }else{
                          document.getElementById('result-search').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
                        }
                    }
                })
            }
      
        });
    });
</script>


<script type="text/javascript">
    function alerteS(){
        return(confirm('Valider la suppression'));
    }

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }

    function alerteT(){
        return(confirm('Confirmer le transfert des produits'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }

    window.onload = function() { 
        for(var i = 0, l = document.getElementsByTagName('input').length; i < l; i++) { 
            if(document.getElementsByTagName('input').item(i).type == 'text') { 
                document.getElementsByTagName('input').item(i).setAttribute('autocomplete', 'off'); 
            }; 
        }; 
    };

</script>  