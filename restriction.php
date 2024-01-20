<?php
require '_header.php'
?><!DOCTYPE html>
<html lang="fr">

<head>
    <title>logescom-ms</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta content="Page par défaut" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
    <link rel="stylesheet" href="css/formulaire.css" type="text/css" media="screen" charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    
    
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="deconnexion.php"><img src="css/img/deconn.jpg" width="30" alt="damko"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="choix.php">Accueil</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="versement.php?client">Entrées</a>
            </li><?php

              if ($_SESSION['statut']=='responsable' or $_SESSION['statut']=='admin') {?>

                <li class="nav-item">
                  <a class="nav-link" href="dec.php?client">Sorties</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="devise.php">Devise</a>
                </li>           
                
                <li class="nav-item">
                  <a class="nav-link" href="top5.php">Statistiques</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="promotion.php">Promotion</a>
                </li><?php 
              }?>

            <li class="nav-item">
              <a class="nav-link" href="comptasemaine.php">Comptabilite</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="bulletin.php?compte">Compte</a>
            </li>


          </ul>
          <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
          </form>
        </div>
      </div>
    </nav>
    
    <div class="container-fluid">
      <div class="row"><?php 
        if (isset($_SESSION['pseudo'])) {
          $bdd='limitecredit';   

          $DB->insert(" CREATE TABLE IF NOT EXISTS `limitecredit` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `montant` double NOT NULL DEFAULT '1000000000000',
            `idclient` int(11) NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8; ");

          $pseudo=$_SESSION['pseudo'];

          $products = $DB->querysI('SELECT statut FROM login WHERE pseudo=? and type=?',array($pseudo, 'personnel'));


          if (isset($_GET['restriction'])) {

            $id=$panier->h($_GET['restriction']);
            $etat='bloque';
            
            $DB->insert("UPDATE client SET restriction = ? WHERE id = ?",array($etat, $id));
          }

          if (isset($_GET['debloque'])) {

            $id=$panier->h($_GET['debloque']);
            $etat='ok';
            
            $DB->insert("UPDATE client SET restriction = ? WHERE id = ?",array($etat, $id));
          }
          if (isset($_POST['limitcredit'])) {
            $montant=$panier->h($panier->espace($_POST['montantlimite']));
            $client=$panier->h($_POST['client']);
            $verif=$DB->querys("SELECT id from limitecredit where idclient='{$client}' ");
            if (empty($verif['id'])) {
              $DB->insert("INSERT INTO limitecredit (idclient, montant)VALUES(?, ?)", array($client, $montant));
            }else{
              $DB->insert("UPDATE limitecredit SET montant = '{$montant}' where idclient='{$client}' ");
            }
            
          }


          if (!isset($_GET['ajoutc'])) {

            if (isset($_GET['client'])) {

              if (isset($_GET['clientsearch'])) {

                $products= $DB->query("SELECT *FROM client where id='{$_GET['clientsearch']}'");

              }else{

                $type1='client';
                $type2='clientf';

                $products= $DB->query("SELECT *FROM client where typeclient='{$type1}' or typeclient='{$type2}' order by(nom_client) ");          
              }
              foreach ($products as $key=> $product ){
                $prodcredit= $DB->querys("SELECT *FROM limitecredit where idclient='{$product->id}' ");
                if (empty($prodcredit['id'])) {
                  $DB->insert("INSERT INTO limitecredit (idclient, montant)VALUES(?, ?)", array($product->id, 1000000000));
                }
              }?>

              <!-- <table class="table table-hover table-bordered table-striped table-responsive text-center w-50 my-2">

                <thead>

                  <tr>
                    <th colspan="2">Limite Créances Client</th>            
                  </tr>

                  <tr>
                    <th>Montant Limite</th>
                    <th>Action</th>
                  </tr>

                </thead>

                <tbody>
                  <form class="form" method="POST" action="restriction.php?client">
                    <tr>
                      <td><input class="form-control text-center fs-5" type="text" name="montantlimite" value="<?=$montantlimite;?>"/></td>
                      <td><button class="btn btn-success" type="submit" name="limiteCredit" onclick="return alerteC();">Valider</button></td>
                    </tr>
                  </form>

                </tbody>

              </table> -->

              <div class="row">
                <div class="col-sm-12 col-md-6">
                <table class="table table-hover table-bordered table-striped table-responsive text-center my-2">

                  <thead>

                    <tr>
                      <th colspan="4">
                        <input  class="form-control" id="search-user" type="text" name="clientsearch" placeholder="rechercher un client" />
                        <div class="bg-danger " id="result-search"></div>
                      </th>

                      <th class="legende" colspan="6" height="30"><?php 
                        if (isset($_GET['clientsearch'])) {
                          $_SESSION['reclient']=$_GET['clientsearch'];
                        }?>

                        <?= "Liste des Clients  " ?>
                      </th>
                    </tr>

                    <tr>
                      <th>N°</th>
                      <th>Nom & Prénom</th>
                      <th>Téléphone</th>
                      <th>Solde Compte</th>
                      <th>Montant Limite</th>
                      <th>Action</th>
                    </tr>

                  </thead>

                  <tbody><?php

                    foreach ($products as $key=> $product ){
                      $prodcredit= $DB->querys("SELECT *FROM limitecredit where idclient='{$product->id}' ");
                      if (empty($prodcredit['id'])) {
                        $DB->insert("INSERT INTO limitecredit (idclient, montant)VALUES(?, ?)", array($product->id, 1000000000));
                      }
                      $montantlimite=number_format($prodcredit['montant'],0,',',' ');?>
                      <form class="form" method="POST">

                        <tr>
                          <td><?=$key+1;?></td>                        
                          <td class="text-start"><?=ucwords(strtolower($product->nom_client));?></td>
                          <td><?=$product->telephone; ?></td>
                          <td style="text-align: right; padding-right: 10px;"><?=number_format(-$panier->compteClient($product->id, 'gnf'),0,',',' '); ?></td>
                          <td>
                            <input class="form-control" type="text" value="<?=$montantlimite;?>" name="montantlimite">
                            <input class="form-control" type="hidden" value="<?=$product->id;?>" name="client">
                          </td>
                          <td><button class="btn btn-danger" type="submit" name="limitcredit" onclick="return alerteB();">Valider</button></td>
                        </tr>
                      </form><?php 
                    }?>

                  </tbody>

                  </table>
                </div>
                <div class="col-sm-12 col-md-6">              
                  <table class="table table-hover table-bordered table-striped table-responsive text-center my-2">

                    <thead>

                      <tr>
                        <th colspan="4">
                          <input  class="form-control" id="search-user" type="text" name="clientsearch" placeholder="rechercher un client" />
                          <div class="bg-danger " id="result-search"></div>
                        </th>

                        <th class="legende" colspan="6" height="30"><?php 
                          if (isset($_GET['clientsearch'])) {
                            $_SESSION['reclient']=$_GET['clientsearch'];
                          }?>

                          <?= "Liste des Clients  " ?>
                        </th>
                      </tr>

                      <tr>
                        <th>N°</th>
                        <th>Nom & Prénom</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Solde Compte</th>
                        <th>Action</th>
                      </tr>

                    </thead>

                    <tbody><?php

                      foreach ($products as $key=> $product ){?>

                        <tr>

                          <td><?=$key+1;?></td>                        
                          <td class="text-start"><?=ucwords(strtolower($product->nom_client));?></td>
                          <td><?=$product->telephone; ?></td>
                          <td><?=ucwords(strtolower($product->adresse)) ; ?></td>

                          <td style="text-align: right; padding-right: 10px;"><?=number_format(-$panier->compteClient($product->id, 'gnf'),0,',',' '); ?></td>

                          <td><?php 
                            if ($product->restriction =='ok') {?>

                              <a  class="btn btn-danger" onclick="return alerteB();" href="restriction.php?restriction=<?=$product->id;?>&client">Bloquer</a><?php

                            }else{?>

                              <a class="btn btn-success" onclick="return alerteD();" href="restriction.php?debloque=<?=$product->id;?>&client">Débloquer</a><?php 
                            }?>
                          </td>

                        </tr><?php 
                      }?>

                    </tbody>

                  </table>
                </div>
              </div><?php
            }

          }else{

            echo "Vous n'avez pas toutes les autorisations réquises";

          }

        }else{

        }?>
      </div>
    </div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script><?php 

if (isset($_GET['client'])) {?>

  <script>
    $(document).ready(function(){
        $('#search-user').keyup(function(){
            $('#result-search').html("");

            var utilisateur = $(this).val();

            if (utilisateur!='') {
                $.ajax({
                    type: 'GET',
                    url: 'recherche_utilisateur.php?clientrest',
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
  </script><?php 
}?>

<script type="text/javascript">
    function alerteB(){
        return(confirm('Confirmer le bloquage de ce compte'));
    }

    function alerteD(){
        return(confirm('Confirmer le débloquage'));
    }

    function alerteC(){
        return(confirm('Confirmer la restriction'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }

</script>
