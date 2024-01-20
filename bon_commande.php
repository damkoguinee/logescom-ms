<?php require 'headerv2.php';

if (isset($_SESSION['pseudo'])) {

  $bdd='bon_commande';   

  $DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `numedit` varchar(150),
    `id_client` int(10) DEFAULT NULL,
    `libelle` varchar(150),
    `bl` varchar(150),
    `montant` double DEFAULT NULL,
    `devise` varchar(10),
    `lieuvente` int(2) DEFAULT NULL,
    `dateop` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ");

  ?>
  <div class="container-fluid my-1">
    <div class="row"><?php
      require 'navfournisseur.php';?>
      <div class="col-sm-12 col-md-10"><?php  

        if ($_SESSION['level']>=3) {

          if (!isset($_POST['magasin'])) {

            if (!isset($_POST['j1'])) {

              $_SESSION['date']=date("Ymd");  
              $dates = $_SESSION['date'];
              $dates = new DateTime( $dates );
              $dates = $dates->format('Ymd'); 
              $_SESSION['date']=$dates;
              $_SESSION['date1']=$dates;
              $_SESSION['date2']=$dates;
              $_SESSION['dates1']=$dates; 

            }else{

              $_SESSION['date01']=$_POST['j1'];
              $_SESSION['date1'] = new DateTime($_SESSION['date01']);
              $_SESSION['date1'] = $_SESSION['date1']->format('Ymd');
              
              $_SESSION['date02']=$_POST['j2'];
              $_SESSION['date2'] = new DateTime($_SESSION['date02']);
              $_SESSION['date2'] = $_SESSION['date2']->format('Ymd');

              $_SESSION['dates1']=(new DateTime($_SESSION['date01']))->format('d/m/Y');
              $_SESSION['dates2']=(new DateTime($_SESSION['date02']))->format('d/m/Y');  
            }
          }

          if (isset($_POST['j2'])) {

            $datenormale='entre le '.$_SESSION['dates1'].' et le '.$_SESSION['dates2'];

          }else{

            $datenormale=(new DateTime($_SESSION['date']))->format('d/m/Y');
          }

          if (isset($_POST['clientliv'])) {
            $_SESSION['clientliv']=$_POST['clientliv'];
          }


          if (isset($_GET['deleteret'])) {

            $DB->delete("DELETE from bon_commande where numedit='{$_GET['deleteret']}'");

            $DB->delete("DELETE from bulletin where numero='{$_GET['deleteret']}'");?>

            <div class="alert alert-success">Suppression reussie!!</div><?php 
          }


          if (isset($_POST["valid"])) {

            if (empty($_POST["client"])) {?>

              <div class="alert alert-warning">Les Champs sont vides</div><?php

            }else{
              $numdec = $DB->querys('SELECT max(id) AS id FROM bon_commande');
              $numdec=$numdec['id']+1;

              $montant=$panier->h($_POST['montant']);
              $bl=$panier->h($_POST['bl']);
              //$nature=$panier->h($_POST['nature']);
              $devise=$panier->h($_POST['devise']);
              $client=$panier->h($_POST['client']);
              $motif=$panier->h($_POST['motif']);
              $taux=1;

              $lieuventeret=$_SESSION['lieuvente']; 
              $dateop=$_POST['datedep'];

              $numdec='editf'.$numdec;        

              $prodverif = $DB->querys("SELECT id FROM bon_commande where bl='{$bl}' ");

              if (!empty($prodverif['id'])) {?>

                <div class="alert alert-warning">Ce numero BL existe dejà!!!</div><?php
                
              }else{

                if (empty($dateop)) {

                  $DB->insert('INSERT INTO bon_commande (numedit, id_client, montant, bl, libelle, devise, lieuvente, dateop) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array($numdec, $client, $montant, $bl,  $motif, $devise, $lieuventeret));

                  

                }else{ 

                  $DB->insert('INSERT INTO bon_commande (numedit, id_client, montant, bl, libelle, devise, lieuvente, dateop) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array($numdec, $client, $montant, $bl,  $motif, $devise, $lieuventeret, $dateop));

                  
                    
                }
                unset($_POST);
                unset($_GET);
                unset($_SESSION['searchclientvers']);
                ?>

                <div class="alert alert-success">Facture enregistrée avec succèe dans le compte selectionné!!!</div><?php 
              }

            }

          }else{

            
          }

          if (isset($_GET['searchclientvers']) ) {

              $_SESSION['searchclientvers']=$_GET['searchclientvers'];
          }

          if (isset($_GET['ajout']) or isset($_GET['searchclientvers'])) {?>
              <form class="form my-2" method="POST" enctype="multipart/form-data">

                  <fieldset>

                  <div class="row mb-1">
                      <div class="col-sm-12 col-md-6">

                          <label class="form-label">Fournisseur*</label>
                          <select class="form-select" type="text" name="client"><?php 

                          if (!empty($_SESSION['searchclientvers'])) {?>

                              <option value="<?=$_SESSION['searchclientvers'];?>"><?=$panier->nomClient($_SESSION['searchclientvers']);?></option><?php
                          }else{?>
                              <option></option><?php 
                          }

                          $type1='fournisseur';
                          $type2='clientf';

                          foreach($panier->clientF($type1, $type2) as $product){?>
                              <option value="<?=$product->id;?>"><?=$product->nom_client;?></option><?php
                          }?>
                          </select>
                      </div>
                      <div class="col-sm-12 col-md-4">

                          <label class="form-label">N°BL/Numero*</label>
                          <input class="form-control" type="text"   name="bl" required="">
                      </div>
                  </div>

                  <div class="row mb-1">

                      <div class="col-sm-12 col-md-4">
                          <label class="form-label">Libellé de la Facture*</label>
                          <input class="form-control" type="text"   name="motif" required="">
                      </div>
                      <div class="col-sm-12 col-md-4">
                      <label class="form-label">Devise*</label>
                      <select class="form-select" name="devise" required="" ><?php 
                          foreach ($panier->monnaie as $valuem) {?>
                              <option value="<?=$valuem;?>"><?=strtoupper($valuem);?></option><?php 
                          }?>
                      </select>
                  </div>
                  </div>

                  <div class="mb-1"><label class="form-label">Montant*</label>

                      <div class="container-fluid px-0 mb-1">
                          <div class="row">
                          <div class="col-sm-12 col-md-6">
                              <input class="form-control" id="numberconvert" type="number"   name="montant" value="0" min="0" required="">
                          </div>

                          <div class="col-sm-12 col-md-6">
                              <div class="text-danger fw-bold fs-4" id="convertnumber"></div>
                          </div>
                          </div>
                      </div>
                  </div>

                  <div class="row mb-1">
                      <div class="col-sm-12 col-md-4">
                          <label class="form-label">Date de la Facture</label>
                          <input class="form-control" type="date" name="datedep">
                      </div>
                  </div>
                  
                  <button class="btn btn-primary"  type="submit" name="valid" onclick="return alerteV();">Valider</button>
              </form><?php
          }

          if (isset($_POST["validup"])) {

              if (empty($_POST["client"]) or empty($_POST["montant"])) {?>
        
                <div class="alert alert-warning">Les Champs sont vides</div><?php
        
              }else{
        
                $numdec=$panier->h($_POST['numedit']);
                $blinit=$panier->h($_POST['blinit']);
                $montant=$panier->h($_POST['montant']);
                $bl=$panier->h($_POST['bl']);
                $devise=$panier->h($_POST['devise']);
                $client=$panier->h($_POST['client']);
                $motif=$panier->h($_POST['motif']);
                $taux=1;
        
                $lieuventeret=$_SESSION['lieuvente']; 
                $dateop=$_POST['datedep']; 
        
                if ($bl==$blinit) {
        
                  $DB->delete("DELETE from bon_commande where numedit='{$numdec}'");
        
                  $DB->delete("DELETE from bulletin where numero='{$numdec}'");
        
                  $DB->insert("UPDATE stockmouv SET coment= ? WHERE coment = ?", array($bl, $blinit)); 
                }    
        
                $prodverif = $DB->querys("SELECT id FROM bon_commande where bl='{$bl}' ");
        
                  if (!empty($prodverif['id'])) {?>
        
                      <div class="alert alert-warning">Ce numero BL existe dejà!!!</div><?php
                  
                  }else{ 
        
                      $DB->delete("DELETE from bon_commande where numedit='{$numdec}'");
          
                      $DB->delete("DELETE from bulletin where numero='{$numdec}'");
          
                      $DB->insert("UPDATE stockmouv SET coment= ? WHERE coment = ?", array($bl, $blinit));         
                      if (empty($dateop)) {
          
                          $DB->insert('INSERT INTO bon_commande (numedit, id_client, montant, bl, libelle, devise, lieuvente, dateop) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array($numdec, $client, $montant, $bl,  $motif, $devise, $lieuventeret));
              
                      }else{ 
          
                          $DB->insert('INSERT INTO bon_commande (numedit, id_client, montant, bl, libelle, devise, lieuvente, dateop) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array($numdec, $client, $montant, $bl,  $motif, $devise, $lieuventeret, $dateop));
                          
                      }
                      unset($_POST);
                      unset($_GET);
                      unset($_SESSION['searchclientvers']);
                      ?>
        
                      <div class="alert alert-success">Facture modifiée avec succèe dans le compte selectionné!!!</div><?php 
                  }
        
              }
        
          }

          if (isset($_GET['update'])) {

            $prod = $DB->querys("SELECT *FROM bon_commande where numedit='{$_GET['update']}' ");

            $datefacture=(new dateTime($prod['dateop']))->format("Y-m-d");?>
              <form class="form my-2" method="POST" enctype="multipart/form-data">
                  <div class="row mb-1">
                      <div class="col-sm-12 col-md-6">

                          <label class="form-label">Fournisseur*</label>
                          <select class="form-select" type="text" name="client">
                          <option value="<?=$prod['id_client'];?>"><?=$panier->nomClient($prod['id_client']);?></option><?php 

                          if (!empty($_SESSION['searchclientvers'])) {?>

                              <option value="<?=$_SESSION['searchclientvers'];?>"><?=$panier->nomClient($_SESSION['searchclientvers']);?></option><?php
                          }else{?>
                              <option></option><?php 
                          }

                          $type1='fournisseur';
                          $type2='clientf';

                          foreach($panier->clientF($type1, $type2) as $product){?>
                              <option value="<?=$product->id;?>"><?=$product->nom_client;?></option><?php
                          }?>
                          </select>
                      </div>
                      <div class="col-sm-12 col-md-4">

                          <label class="form-label">N°BL/Numero*</label>
                          <input class="form-control" type="text"   name="bl" value="<?=$prod['bl'];?>" required="">
                          <input type="hidden"   name="blinit" value="<?=$prod['bl'];?>">
                          <input type="hidden"   name="numedit" value="<?=$prod['numedit'];?>">
                      </div>
                  </div>

                  <div class="row mb-1">
                      <div class="col-sm-12 col-md-4">
                          <label class="form-label">Libellé de la Facture*</label>
                          <input class="form-control" type="text"   name="motif" value="<?=$prod['libelle'];?>"  required="">
                      </div>
                      <div class="col-sm-12 col-md-4">
                      <label class="form-label">Devise*</label>
                      <select class="form-select" name="devise" required="" >
                          <option value="<?=$prod['devise'];?>"><?=$prod['devise'];?></option><?php 
                          foreach ($panier->monnaie as $valuem) {?>
                              <option value="<?=$valuem;?>"><?=strtoupper($valuem);?></option><?php 
                          }?>
                      </select>
                  </div>
                  </div>

                  <div class="mb-1"><label class="form-label">Montant*</label>

                      <div class="container-fluid px-0 mb-1">
                          <div class="row">
                          <div class="col-sm-12 col-md-6">
                              <input class="form-control" id="numberconvert" type="number"   name="montant" value="<?=$prod['montant'];?>" value="0" min="0" required="">
                          </div>

                          <div class="col-sm-12 col-md-6">
                              <div class="text-danger fw-bold fs-4" id="convertnumber"></div>
                          </div>
                          </div>
                      </div>
                  </div>

                  <div class="row mb-1">                  
                      <div class="col-sm-12 col-md-4">
                          <label class="form-label">Date de la Facture</label>
                          <input class="form-control" type="date" name="datedep">
                      </div>
                  </div>

                  <button class="btn btn-primary"  type="submit" name="validup" onclick="return alerteV();">Valider</button>
              </form><?php
          }

          if (!isset($_GET['ajout'])) {?>

            <div class="row"  style="overflow: auto;">

              <table class="table table-hover table-bordered table-striped table-responsive text-center align-middle">
                <thead class="sticky-top bg-secondary text-center">
                  <tr>
                    <th colspan="14"><?="factures fournisseurs";?>
                      <div class="d-flex justify-content-around">
                        <a class="btn btn-warning" href="?ajout">Enregistrer un bon de commande</a>
                        <form class="d-flex" method="get">
                          <input type="text" name="search" onchange="this.form.submit()" class="form-control">
                          <button class="btn btn-primary" type="submit">Rechercher</button>

                        </form>
                      </div>
                    </th></tr>

                  <tr>
                    <th>N°</th>
                    <th>Facture</th>
                    <th>Date</th>
                    <th>N°BL</th>
                    <th>libelle</th> 
                    <th>Collaborateur</th>                             
                    <th>Montant</th>
                    <th>Devise</th>
                    <th colspan="3">Actions</th>
                  </tr>

                </thead>

                <tbody><?php
                  if (isset($_GET['search'])) {
                    $terme = htmlspecialchars($_GET["search"]);
                    $products= $DB->query("SELECT *FROM bon_commande inner join client on id_client=client.id  WHERE (nom_client LIKE ? OR telephone LIKE ? OR bl LIKE ? ) and  lieuvente LIKE ? order by(bon_commande.id) DESC ", array("%".$terme."%", "%".$terme."%", "%".$terme."%", $_SESSION['lieuvente']));
                  }else{ 

                    if ($_SESSION['level']>6) {
                      $products= $DB->query("SELECT *FROM bon_commande  order by(id) DESC");
                    }else{
                      $products= $DB->query("SELECT *FROM bon_commande  WHERE lieuvente='{$_SESSION['lieuvente']}' order by(id) DESC ");
                    } 
                  }  

                  $montantgnf=0;
                  $montanteu=0;
                  $montantus=0;
                  $montantcfa=0;
                  $virement=0;
                  $cheque=0;
                  foreach ($products as $keyv=> $product ){

                    $prodverif= $DB->querys("SELECT *FROM stockmouv  WHERE coment='{$product->bl}' ");?>

                    <tr>
                      <td><?= $keyv+1; ?></td>
                      <td>
                        <a target="_blank" class="btn btn-warning" href="bon_commande_pdf.php?id_bon=<?=$product->bl;?>&client=<?=$product->id_client;?>&prix"><i class="fa-solid fa-file-pdf"></i></a>
                        <a target="_blank" class="btn btn-warning" href="bon_commande_pdf.php?id_bon=<?=$product->bl;?>&client=<?=$product->id_client;?>"><i class="fa-solid fa-file-pdf"></i></a>
                      </td>
                      <td><?=(new DateTime($product->dateop))->format("d/m/Y"); ?></td>
                      <td><?= strtoupper($product->bl); ?></td>
                      <td class="text-start"><?=$product->libelle; ?></td>
                      <td class="text-start"><?=strtoupper($panier->nomClient($product->id_client)); ?></td><?php

                        $montantgnf+=$product->montant;?>

                        <td class="text-end"><?= number_format($product->montant,0,',',' '); ?></td>
                        <td><?=$product->devise;?></td>
                        <td><?php if ($_SESSION['level']>=1){?><a onclick="return alerteV();" class="btn btn-success" href="bon_edition.php?reception=<?=$product->numedit;?>&bl=<?=$product->bl;?>&idclient=<?=$product->id_client;?>&datef=<?=$product->dateop;?>">Ajout Produits</a><?php };?></td>

                        <td><?php if ($_SESSION['level']>=6 ){?><a class="btn btn-warning" onclick="return alerteV();" href="?update=<?=$product->numedit;?>">Modifier</a><?php };?></td>

                        <td><?php if ($_SESSION['level']>=6 and empty($prodverif['id'])){?><a class="btn btn-danger" onclick="return alerteS();" href="?deleteret=<?=$product->numedit;?>">Annuler</a><?php };?></td>
                        
                      </tr><?php 
                  }?>

                  </tbody>

                  <!-- <tfoot>
                    <tr>
                      <th colspan="7">Totaux</th>
                      <th class="text-end"><?= number_format($montantgnf,0,',',' ');?></th>
                      <th class="text-end"><?= number_format($montantus,0,',',' ');?></th>
                      <th class="text-end"><?= number_format($montanteu,0,',',' ');?></th>
                      <th class="text-end"><?= number_format($montantcfa,0,',',' ');?></th>
                    </tr>
                  </tfoot> -->

                </table>
              </div><?php 
            }

            

          }else{

            echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";

          }

        }else{

        }?>
      </div>
    </div>
  </div>
    
</body>

</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <script>
      $(document).ready(function(){
          $('#search-user').keyup(function(){
              $('#result-search').html("");

              var utilisateur = $(this).val();

              if (utilisateur!='') {
                  $.ajax({
                      type: 'GET',
                      url: 'recherche_utilisateur.php?editionfacture',
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

<script>
    $(document).ready(function(){
        $('#numberconvert').keyup(function(){
            $('#convertnumber').html("");

            var utilisateur = $(this).val();

            if (utilisateur!='') {
                $.ajax({
                    type: 'GET',
                    url: 'convertnumber.php?convertvers',
                    data: 'user=' + encodeURIComponent(utilisateur),
                    success: function(data){
                        if(data != ""){
                          $('#convertnumber').append(data);
                        }else{
                          document.getElementById('convertnumber').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
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
