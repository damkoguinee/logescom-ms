<?php require 'header.php';

if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];


  if ($_SESSION['level']>=3) {

    if (isset($_GET['deleteret'])) {

      $DB->delete("DELETE from decdepense where numdec='{$_GET['deleteret']}'");

      $DB->delete("DELETE from bulletin where numero='{$_GET['deleteret']}'");

      $DB->delete("DELETE from banque where numero='{$_GET['deleteret']}'");?>

      <div class="alerteV">Suppression reussi!!</div><?php 
    }

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

    if (isset($_POST['j2'])) {

      $datenormale='entre le '.$_SESSION['dates1'].' et le '.$_SESSION['dates2'];

    }else{

      $datenormale=(new DateTime($_SESSION['date']))->format('d/m/Y');
    }
    

    if (isset($_POST['categorie'])) {
      $_SESSION['categoriedep']=$_POST['categorie'];
    }

    if (isset($_POST['magasin'])) {
      $_SESSION['magasindep']=$_POST['magasin'];
    }

    require 'navdec.php'; 

    if (isset($_GET['ajoutdep']) or isset($_POST['categins']) or isset($_GET["categ"])) {?>

      <form id="naissance" method="POST" action="decpersonnel.php" style="margin-top: 0px; width:90%; margin-top:10px;" >

        <fieldset style="margin-top: -20px;">
            <ol><input type="hidden" name="categorie" value="4"/>

              <li><label>Destinataire*</label>                           

                <select type="text" name="client" style="margin-right: 30px;">
                  <option></option><?php
                  foreach($panier->personnel() as $product){?>
                    <option value="<?=$product->id;?>"><?=$product->nom;?></option><?php
                  }?>
                </select>

                  Période*<input type="date" name="periode" required=""></li>

                <div style="display: flex;">
                  <div style="width: 50%;">

                    <li><label>Salaire Net Payé*</label><input id="numberconvert" type="number"   name="montant" min="0" required="" style="font-size: 25px; width: 50%;"></li>
                  </div>

                  <li style="width:50%;"><label style="width:50%;"><div style="color:white; background-color: grey; font-size: 25px; color: orange; width:100%;" id="convertnumber"></div></li></label>
                </div>

                <div style="display: flex;">
                  <div style="width: 50%;">

                    <li><label>Avance sur Salaire</label><input id="avnumberconvert" type="number"   name="avmontant" value="0" min="0" style="font-size: 25px; width: 50%;"></li>
                  </div>

                  <li style="width:50%;"><label style="width:50%;"><div style="color:white; background-color: grey; font-size: 25px; color: orange; width:100%;" id="avconvertnumber"></div></li></label>
                </div>

                <div style="display: flex;">
                  <div style="width: 50%;">

                    <li><label>Prime</label><input id="prnumberconvert" type="number"   name="prmontant" min="0" value="0" style="font-size: 25px; width: 50%;"></li>
                  </div>

                  <li style="width:50%;"><label style="width:50%;"><div style="color:white; background-color: grey; font-size: 25px; color: orange; width:100%;" id="prconvertnumber"></div></li></label>
                </div>

                <div style="display: flex;">
                  <div style="width: 50%;">

                    <li><label>Cotisation Sociale</label><input id="cotnumberconvert" type="number"   name="cotmontant" value="0" min="0" style="font-size: 25px; width: 50%;"></li>
                  </div>

                  <li style="width:50%;"><label style="width:50%;"><div style="color:white; background-color: grey; font-size: 25px; color: orange; width:100%;" id="cotconvertnumber"></div></li></label>
                </div>

                <li><label>Compte à Prélever</label>

                  <select  name="compte" required="" style="margin-right: 30px;">
                      <option></option><?php
                      $type='Banque';

                      foreach($panier->nomBanque() as $product){?>

                          <option value="<?=$product->id;?>"><?=strtoupper($product->nomb);?></option><?php
                      }?>
                  </select>

                  Devise*
                  <select name="devise" required="" style="margin-right: 30px;"><?php 
                    foreach ($panier->monnaie as $valuem) {?>
                        <option value="<?=$valuem;?>"><?=strtoupper($valuem);?></option><?php 
                    }?>
                  </select>

                  Mode de Payement*</label>
                  <select name="mode_payement" required="" ><?php 
                    foreach ($panier->modep as $value) {?>
                      <option value="<?=$value;?>"><?=$value;?></option><?php 
                    }?>
                  </select>
                </li><input type="hidden" name="coment" value="paiement personnel" ></li>                      

                <li><label>Date Op</label><input type="date" name="datedep"></li>
              </ol>
                      
          </fieldset>

          <fieldset style="margin-top: -30px;"><?php
  
            if (empty($panier->totalsaisie()) AND $panier->licence()!="expiree") {?>

                <input id="form"  type="submit" name="valid" value="VALIDER" onclick="return alerteV();" style="margin-left: 20px; margin-top: -20px; width:150px; cursor: pointer;"><?php

            }else{?>

                <div class="alertes"> Journée cloturée ou la licence est expirée </div><?php

            }?>
          </fieldset> 
      </form><?php 
          
    }

    if (empty($panier->totalsaisie()) AND $panier->licence()!="expiree") {?>

      <input id="button"  type="submit" name="valid" value="VALIDER" onclick="return alerteV();"><?php

    }else{?>

      <div class="alertes"> CAISSE CLOTUREE OU LA LICENCE EST EXPIREE </div><?php

    }


    if (isset($_POST['valid'])){         

      if ($_POST['montant']<0){?>

          <div class="alertes">FORMAT INCORRECT</div><?php

      }elseif ($_POST['montant']>$panier->montantCompteBilEspeces($_POST['compte'], $_POST['devise'])) {?>

          <div class="alertes">Echec montant decaissé est > au montant disponible en caisse</div><?php

      }else{                         

        if (!empty($_POST['client']) and !empty($_POST['categorie']) and !empty($_POST['compte'])) {

          $numdec = $DB->querys('SELECT max(id) AS id FROM decdepense ');
          $numdec=$numdec['id']+1;

          $categorie=$_POST['categorie'];
          $personnel=$panier->h($_POST['client']);
          $periode=$panier->h($_POST['periode']);
          $montant=$panier->h($_POST['montant']);
          $montantav=$panier->h($_POST['avmontant']);
          $montantpr=$panier->h($_POST['prmontant']);
          $montantcot=$panier->h($_POST['cotmontant']);
          $devise=$panier->h($_POST['devise']);
          $motif=$panier->h($_POST['coment']);
          $payement=$_POST['mode_payement'];
          $compte=$panier->h($_POST['compte']);
          $dateop=$panier->h($_POST['datedep']);
          $lieuventeret=$panier->lieuVenteCaisse($compte)[0];                  

          if (empty($dateop)) {
              
            $DB->insert('INSERT INTO decdepense (numdec, client, periodep, categorie, montant, montantav, montantpr, montantcot, devisedep, payement, coment, cprelever, lieuvente, date_payement) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  now())',array('retd'.$numdec, $personnel, $periode, $categorie, $montant, $montantav, $montantpr, $montantcot, $devise, $payement, $motif, $compte, $lieuventeret));

            $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, devise, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, now())', array($compte, -$montant, "Retrait (".$motif.')', 'retd'.$numdec, $devise, $lieuventeret));
          }else{
            $DB->insert('INSERT INTO decdepense (numdec, client, periodep, categorie, montant, montantav, montantpr, montantcot, devisedep, payement, coment, cprelever, lieuvente, date_payement) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',array('retd'.$numdec, $personnel, $periode, $categorie, $montant, $montantav, $montantpr, $montantcot, $devise, $payement, $motif, $compte, $lieuventeret, $dateop));

            $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, devise, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?)', array($compte, -$montant, "Retrait (".$motif.')', 'retd'.$numdec, $devise, $lieuventeret, $dateop));

          }?>

          <div class="alerteV">Retrait enregistré avec succèe!!</div><?php

        } else{?>

          <div class="alertes">Saisissez tous les champs vides</div><?php

        }

      }

    }else{

    }


      if (!isset($_GET['ajoutdep'])) {?>

        <table class="payement">

          <thead>
            <tr><th class="legende" colspan="12" height="30"><?="Liste des salaires payés " .$datenormale ?> <?php 
                    if ($user['statut'] != 'superviseur') {?> <a href="decpersonnel.php?ajoutdep" style="color:orange; font-size: 20px;">Enregistrer un salaire</a> <?php } ?> </th></tr>

            <tr>
              <form method="POST" action="decpersonnel.php" id="suitec" name="termc">

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
            </tr>

            <tr>
              <th>N°</th>
              <th></th>
              <th>Mois</th>
              <th>Bénéficiaire</th>                  
              <th>Salaire Net</th>
              <th>Avnace sur Salaire</th>
              <th>Prime</th>
              <th>Cotisation Sociale</th>
              <th>Mode de Paie</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>

          </thead>

          <tbody><?php 
          $categorie=4;
          $annee = date("Y");

            if (isset($_POST['j1'])) {    

              $products= $DB->query("SELECT *FROM decdepense WHERE DATE_FORMAT(date_payement, \"%Y%m%d\")>='{$_SESSION['date1']}' and DATE_FORMAT(date_payement, \"%Y%m%d\")<='{$_SESSION['date2']}' and categorie='{$categorie}' order by(id) desc");

            }else{

              $products= $DB->query("SELECT *FROM decdepense WHERE DATE_FORMAT(date_payement, \"%Y\")='{$annee}' and categorie='{$categorie}' order by(id) desc");              
            }

            $montantgnf=0;
            $montantgnfav=0;
            $montantgnfpr=0;
            $montantgnfcot=0;

            foreach ($products as $keyv=> $product ){

              $montantgnf+=$product->montant;
              $montantgnfav+=$product->montantav;
              $montantgnfpr+=$product->montantpr;
              $montantgnfcot+=$product->montantcot;

              $moispaye=(new dateTime($product->periodep))->format("m");?>

              <tr>
                <td style="text-align: center;"><?= $keyv+1; ?></td>

                <td style="text-align: center"><a href="printfichedepaye.php?numdec=<?=$product->id;?>&idc=<?=$product->client;?>&mois=<?=$moispaye;?>" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/pdf.jpg"></a></td>

                <td style="text-align: center"><?=$panier->obtenirLibelleMois($moispaye);?></td>

                <td style="text-align: center"><?=$panier->nomPersonnel($product->client);?></td>

                <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td>

                <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montantav,0,',',' '); ?></td>

                <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montantpr,0,',',' '); ?></td>

                <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montantcot,0,',',' '); ?></td>

                <td><?=$product->payement; ?></td>

                <td style="text-align:center;"><?=(new DateTime($product->date_payement))->format("d/m/Y"); ?></td>                   
                <td><?php if ($_SESSION['level']>=6 and $user['statut'] != 'superviseur' ){?><a href="decpersonnel.php?deleteret=<?=$product->numdec;?>"> <input style="width: 100%;height: 30px; font-size: 17px; background-color: red;color: white; cursor: pointer;"  type="submit" value="Supprimer" onclick="return alerteS();"></a><?php };?></td>
                    
              </tr><?php 
            }?>

          </tbody>

          <tfoot>
            <tr>
              <th colspan="4">Totaux</th>
              <th style="text-align: right; padding-right: 10px;"><?= number_format($montantgnf,0,',',' ');?></th>
              <th style="text-align: right; padding-right: 10px;"><?= number_format($montantgnfav,0,',',' ');?></th>
              <th style="text-align: right; padding-right: 10px;"><?= number_format($montantgnfpr,0,',',' ');?></th>
              <th style="text-align: right; padding-right: 10px;"><?= number_format($montantgnfcot,0,',',' ');?></th>
            </tr>
          </tfoot>

        </table><?php
      }

  }else{

      echo "VOUS N'AVEZ PAS TOUTES LES AUTORISATIOS REQUISES";
  }

}else{


}?>   
</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>



<script>
  $(document).ready(function(){
      $('#numberconvert').keyup(function(){
          $('#convertnumber').html("");

          var utilisateur = $(this).val();

          if (utilisateur!='') {
              $.ajax({
                  type: 'GET',
                  url: 'convertnumber.php?convertdec',
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

<script>
  $(document).ready(function(){
      $('#avnumberconvert').keyup(function(){
          $('#avconvertnumber').html("");

          var utilisateur = $(this).val();

          if (utilisateur!='') {
              $.ajax({
                  type: 'GET',
                  url: 'convertnumber.php?convertdec',
                  data: 'user=' + encodeURIComponent(utilisateur),
                  success: function(data){
                      if(data != ""){
                        $('#avconvertnumber').append(data);
                      }else{
                        document.getElementById('avconvertnumber').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
                      }
                  }
              })
          }
    
      });
  });
</script>

<script>
  $(document).ready(function(){
      $('#prnumberconvert').keyup(function(){
          $('#prconvertnumber').html("");

          var utilisateur = $(this).val();

          if (utilisateur!='') {
              $.ajax({
                  type: 'GET',
                  url: 'convertnumber.php?convertdec',
                  data: 'user=' + encodeURIComponent(utilisateur),
                  success: function(data){
                      if(data != ""){
                        $('#prconvertnumber').append(data);
                      }else{
                        document.getElementById('prconvertnumber').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
                      }
                  }
              })
          }
    
      });
  });
</script>

<script>
  $(document).ready(function(){
      $('#cotnumberconvert').keyup(function(){
          $('#cotconvertnumber').html("");

          var utilisateur = $(this).val();

          if (utilisateur!='') {
              $.ajax({
                  type: 'GET',
                  url: 'convertnumber.php?convertdec',
                  data: 'user=' + encodeURIComponent(utilisateur),
                  success: function(data){
                      if(data != ""){
                        $('#cotconvertnumber').append(data);
                      }else{
                        document.getElementById('cotconvertnumber').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
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

</script>

