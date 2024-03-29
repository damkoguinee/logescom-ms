<?php require 'header.php';

if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];

  require 'navversement.php'; 
  

  if ($products['level']>=3) {

    if (isset($_GET['deletevers'])) {

      $numero=$_GET['deletevers'];
      $DB->delete('DELETE FROM versement WHERE numcmd = ?', array($numero));

      $DB->delete('DELETE FROM bulletin WHERE numero = ?', array($numero));

      $DB->delete('DELETE FROM banque WHERE numero = ?', array($numero));

      $DB->delete('DELETE FROM modep WHERE numpaiep = ?', array($numero));?>

      <div class="alerteV">LE VERSEMENT A BIEN ETE SUPPRIME</div><?php
    }

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


    if (isset($_POST["valid"])) {

      if (empty($_POST["client"]) or empty($_POST["montant"]) or empty($_POST['montantr'])) {?>

        <div class="alertes">Les Champs sont vides</div><?php

      }elseif(empty($_POST['numcheque'])){
        header("Location: chequeespces.php.php?ajout");

        $alertescheque='entrer le numéro du chèque';

        $_SESSION['alertescheque']=$alertescheque;

      }else{

        unset($_SESSION['alertescheque']);

        $montant=$panier->h($_POST['montant']);
        $montantr=$panier->h($_POST['montantr']);
        $devise='gnf';
        $client=$panier->h($_POST['client']);
        $motif='depot cheque especes';
        $motifr='paiement cheque especes';
        $payement='chèque';
        $payementr='espèces';
        $compte=$panier->h($_POST['compte']);
        $compter=$panier->h($_POST['compter']);
        $taux=1;
        $convert=$montant*$taux;
        $numcheque=$panier->h($_POST['numcheque']);
        $banquecheque=$panier->h($_POST['banquecheque']);

        if ($panier->lieuVenteCaisse($compte)[1]=='banque') {
          $lieuventeret=$_SESSION['lieuvente'];
        }else{
          $lieuventeret=$panier->lieuVenteCaisse($compte)[0];
        }

        

        $maximum = $DB->querys('SELECT max(id) AS max_id FROM versement ');

        $max=$maximum['max_id']+1;

        $numdec = $DB->querys('SELECT max(id) AS id FROM decaissement ');
        $numdec=$numdec['id']+1;

        $dateop=$_POST['datedep'];

        $prodclient=$DB->querys("SELECT id, typeclient from client where id='{$_POST['client']}'");

        if (empty($dateop)) {

          $DB->insert('INSERT INTO versement (numcmd, nom_client, montant, devisevers, numcheque, banquecheque, motif, type_versement, comptedep, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array('dep'.$max, $client, $montant, $devise, $numcheque, $banquecheque, $motif, $payement, $compte, $lieuventeret));

          $DB->insert('INSERT INTO decaissement (numdec, montant, devisedec, payement, coment, client, cprelever, lieuvente, date_payement) VALUES(?, ?, ?, ?, ?, ?, ?, ?,  now())',array('ret'.$numdec, $montantr, $devise, $payementr, $motifr, $client, $compter, $lieuventeret));

          if ($prodclient['typeclient']!='Banque') {

            $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, devise, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array($client, $montant, $motif, 'dep'.$max, $devise, $compte, $lieuventeret));

            $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, devise, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array($client, -$montantr, 'Retrait ('.$motifr.')', 'ret'.$numdec, $devise, $compter, $lieuventeret));
          }

                      

          $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, devise, typep, lieuvente, numeropaie, banqcheque, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array($compte, $montant, "Depot(".$motif.')', 'dep'.$max, $devise, $payement, $lieuventeret, $numcheque, $banquecheque,));


          $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, devise, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, now())', array($compter, -$montantr, "Retrait (".$motifr.')', 'ret'.$numdec, $devise, $lieuventeret));

          if ($prodclient['typeclient']=='Banque') {

            $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, devise, typep, lieuvente, numeropaie, banqcheque, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array($prodclient['id'], -$montant, "Retrait(".$_POST['motif'].')', 'dep'.$max, $devise, $payement, $lieuventeret, $numcheque, $banquecheque));

            $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, devise, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, now())', array($prodclient['id'], $montantr, "Retrait (".$motifr.')', 'ret'.$numdec, $devise, $lieuventeret));
          }

          $DB->insert('INSERT INTO modep (numpaiep, client, montant, modep, taux, caisse, numerocheque, banquecheque) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array('dep'.$max, $client, $montant, $payement, $taux, $compte, $numcheque, $banquecheque));

        }else{

          $DB->insert('INSERT INTO versement (numcmd, nom_client, montant, devisevers, numcheque, banquecheque, motif, type_versement, comptedep, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array('dep'.$max, $client, $montant, $devise, $numcheque, $banquecheque, $motif, $payement, $compte, $lieuventeret, $dateop));

          $DB->insert('INSERT INTO decaissement (numdec, montant, devisedec, payement, coment, client, cprelever, lieuvente, date_payement) VALUES(?, ?, ?, ?, ?, ?, ?, ?,  ?)',array('ret'.$numdec, $montantr, $devise, $payementr, $motifr, $client, $compter, $lieuventeret, $dateop));

          if ($prodclient['typeclient']!='Banque') {

            $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, devise, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array($client, $montant, $motif, 'dep'.$max, $devise, $compte, $lieuventeret, $dateop));

            $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, devise, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array($client, -$montantr, 'Retrait ('.$motifr.')', 'ret'.$numdec, $devise, $compter, $lieuventeret, $dateop));
          }

          

          $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, devise, typep, lieuvente, numeropaie, banqcheque, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array($compte, $montant, "Depot(".$motif.')', 'dep'.$max, $devise, $payement, $lieuventeret, $numcheque, $banquecheque, $dateop));

          $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, devise, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?)', array($compter, -$montantr, "Retrait (".$motifr.')', 'ret'.$numdec, $devise, $lieuventeret, $dateop));

          if ($prodclient['typeclient']=='Banque') {

            $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, devise, typep, lieuvente, numeropaie, banqcheque, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array($prodclient['id'], -$montant, "Retrait(".$_POST['motif'].')', 'dep'.$max, $devise, $payement, $lieuventeret, $numcheque, $banquecheque, $dateop));

            $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, devise, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?)', array($prodclient['id'], $montantr, "Retrait (".$motifr.')', 'ret'.$numdec, $devise, $lieuventeret, $dateop));
          }

          $DB->insert('INSERT INTO modep (numpaiep, client, montant, modep, taux, caisse, numerocheque, banquecheque, datefact) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)', array('dep'.$max, $client, $montant, $payement, $taux, $compte, $numcheque, $banquecheque, $dateop));
        }

        if (isset($_POST["valid"])) {
                
          $_SESSION['reclient']=$client;
          $_SESSION['nameclient']=$client;

          //header("Location:printversement.php");
    
        }

      }

    }else{

      
    }

    if (isset($_GET['ajout']) or isset($_GET['searchclientvers'])) {

      if (isset($_GET['searchclientvers']) ) {

          $_SESSION['searchclientvers']=$_GET['searchclientvers'];
      }?>

      <form id="naissance" method="POST" action="chequeespeces.php" style="margin-top: 0px; width:90%; margin-top:5px;" >

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

            <div style="display: flex;">
              <div style="width: 50%;">

                <li><label>Montant Cheque*</label><input id="numberconvert" type="number"   name="montant" min="0" required="" style="font-size: 25px; width: 50%;"></li>
              </div>

              <li style="width:50%;"><label style="width:50%;"><div style="color:white; background-color: grey; font-size: 25px; color: orange; width:100%;" id="convertnumber"></div></li></label>
            </div>

            <li><label>N°Chèque</label><input type="text" name="numcheque"><div style="color: red;"><?php if (isset($_POST['numcheque']) ) {?><?=$_SESSION['alertescheque'];?><?php };?></div></li>

            <li><label>Banque Chèque</label>
              <select type="text" name="banquecheque" style="width: 25%;">
                <option></option>
                <option value="ecobank">Ecobank</option>
                <option value="bicigui">Bicigui</option>
                <option value="vistagui">Vistagui</option>
                <option value="bsic">Bsic</option>
                <option value="uba">UBA</option>
                <option value="banque islamique">Banque islamique</option>
                <option value="skye bank">Skye Banq</option>
                <option value="bci">BCI</option>
                <option value="fbn">FBN</option>
                <option value="societe generale">Société Générale</option>
                <option value="orabank">Orabank</option>
                <option value="vistabank">Vista Bank</option>
                <option value="asses">Asses</option>
                <option value="bpmg">BPMG</option>
                <option value="afriland">Afriland</option>
              </select>
            </li> 

            <li><label>Compte de dépôt*</label>
              <select  name="compte" required=""><?php
                  $type='Banque';

                foreach($panier->nomBanqueCaisseFiltre() as $product){?>

                  <option value="<?=$product->id;?>"><?=strtoupper($product->nomb);?></option><?php
                }

                foreach($panier->nomBanqueVire() as $product){?>

                  <option value="<?=$product->id;?>"><?=strtoupper($product->nomb);?></option><?php
                }?>
              </select>
            </li>

            <div style="display: flex;">
              <div style="width: 50%;">

                <li><label>Montant Remis*</label><input id="numberconvertl" type="number"   name="montantr" min="0" required="" style="font-size: 25px; width: 50%;"></li>
              </div>

              <li style="width:50%;"><label style="width:50%;"><div style="color:white; background-color: grey; font-size: 25px; color: orange; width:100%;" id="convertnumberl"></div></li></label>
            </div>

            <li><label>Compte de Retraît*</label>
              <select  name="compter" required=""><?php
                  $type='Banque';

                foreach($panier->nomBanqueCaisseFiltre() as $product){?>

                  <option value="<?=$product->id;?>"><?=strtoupper($product->nomb);?></option><?php
                }

                foreach($panier->nomBanqueVire() as $product){?>

                  <option value="<?=$product->id;?>"><?=strtoupper($product->nomb);?></option><?php
                }?>
              </select>
            </li>

            <li><label>Date de dépôt</label><input type="date" name="datedep"></li>
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

    if (!isset($_GET['ajout']) ) {?>  

      <table class="payement">

        <thead>
          <tr><th class="legende" colspan="12" height="30"><?="Liste des dépôts " .$datenormale ?> <?php 

            if ($user['statut'] != 'superviseur') {?> <a style="color:orange; font-size:30px;" href="chequeespeces.php?ajout">Enregistrer un Chèque/Espèces</a> <?php } ?> </th></tr>

          <tr>
            <form method="POST" action="chequeespeces.php" id="suitec" name="termc">

              <th colspan="2" ><?php

                if (isset($_POST['j1'])) {?>

                  <input style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()" value="<?=$_POST['j1'];?>"><?php

                }else{?>

                  <input style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()"><?php

                }

                if (isset($_POST['j2'])) {?>

                  <input style="width:150px;" type = "date" name = "j2" value="<?=$_POST['j2'];?>" onchange="this.form.submit()"><?php

                }else{?>

                  <input style="width:150px;" type = "date" name = "j2" onchange="this.form.submit()"><?php

                }?>
              </th>
            </form>

            <form method="POST" action="chequeespeces.php" id="suitec" name="termc">
              <th colspan="8"><?php 


                if (!empty($_SESSION['date1'])) {?>
              
                  <select style="width: 200px;" name="magasin" onchange="this.form.submit()"><?php

                    if (isset($_POST['magasin']) and $_POST['magasin']=='general') {?>

                      <option value="<?=$_POST['magasin'];?>">Général</option><?php
                      
                    }elseif (isset($_POST['magasin'])) {?>

                      <option value="<?=$_POST['magasin'];?>"><?=$panier->nomStock($_POST['magasin'])[0];?></option><?php
                      
                    }else{?>

                      <option value="<?=$_SESSION['lieuvente'];?>"><?=$panier->nomStock($_SESSION['lieuvente'])[0];?></option><?php

                    }

                    if ($_SESSION['level']>6) {

                      foreach($panier->listeStock() as $product){?>

                        <option value="<?=$product->id;?>"><?=strtoupper($product->nomstock);?></option><?php

                      }?>

                      <option value="general">Général</option><?php
                    }?>
                  </select><?php 
                }?>
              </th>
            </form>

            <form method="POST" action="chequeespeces.php">

              <th colspan="2">

                <input style="width:65%;" id="search-user" type="text" name="clientsearch" placeholder="rechercher un client" />
                <div style="color:white; background-color: grey; font-size: 16px;" id="result-search"></div>
              </th>
            </form>
            
          </tr>

          <tr>
            <th>N°</th>
            <th>Client</th>
            <th>Motif</th>
            <th>Date</th>
            <th>GNF</th>
            <th>$</th>
            <th>€</th>
            <th>CFA</th>
            <th>V. Banque</th>
            <th>Chèque</th>
            <th colspan="2">Actions</th>
          </tr>

        </thead>

        <tbody><?php
         
          $cumulmontant=0;
          if (isset($_POST['j1'])) {

            
            if ($_SESSION['level']>6) {
              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, devisevers, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE DATE_FORMAT(date_versement, \'%Y%m%d\')>= :date1 and DATE_FORMAT(date_versement, \'%Y%m%d\')<= :date2 order by(versement.id)', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2']));
            }else{
              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, devisevers, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE lieuvente=:lieu and DATE_FORMAT(date_versement, \'%Y%m%d\')>= :date1 and DATE_FORMAT(date_versement, \'%Y%m%d\')<= :date2 order by(versement.id)', array('lieu'=>$_SESSION['lieuvente'], 'date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2']));
            }

          }elseif (isset($_POST['magasin'])) {

            if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, devisevers, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE DATE_FORMAT(date_versement, \'%Y%m%d\')>= :date1 and DATE_FORMAT(date_versement, \'%Y%m%d\')<= :date2 order by(versement.id)', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2']));
            }else{
              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, devisevers, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE lieuvente=:lieu and DATE_FORMAT(date_versement, \'%Y%m%d\')>= :date1 and DATE_FORMAT(date_versement, \'%Y%m%d\')<= :date2 order by(versement.id)', array('lieu'=>$_POST['magasin'], 'date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2']));
            }                 

          }elseif (isset($_GET['searchversclient'])) {

            if ($_SESSION['level']>6) {
              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, devisevers, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE versement.nom_client = :client order by(versement.id) ', array('client' => $_GET['searchversclient']));
            }else{
              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, devisevers, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE lieuvente=:lieu and versement.nom_client = :client order by(versement.id)', array('lieu'=>$_SESSION['lieuvente'], 'client' => $_GET['searchversclient']));
            }

            

          }else{

            if ($_SESSION['level']>6) {
              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, devisevers, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE YEAR(date_versement) = :annee order by(versement.id) ', array( 'annee' => date('Y')));
            }else{
              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, devisevers, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE lieuvente=:lieu and YEAR(date_versement) = :annee order by(versement.id)', array('lieu'=>$_SESSION['lieuvente'], 'annee' => date('Y')));
            }

            
          }

      
        $montantgnf=0;
        $montanteu=0;
        $montantus=0;
        $montantcfa=0;
        $virement=0;
        $cheque=0;
        foreach ($products as $keyd=> $product ){?>

          <tr>
            <td style="text-align: center;"><?= $keyd+1; ?></td>
            <td><?= $product->nom_client; ?></td>
            <td><?= ucwords(strtolower($product->motif)); ?></td>
            <td style="text-align:center;"><?=(new DateTime($product->date_versement))->format("d/m/Y à H:i"); ?></td><?php

            if ($product->devisevers=='gnf' and $product->type_versement=='espèces') {

              $montantgnf+=$product->montant;?>

              <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td>

              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td><?php

            }elseif ($product->devisevers=='us') {
              $montantus+=$product->montant;?>

              <td></td>
              <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td><?php
            }elseif ($product->devisevers=='eu') {
              $montanteu+=$product->montant;?>

              <td></td>
              <td></td>
              <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td>
              <td></td>
              <td></td>
              <td></td><?php
            }elseif ($product->devisevers=='cfa') {
              $montantcfa+=$product->montant;?>

              <td></td>
              <td></td>
              <td></td>
              <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td>
              <td></td>
              <td></td><?php

            }elseif ($product->type_versement=='virement') {
              $virement+=$product->montant;?>

              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td>
              <td></td><?php
            }elseif ($product->type_versement=='chèque') {
              $cheque+=$product->montant;?>

              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td style="text-align: right; padding-right: 10px;"><?= number_format($product->montant,0,',',' '); ?></td><?php
            }?>

            <td style="text-align: center"><a href="printversement.php?numdec=<?=$product->id;?>&idc=<?=$product->idc;?>" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/pdf.jpg"></a></td>

            <td><?php if ($_SESSION['level']>6 and $user['statut'] != 'superviseur' ){?><a href="versement.php?deletevers=<?=$product->numcmd;?>"> <input style="width: 100%;height: 30px; font-size: 17px; background-color: red;color: white; cursor: pointer;"  type="submit" value="Supprimer" onclick="return alerteS();"></a><?php };?></td>
            
          </tr><?php 
        }?>

      </tbody>

      <tfoot>
        <tr>
          <th colspan="4">Totaux Versements</th>
          <th style="text-align: right; padding-right: 10px;"><?= number_format($montantgnf,0,',',' ');?></th>
          <th style="text-align: right; padding-right: 10px;"><?= number_format($montantus,0,',',' ');?></th>
          <th style="text-align: right; padding-right: 10px;"><?= number_format($montanteu,0,',',' ');?></th>
          <th style="text-align: right; padding-right: 10px;"><?= number_format($montantcfa,0,',',' ');?></th>
          <th style="text-align: right; padding-right: 10px;"><?= number_format($virement,0,',',' ');?></th>
          <th style="text-align: right; padding-right: 10px;"><?= number_format($cheque,0,',',' ');?></th>
        </tr>
      </tfoot>

    </table><?php
  }

      

    }else{

      echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";

    }

  }else{

  }?>
    
</body>

</html>

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
                      url: 'recherche_utilisateur.php?clientvers',
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
}else{?>

  <script>
      $(document).ready(function(){
          $('#search-user').keyup(function(){
              $('#result-search').html("");

              var utilisateur = $(this).val();

              if (utilisateur!='') {
                  $.ajax({
                      type: 'GET',
                      url: 'recherche_utilisateur.php?versclient',
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

} ?>

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

  <script>
    $(document).ready(function(){
        $('#numberconvertl').keyup(function(){
            $('#convertnumberl').html("");

            var utilisateur = $(this).val();

            if (utilisateur!='') {
                $.ajax({
                    type: 'GET',
                    url: 'convertnumber.php?convertvers',
                    data: 'user=' + encodeURIComponent(utilisateur),
                    success: function(data){
                        if(data != ""){
                          $('#convertnumberl').append(data);
                        }else{
                          document.getElementById('convertnumberl').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
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
