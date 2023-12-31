<?php require 'header.php';

if (isset($_SESSION['pseudo'])) {

  $bdd='editionfacture';   

  $DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `numedit` varchar(150),
    `id_client` int(10) DEFAULT NULL,
    `libelle` varchar(150),
    `bl` varchar(150),
    `nature` varchar(150),
    `montant` double DEFAULT NULL,
    `devise` varchar(10),
    `lieuvente` int(2) DEFAULT NULL,
    `dateop` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ");

  $pseudo=$_SESSION['pseudo'];

  require 'navdec.php';  

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

      $DB->delete("DELETE from editionfacture where numedit='{$_GET['deleteret']}'");

      $DB->delete("DELETE from bulletin where numero='{$_GET['deleteret']}'");?>

      <div class="alerteV">Suppression reussi!!</div><?php 
    }


    if (isset($_POST["valid"])) {

      if (empty($_POST["client"]) or empty($_POST["montant"])) {?>

        <div class="alertes">Les Champs sont vides</div><?php

      }else{
        $numdec = $DB->querys('SELECT max(id) AS id FROM editionfacture');
        $numdec=$numdec['id']+1;

        $montant=$panier->h($_POST['montant']);
        $bl=$panier->h($_POST['bl']);
        $nature=$panier->h($_POST['nature']);
        $devise=$panier->h($_POST['devise']);
        $client=$panier->h($_POST['client']);
        $motif=$panier->h($_POST['motif']);
        $taux=1;

        $lieuventeret=$_SESSION['lieuvente']; 
        $dateop=$_POST['datedep'];

        if(isset($_POST["env"])){
          require "uploadfacture.php";
        }

        if (empty($dateop)) {

          $DB->insert('INSERT INTO editionfacture (numedit, id_client, montant, bl, nature, libelle, devise, lieuvente, dateop) VALUES(?, ?, ?, ?, ?, ?, ?, ?, now())', array('edit'.$numdec, $client, $montant, $bl, $nature, $motif, $devise, $lieuventeret));

          $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, devise, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array($client, $montant, $motif, 'edit'.$numdec, $devise, 1, $lieuventeret));

        }else{ 

          $DB->insert('INSERT INTO editionfacture (numedit, id_client, montant, bl, nature, libelle, devise, lieuvente, dateop) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)', array('edit'.$numdec, $client, $montant, $bl, $nature, $motif, $devise, $lieuventeret, $dateop));

          $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, devise, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array($client, $montant, $motif, 'edit'.$numdec, $devise, 1, $lieuventeret, $dateop));
            
        }
        unset($_POST);
        unset($_GET);
        unset($_SESSION['searchclientvers']);
        ?>

        <div class="alerteV">Facture enregistrée avec succèe dans le compte selectionné!!!</div><?php 

      }

    }else{

      
    }

    if (isset($_GET['searchclientvers']) ) {

        $_SESSION['searchclientvers']=$_GET['searchclientvers'];
    }

    if (isset($_GET['ajout']) or isset($_GET['searchclientvers'])) {?>

      <form id="naissance" method="POST" action="editionfacture.php" enctype="multipart/form-data" style="margin-top: 0px; width:90%; margin-top:1px;" >

        <fieldset style="margin-top:-30px;">
          <ol>          
            <li><label>Collaborateur*</label>
              <select type="text" name="client"><?php 

                if (!empty($_SESSION['searchclientvers'])) {?>

                    <option value="<?=$_SESSION['searchclientvers'];?>"><?=$panier->nomClient($_SESSION['searchclientvers']);?></option><?php
                }else{?>
                    <option></option><?php 
                }

                foreach($panier->client() as $product){?>
                  <option value="<?=$product->id;?>"><?=$product->nom_client;?></option><?php
                }?>
              </select>

              <input style="width:400px;" id="search-user" type="text" name="clients" placeholder="rechercher un collaborateur" />

              <div style="color:white; background-color: black; font-size: 16px; margin-left: 300px;" id="result-search"></div>
            </li>
            <li><label>N° BL/Numero*</label><input type="text"   name="bl" required=""></li>

            <li><label>Nature*</label><input type="text"   name="nature" required="" placeholder="par exemple oignon jaune"></li>

            <li><label>Libellé de la Facture*</label><input type="text"   name="motif" required=""></li>

            <div style="display: flex;">
              <div style="width: 50%;">

                <li><label>Montant*</label><input id="numberconvert" type="number"   name="montant" min="0" required="" style="font-size: 25px; width: 50%;"></li>
              </div>

              <li style="width:50%;"><label style="width:50%;"><div style="color:white; background-color: grey; font-size: 25px; color: orange; width:100%;" id="convertnumber"></div></li></label>
            </div>

            <li><label>Devise*</label>
              <select name="devise" required="" ><?php 
                foreach ($panier->monnaie as $valuem) {?>
                    <option value="<?=$valuem;?>"><?=strtoupper($valuem);?></option><?php 
                }?>
              </select>
            </li>

            <li><label>Joindre la facture</label>
              <input type="file" name="just[]"multiple id="photo" />
              <input type="hidden" value="b" name="env"/>
            </li>
            <li><label>Date de la Facture</label><input type="date" name="datedep"></li>
          </ol>
        </fieldset>

        <fieldset style="margin-top:-30px;"><?php
            
          if (empty($panier->totalsaisie()) AND $panier->licence()!="expiree") {?>

            <input id="form"  type="submit" name="valid" value="VALIDER" onclick="return alerteV();" style="margin-left: 20px; margin-top: -20px; width:150px; cursor: pointer;"><?php

          }else{?>

            <div class="alertes"> Journée cloturée ou la licence est expirée </div><?php

          }?>
        </fieldset> 
      </form> <?php
    }


    if (!isset($_GET['ajout'])) {?>

      <div style="overflow: auto">

        <table class="payement">

          <thead>
            <tr><th class="legende" colspan="13" height="30"><?="Liste des editions factures ";?> <a href="editionfacture.php?ajout" style="color:orange; font-size: 25px;">Editer une facture</a></th></tr>

            <tr>
              <th>N°</th>
              <th>Facture</th>
              <th>Date</th>
              <th>N°BL</th>
              <th>libelle</th> 
              <th>Nature</th>
              <th>Collaborateur</th>                             
              <th>GNF</th>
              <th>$</th>
              <th>€</th>
              <th>CFA</th>
              <th></th>
            </tr>

          </thead>

          <tbody><?php 

            if ($_SESSION['level']>6) {
              $products= $DB->query("SELECT *FROM editionfacture  order by(id) desc");
            }else{
              $products= $DB->query("SELECT *FROM editionfacture  WHERE lieuvente='{$_SESSION['lieuvente']}' ");
            }   

            $montantgnf=0;
            $montanteu=0;
            $montantus=0;
            $montantcfa=0;
            $virement=0;
            $cheque=0;
            foreach ($products as $keyv=> $product ){?>

              <tr>
                <td style="text-align: center;"><?= $keyv+1; ?></td>

                <td style="text-align: center"><?php
                  $num='fact'.$product->id;
                  $nom_dossier="editfacture/".'fact'.$product->id."/";
                  if (file_exists($nom_dossier)) {

                      $dossier=opendir($nom_dossier);
                      while ($fichier=readdir($dossier)) {

                          if ($fichier!='.' && $fichier!='..') {?>

                              <a href="editfacture/<?='fact'.$product->id;?>/<?=$fichier;?>" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/pdf.jpg"></a><?php
                          }
                      }closedir($dossier);
                  }?>
                </td>
                <td style="text-align:center;"><?=(new DateTime($product->dateop))->format("d/m/Y"); ?></td>
                <td><?= strtoupper($product->bl); ?></td>
                <td><?=$product->libelle; ?></td>
                <td><?=$product->nature; ?></td>
                <td><?=strtoupper($panier->nomClient($product->id_client)); ?></td><?php

                if ($product->devise=='gnf') {

                  $montantgnf+=$product->montant;?>

                  <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td>

                  <td></td>
                  <td></td>
                  <td></td><?php

                  }elseif ($product->devise=='us') {
                    $montantus+=$product->montant;?>

                    <td></td>
                    <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td>
                    <td></td>
                    <td></td><?php
                  }elseif ($product->devise=='eu') {
                    $montanteu+=$product->montant;?>

                    <td></td>
                    <td></td>
                    <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td>
                    <td></td><?php
                  }elseif ($product->devise=='cfa') {
                    $montantcfa+=$product->montant;?>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td><?php

                  }?>

                  <td><?php if ($_SESSION['level']>=6){?><a href="editionfacture.php?deleteret=<?=$product->numedit;?>"> <input style="width: 100%;height: 30px; font-size: 17px; background-color: red;color: white; cursor: pointer;"  type="submit" value="Supprimer" onclick="return alerteS();"></a><?php };?></td>
                  
                </tr><?php 
            }?>

            </tbody>

            <tfoot>
              <tr>
                <th colspan="7">Totaux</th>
                <th style="text-align: right; padding-right: 10px;"><?= number_format($montantgnf,0,',',' ');?></th>
                <th style="text-align: right; padding-right: 10px;"><?= number_format($montantus,0,',',' ');?></th>
                <th style="text-align: right; padding-right: 10px;"><?= number_format($montanteu,0,',',' ');?></th>
                <th style="text-align: right; padding-right: 10px;"><?= number_format($montantcfa,0,',',' ');?></th>
              </tr>
            </tfoot>

          </table>
        </div><?php 
      }

      

    }else{

      echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";

    }

  }else{

  }?>
    
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
