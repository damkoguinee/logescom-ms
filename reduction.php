<?php require 'header.php';

if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];

  require 'navversement.php'; 
  

  if ($products['level']>=3) {

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
      

      $DB->delete("DELETE from bulletin where id ='{$_GET['deleteret']}'");?>

      <div class="alerteV">Opération annulée avec succèe</div><?php 
    }


    if (isset($_POST["valid"])) {

      if (empty($_POST["client"]) or empty($_POST["montant"])) {?>

        <div class="alertes">Les Champs sont vides</div><?php

      }else{
        $montant=$panier->h($_POST['montant']);
        $devise='gnf';
        $client=$panier->h($_POST['client']);
        $motif=$panier->h($_POST['motif']);
        $taux=1;

        $lieuventeret=$_SESSION['lieuvente']; 
        $dateop=$_POST['datedep'];

        $prodclient=$DB->querys("SELECT id, typeclient from client where id='{$_POST['client']}'");

        if (empty($dateop)) {

          $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, devise, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array($client, $montant, $motif, 'reduction', 'gnf', 1, $lieuventeret));

        }else{          

          $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, devise, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array($client, $montant, $motif, 'reduction', 'gnf', 1, $lieuventeret, $dateop));
            
        }?>

        <div class="alerteV">Reduction effectuée avec succèe dans le compte selectionné!!!</div><?php 

      }

    }else{

      
    }

    if (isset($_GET['searchclientvers']) ) {

        $_SESSION['searchclientvers']=$_GET['searchclientvers'];
    }?>

    <form id="naissance" method="POST" action="reduction.php" style="margin-top: 0px; width:90%; margin-top:1px;" >

      <fieldset style="margin-top:-30px;">
        <ol>          
          <li><label>Client*</label>
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
          <li><label>Motif*</label><input type="text"   name="motif" value="reduction sur la facture ilc.." required=""></li>

          <div style="display: flex;">
            <div style="width: 50%;">

              <li><label>Montant*</label><input id="numberconvert" type="number"   name="montant" min="0" required="" style="font-size: 25px; width: 50%;"></li>
            </div>

            <li style="width:50%;"><label style="width:50%;"><div style="color:white; background-color: grey; font-size: 25px; color: orange; width:100%;" id="convertnumber"></div></li></label>
          </div>
          <li><label>Date de la reduction</label><input type="date" name="datedep"></li>
        </ol>
      </fieldset>

      <fieldset style="margin-top:-30px;"><?php
          
        if (empty($panier->totalsaisie()) AND $panier->licence()!="expiree") {?>

          <input id="form"  type="submit" name="valid" value="VALIDER" onclick="return alerteV();" style="margin-left: 20px; margin-top: -20px; width:150px; cursor: pointer;"><?php

        }else{?>

          <div class="alertes"> Journée cloturée ou la licence est expirée </div><?php

        }?>
      </fieldset> 
    </form>
    
    <table class="payement">

          <thead>
            <tr><th class="legende" colspan="12" height="30"><?="Liste des reductions " ?> </th></tr>

            <!-- <tr>
              <form method="POST" action="" id="suitec" name="termc">

                <th colspan="11" ><?php

                  if (isset($_POST['j1'])) {?>

                    <input style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()" value="<?=$_SESSION['date01'];?>"><?php

                  }else{?>

                    <input style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()"><?php

                  }

                  if (isset($_POST['j2']) ) {?>

                    <input style="width:150px;" type = "date" name = "j2" value="<?=$_SESSION['date02'];?>" onchange="this.form.submit()"><?php

                  }else{?>

                    <input style="width:150px;" type = "date" name = "j2" onchange="this.form.submit()"><?php

                  }?>
                </th>
              </form>                 
            </tr> -->

            <tr>
              <th>N°</th>
              <th>Date</th>
              <th>Bénéficiaire</th> 
              <th>Montant</th>
              <th>Motif</th>                 
              <th>Actions</th>
            </tr>

          </thead>

          <tbody><?php 
          $numero="reduction";


            $products= $DB->query("SELECT *FROM bulletin WHERE numero='{$numero}' and lieuvente='{$_SESSION['lieuvente']}' order by(id) desc ");
             

            $montantgnf=0;
            $montantgnfav=0;
            $montantgnfpr=0;
            $montantgnfcot=0;

            foreach ($products as $keyv=> $product ){

              $montantgnf+=$product->montant;?>

              <tr>
                <td style="text-align: center;"><?= $keyv+1; ?></td>
                <td style="text-align:center;"><?=(new DateTime($product->date_versement))->format("d/m/Y"); ?></td>                   

                <td><?=$panier->nomClient($product->nom_client);?></td>
                
                <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td>
                <td><?=$panier->nomClient($product->libelles);?></td>

                <td><?php if ($_SESSION['level']>=6){?><a href="?deleteret=<?=$product->id;?>"> <input style="width: 100%;height: 30px; font-size: 17px; background-color: red;color: white; cursor: pointer;"  type="submit" value="Annuler" onclick="return alerteS();"></a><?php };?></td>
                    
              </tr><?php 
            }?>

          </tbody>

          <tfoot>
            <tr>
              <th colspan="3">Totaux</th>
              <th style="text-align: right; padding-right: 10px;"><?= number_format($montantgnf,0,',',' ');?></th>
            </tr>
          </tfoot>

        </table><?php

      

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
                      url: 'recherche_utilisateur.php?reduction',
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
