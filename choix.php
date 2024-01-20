<?php require '_header.php';?>

<!DOCTYPE html>
<html>
<head>
    <title>Logescom-ms</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/8df11ad090.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
</head>
<body><?php 

    if (isset($_SESSION['pseudo'])){
        $pseudo=$_SESSION['pseudo'];
        $bdd='limitecredit';   

        $DB->insert(" CREATE TABLE IF NOT EXISTS `limitecredit` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `montant` double NOT NULL DEFAULT '1000000000000',
            `idclient` int(11) NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8; ");

        $type1='client';
        $type2='clientf';

        $prodclient= $DB->query("SELECT *FROM client where typeclient='{$type1}' or typeclient='{$type2}' order by(nom_client) ");          
        
        foreach ($prodclient as $key=> $product ){
            $prodcredit= $DB->querys("SELECT *FROM limitecredit where idclient='{$product->id}' ");
            if (empty($prodcredit['id'])) {
                $DB->insert("INSERT INTO limitecredit (idclient, montant)VALUES(?, ?)", array($product->id, 1000000000));
            }
        }

        $personnel = $DB->querysI('SELECT statut, nom, level FROM login WHERE pseudo= :PSEUDO', array('PSEUDO'=>$pseudo));?>
        <div class="container-fluid m-0 p-0">   

            <div class="rowHeader m-0 p-0" style="background-color:#2f2c2c; display:flex; justify-content:space-between;align-items: center; height:55px; ">
                <div><a class="px-2" href="deconnexion.php"><img src="css/img/deconn.jpg" alt="logout" width="40"style="border-radius:5px;" ></a></div> 
                <div>
                    <form class="form" method="POST" action="recherche.php">
                        <div class="row">
                            <div class="col-8"><input class="form-control"  type ="search" name = "rechercher" placeholder="rechercher un ticket"></div>
                            <div class="col-4"><input class="form-control"  type ="submit" name = "s" value = "Rechercher"></div>
                        </div>
                    </form>
                </div>

                <div class="btn btn-success mx-1"><?="Compte de ".ucwords($personnel['nom']);?> </div> 

            </div>

            <div><?php 

                if (isset($_POST['magasin'])) {

                    $_SESSION['lieuventealerte']=$_POST['magasin'];
                }else{

                    $_SESSION['lieuventealerte']=$_SESSION['lieuvente'];

                }

                $nomtab=$panier->nomStock($_SESSION['lieuventealerte'])[1];

                $idstock=$panier->nomStock($_SESSION['lieuventealerte'])[2];            

                //require 'indicateur.php';?>
                    
            </div><?php

            $adress=$DB->querys('SELECT * FROM adresse ');?>

            <div id="home">

                <div class="choix" style="background-color:#534444;"><?php 
                    if ($user['statut'] != 'superviseur') {?>

                        <div class="option"><a href="index.php">
                            <div class="picturec"><img src="css/img/achat.jpg"></div>
                            <div class="descript_option">Ventes</div></a>
                        </div><?php 
                    }?>


                    <div class="option"><a href="livraisonachat.php">
                        <div class="picturec"><img src="css/img/livraison.jpg"></div>
                        <div class="descript_option">Livraison</div></a>
                    </div>

                    <div class="option"><a href="client.php">
                        <div class="picturec"><img src="css/img/client.jpg"></div>
                        <div class="descript_option">Clients</div></a>
                    </div>

                    <?php 
                    if ($user['statut'] != 'superviseur') {?>

                        <div class="option"><a href="commande.php">
                            <div class="picturec"><img src="css/img/approv.jpg"></div>
                            <div class="descript_option">Approvisionnment</div></a>
                        </div><?php 
                    }?>
                    
                    <div class="option"><a href="editionfacturefournisseur.php?recette">
                        <div class="picturec"><img src="css/img/achatfournisseur.jpg"></div>
                        <div class="descript_option">Achat Four...</div></a>
                    </div><?php  

                    if ($_SESSION['level']>=6) {?>

                        <div class="option"><a href="ajoutstock.php">
                            <div class="picturec"><img src="css/img/stock.jpg"></div>
                            <div class="descript_option">Gestion Stock</div></a>
                        </div> 

                        <div class="option"><a href="dec.php?client">
                            <div class="picturec"><img src="css/img/retrait.jpg"></div>
                            <div class="descript_option">Sorties</div></a>
                        </div>

                        <div class="option"><a href="banque.php">
                            <div class="picturec"><img src="css/img/transfert.jpg"></div>
                            <div class="descript_option">Transfert des fonds</div></a>
                        </div>

                        <div class="option"><a href="personnel.php?enseig">
                            <div class="picturec"><img src="css/img/personnel.jpg"></div>
                            <div class="descript_option">Personnels</div></a>
                        </div><?php 
                    }?> 

                    <div class="option"><a href="comptasemaine.php">
                        <div class="picturec"><img src="css/img/compta.jpg"></div>
                        <div class="descript_option">Comptabilite</div></a>
                    </div>

                    <div class="option"><a href="versement.php?client">
                        <div class="picturec"><img src="css/img/versement.jpg"></div>
                        <div class="descript_option">Entrée</div></a>
                    </div>               

                    <div class="option"><a href="bulletin.php?compte">
                        <div class="picturec"><img src="css/img/compte.jpg"></div>
                        <div class="descript_option">Compte</div></a>
                    </div><?php                     

                        if ($_SESSION['level']>6 and $user['statut'] != 'superviseur' ) {?>

                            <div class="option"><a href="restriction.php?client">
                                <div class="picturec"><img src="css/img/restriction.jpg"></div>
                                <div class="descript_option">Restrictions</div></a>
                            </div><?php 
                        }
                    ?>                

                </div>
            </div>
            
            <div class="row">
                <div class="col-sm-12 col-md-6">            
                    <table class="table table-hover table-bordered table-striped table-responsive text-center my-4">
                        <thead>
                            <tr>
                                <th colspan="6" scope="col" class="text-center bg-danger text-white"><label>Liste des créanciers à relancer / Aucune Opération depuis 30 jours</label> <a class="btn btn-light" href="printcreancier.php" target="_blank"><i class="fa-solid fa-file-pdf fs-4" style="color: #932f34;"></a></th>
                            </tr>
                            <tr>
                                <th scope="col" class="text-center">N°</th>
                                <th scope="col" class="text-center">Prénom & Nom</th>
                                <th scope="col" class="text-center">Téléphone</th>
                                <th scope="col" class="text-center">Solde</th>
                                <th scope="col" class="text-center">Dernière Op</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody><?php
                            $soldeCumul=0;
                            $type1='client';
                            $type2='clientf';
                            if ($_SESSION['level']>6) {
                                $prodclient = $DB->query("SELECT *FROM client where (typeclient='{$type1}' or typeclient='{$type2}') order by(nom_client) ");
                            }else{
                                $prodclient = $DB->query("SELECT *FROM client where positionc='{$_SESSION['lieuvente']}' and (typeclient='{$type1}' or typeclient='{$type2}') order by(nom_client) ");
                            }
                            $i=1;
                            foreach ($prodclient as $key => $value) {
                                $prodmax= $DB->querys("SELECT max(date_versement) as datev FROM bulletin where nom_client='{$value->id}' ");

                                $now = date('Y-m-d');
                                $datederniervers = $prodmax['datev'];

                                $now = new DateTime( $now );
                                $now = $now->format('Ymd');       

                                $datederniervers = new DateTime($datederniervers);
                                $datederniervers = $datederniervers->format('Ymd');

                                $jourd=(new dateTime($now))->format("d");
                                $moisd=(new dateTime($now))->format("m");
                                $anneed=(new dateTime($now))->format("Y");

                                $datealertemin = date("Ymd", mktime(0, 0, 0, $moisd, $jourd-$panier->delaialerte,   $anneed));

                                $datealerte = date("Ymd", mktime(0, 0, 0, $moisd, $jourd-30,   $anneed));

                                $delai=$panier->delai;

                                $delaialerte=$panier->delaialerte;
                                if ($panier->compteClient($value->id, 'gnf')<0) {
                                    if ($datealerte>=$datederniervers) {
                                        $soldeCumul+=(-$panier->compteClient($value->id, 'gnf')); ?>
                                        <tr>
                                            <td><?=$i;?></td>
                                            <td class="text-start"><?=ucwords(strtolower($value->nom_client));?></td>
                                            <td><?=$value->telephone;?></td>
                                            <td class="text-end"><?=number_format(-$panier->compteClient($value->id, 'gnf'),0,',',' ');?></td>
                                            <td><?=(new dateTime($datederniervers))->format("d/m/Y");?></td>
                                            <td>
                                                <a class="btn btn-success m-auto" href="clientgestion.php?suiviclient=<?=$value->id;?>&nomclient=<?=$value->nom_client;?>">Consulter</a>
                                            </td>
                                        </tr><?php
                                        $i++;
                                    }
                                }
                            }?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Cumul Créances</th>
                                <th class="text-end"><?=number_format($soldeCumul,0,',',' ');?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div><?php
                if ($_SESSION['level']>=6) {?>                    
                        <div class="col-sm-12 col-md-6 py-4 px-5"  style="overflow: auto;"><?="<img src='./statventegenerale.php' />"; ?></div>
                    <?php 
                }?>
            </div>
        </div><?php
    }else{

        header("Location: form_connexion.php");

    }?>
    
</body>
</html>