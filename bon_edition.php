<?php require 'headerv2.php';?>

<div class="container-fluid mx-0 px-0"><?php 
    $bdd='bon_commande_produit';   

    $DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_prod` int(10) DEFAULT NULL,
        `id_bon` varchar(100) DEFAULT NULL,
        `quantite` float DEFAULT '0',
        `prix_achat` double DEFAULT NULL,
        `dateop` datetime DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ");

    if (isset($_GET['deletevers'])) {

        $id=$_GET['deletevers'];

        $numero=$_GET['idprod'];
        $depart=$_GET['depart'];
        $nomtabdep=$panier->nomStock($depart)[1];
        $qtitesup=$_GET['qtite'];
        $dateop=$_GET['dateop'];
        $bl=$_SESSION['bl_bon'];

        $DB->delete('DELETE FROM bon_commande_produit WHERE id = ?', array($id));?>

        <div class="alert alert-success">Le produit a été bien annulé</div><?php
    }


    if (isset($_POST['qtiteap'])) {
        $id=$panier->h($_POST['idap']);
        $pa=$panier->h($panier->espace($_POST['pa']));
        

        $designation=$panier->nomProduit($id);

        if (empty($_POST['pa'])) {
        $pra=0;
        }else{

            $pra=$panier->h($_POST['pa']);
        }
        $bl=$_SESSION['bl_bon'];
        $qtite=$panier->h($_POST['qtiteap']); 
        $DB->delete('DELETE FROM bon_commande_produit WHERE id_prod = ? and id_bon = ?', array($id, $bl));       
        $DB->insert('INSERT INTO bon_commande_produit (id_prod, quantite, prix_achat, id_bon, dateop) VALUES(?, ?, ?, ?, now())', array($id, $qtite, $pa, $bl));?>

        <div class="alert alert-success">Produit ajputé avec sucèe!!!</div> <?php

    }?>

    <div class="row">

        <div class="col-sm-12 col-md-7 px-0">

            <table class="table table-hover table-bordered table-striped table-responsive text-center align-middle">

                <form method="GET" class="form">
                    <thead class="sticky-top bg-secondary  text-center"><?php 

                        if (isset($_GET['bl'])) {
                            $_SESSION['bl_bon']=$_GET['bl'];
                            $_SESSION['idclient_bon']=$_GET['idclient'];
                            $_SESSION['datef_bon']=$_GET['datef'];
                        }?>

                        <tr>
                          <th colspan="5">
                            Ajout des produits sur le Bon N° <?=$_SESSION['bl_bon'].' de '.$panier->nomClient($_SESSION['idclient_bon']);?>
                            <a class="btn btn-info" href="bon_commande.php">Liste des Bons</a>
                          </th>
                        </tr>

                        <tr>

                            <th colspan="5">
                                <div class="d-flex justify-content-between">
                                    <input class="form-control" type = "search" name = "terme" placeholder="rechercher un produit" onchange="this.form.submit()"/>
                                    <button class="btn btn-primary" type="submit">Rechercher</button>
                                </div>
                        </th>
                        </tr> 
                        <tr>
                            <th>N°</th>
                            <th>Désignation</th>
                            <th>Qtité</th>
                            <th>Ancien Prix-Achat</th>
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
                            $products = $DB->query("SELECT * FROM productslist WHERE designation LIKE ? OR Marque LIKE ? order by(designation)",array("%".$terme."%", "%".$terme."%"));
                        }else{

                        $message = "Vous devez entrer votre requete dans la barre de recherche";

                        }

                        if (empty($products)) {?>

                        <div class="alert alert-warning">Produit indisponible<a href="ajout.php">Ajouter le produit</a></div><?php

                        }

                    }else{

                        if (!empty($_SESSION['terme'])) {
                        
                        $products = $DB->query("SELECT * FROM productslist WHERE designation LIKE ? OR Marque LIKE ? order by(designation)",array("%".$_SESSION['terme']."%", "%".$_SESSION['terme']."%"));

                        }else{

                        $products = $DB->query("SELECT * FROM productslist order by(designation) LIMIT 50");
                        }
                    }
                    }else{

                    $products = $DB->query("SELECT * FROM productslist WHERE id= ? order by(designation)",array($_GET['termeliste']));
                    }

                    if (!empty($products)) {

                        foreach ($products as $key=> $product){
                            //var_dump($_SESSION['idclient_bon'],$product->id);

                            $prodmax=$DB->querys("SELECT max(id) as idmax FROM achat where fournisseur='{$_SESSION['idclient_bon']}' and id_produitfac='{$product->id}' ");

                            $prodprix=$DB->querys("SELECT (pachat*taux) as pa FROM achat where fournisseur='{$_SESSION['idclient_bon']}' and id_produitfac='{$product->id}' and id='{$prodmax['idmax']}' ");
                            $ancien_prix=$prodprix['pa'];


                            if ($product->type=='paquet') {
                            $color='green';
                            }elseif ($product->type=='detail') {
                            $color='blue';
                            }else{
                            $color='';
                            }?>

                            <tr>
                                <td><?=$key+1;?></td>  

                                <td class="text-<?=$color;?> text-start"><?= ucwords(strtolower($product->designation)); ?></td>

                                <form class="form" method="POST"><?php 
                                    if ($_SESSION['level']>6) {?>

                                        <td><input class="form-control" type="number" name="qtiteap" min="-100" /><input class="form-control" type="hidden" name="idap" value="<?=$product->id;?>"> <input class="form-control" type="hidden" name="bl" value="<?=$_SESSION['bl_bon'];?>"></td><?php 
                                    }else{?>

                                        <td><input class="form-control" type="number" name="qtiteap" min="0" /><input class="form-control" type="hidden" name="idap" value="<?=$product->id;?>"></td><?php

                                    }?>

                                    <td><input class="form-control text-end" type="text" name="pa" value="<?=number_format($ancien_prix,0,',',' ');?>" min="0" /></td>
                                    <td><button class="btn btn-success" type="submit" name="validap" onclick="return alerteT();" >Ajouter</td>
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

                        <tr>
                            <th colspan="6">
                                Produits ajoutés sur le Bon N° <?=$_SESSION['bl_bon'].' de '.$panier->nomClient($_SESSION['idclient_bon']);?>
                                <a target="_blank" class="btn btn-warning" href="bon_commande_pdf.php?id_bon=<?=$_SESSION['bl_bon'];?>&client=<?=$_SESSION['idclient_bon'];?>&prix"><i class="fa-solid fa-file-pdf"></i></a>
                                <a target="_blank" class="btn btn-warning" href="bon_commande_pdf.php?id_bon=<?=$_SESSION['bl_bon'];?>&client=<?=$_SESSION['idclient_bon'];?>"><i class="fa-solid fa-file-pdf"></i></a>
                            </th>

                        <tr>
                        <th>N°</th>
                        <th>Date</th>
                        <th>Désignation</th>
                        <th>Qtité</th>
                        <th>Prix</th>
                        <th></th>
                        </tr>

                    </thead>

                    <tbody><?php
                
                        $cumulmontant=0;
                        $zero=0;

                        $products= $DB->query("SELECT *FROM bon_commande_produit WHERE id_bon='{$_SESSION['bl_bon']}' order by(dateop) desc");


                        $qtitetot=0;
                        foreach ($products as $keyd=> $product ){

                            $qtitetot+=$product->quantite;?>

                            <tr>
                            <td><?= $keyd+1; ?></td>
                            <td><?= (new dateTime($product->dateop))->format("d/m/Y"); ?></td>
                            <td><?=$panier->nomProduit($product->id_prod); ?></td>
                            <td><?=$product->quantite; ?></td>
                            <td><?=number_format($product->prix_achat,0,',',' '); ?></td>
                            <td><a class="btn btn-danger" onclick="return alerteS();" href="?deletevers=<?=$product->id;?>&idprod=<?=$product->id_prod;?>&dateop=<?=$product->dateop;?>&qtite=<?=$product->quantite;?>&depart=<?=$product->id_bon;?>">Annuler</a></td>
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