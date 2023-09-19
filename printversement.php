<?php
require_once "lib/html2pdf.php";

ob_start(); ?>

<?php require '_header.php';?>

<style type="text/css">

body{
  margin: 0px;
  width: 100%;
  height:68%;
  padding:0px;
}
  table {
    margin:auto;
    width: 100%;
    color: #717375;
    font-family: helvetica;    
    line-height: 5mm;
  }
  
  .border th {
    border: 2px solid #CFD1D2;
    padding: 5px;
    font-size: 14px;
    color: black;
    font-weight: normal;
    background: white;
    text-align: center; }
  .border td {
    line-height: 5mm;
    border: 0px solid #CFD1D2;    
    font-size: 14px;
    color: black;
    background: white;
    text-align: center;}
  .footer{
    font-size: 18px;
    font-style: italic;
  }

</style>

<page backtop="5mm" backleft="10mm" backright="10mm" backbottom="10mm" footer="page;">
  
  <?php

  $Numv=$_GET['numdec'];

  $payement = $DB->querys('SELECT nom_client as client, numcmd, montant, taux, devisevers, comptedep, motif, lieuvente, type_versement, DATE_FORMAT(date_versement, \'%d/%m/%Y \à %H:%i:%s\')AS DateV FROM versement WHERE versement.id= ?', array($Numv));

  $_SESSION['reclient']=$_GET['idc'];

  $idc=$_GET['idc'];

  $lieuvente=$payement['lieuvente'];
  $client=$payement['client'];?>

<?php $adress = $DB->querys("SELECT * FROM adresse where lieuvente='{$lieuvente}' ");

$total = 0;
$total_tva = 0; ?>



<table style="margin:auto; width: 100%;  text-align: center;color: black; background: white; line-height: 5mm;" >

  <tr>
    <th style="font-weight: bold; font-size: 22px; padding: 5px; padding-left: 0px;"><?php 

    if ($adress['nom_mag']=='SOGUICOM SARLU') {?>
      <img src="css/img/logo.jpg" width="300" height="80"><?php

    }elseif ($adress['initial']=='um') {?>
        <img src="css/img/logo.jpg" width="550" height="80"><?php

    }elseif ($adress['initial']=='sog') {?>
        <img src="css/img/logo.jpg" width="650" height="80"><?php

    }elseif ($adress['initial']=='mam') {?>
        <img src="css/img/logo.jpg" width="180" height="90"><?php

    }elseif ($adress['initial']=='kmco') {?>
        <img src="css/img/logo.jpg" width="400" height="80"><?php

    }elseif ($adress['initial']=='osa') {?>
        <img src="css/img/logo.jpg" width="180" height="120"><?php

    }elseif ($adress['initial']=='asc') {?>

        <img src="css/img/logo.jpg" width="0" height="0"><?php echo $adress['nom_mag'];

    }elseif ($adress['initial']=='chel') {?>

        <img src="css/img/logo.jpg" width="180" height="100"><?php echo $adress['nom_mag'];

    }elseif ($adress['initial']=='kbt') {?>
        <img src="css/img/logo.jpg" width="250" height="100"><?php

    }elseif ($adress['initial']=='afb') {?>
      <img src="css/img/logo.jpg" width="400" height="80"><?php

    }elseif ($adress['initial']=='ibd') {?>
      <img src="css/img/logo.jpg" width="500" height="70"><?php

    }elseif ($adress['initial']=='oum') {?>
      <img src="css/img/logo.jpg" width="200" height="120"><?php

    }else{?>
      <img src="css/img/logo.jpg" width="0" height="0"><?php echo $adress['nom_mag'];
    }?></th>
  </tr>

  <tr>
    <td style="font-size: 14px;"><?=$adress['type_mag']; ?></td>
  </tr>

  <tr>
    <td style="font-size: 14px;"><?=$adress['adresse']; ?></td>
  </tr>
</table>

  <table class="border" style="margin-top:10px;">
    <thead>
      <tr>
        <th rowspan="3" style="text-align:left; font-weight: 800;">Reçu de Paiment<br><br>
          <span><?=$panier->adClient($client)[0]; ?></span><br>
          <span>Téléphone: <?=$panier->adClient($client)[1]; ?></span><br>
          <span>Adresse: <?=$panier->adClient($client)[2]; ?></span>
        </th>
        <th>Reçu N° <span><?=$payement['numcmd'];?></span></th>
        <th>Mode de Paie <span><?=$payement['type_versement'];?></span></th>
        <th>Date <span><?=$payement['DateV'];?></span></th>
      </tr>
      <tr>
        <th>Taux<br><span><?=number_format($payement['taux'],0,',',' ');?></span></th>
        <th>Montant <br><span><?=number_format($payement['montant'],0,',',' ');?> <?=strtoupper($payement['devisevers']);?></span></th>
        <th>Montant Total <br><span><?=number_format($payement['montant'],0,',',' ');?> <?=strtoupper($payement['devisevers']);?></span></th>
      </tr>
      <tr>
        <th colspan="2">Motif <br><span><?=ucfirst($payement['motif']);?></span></th>
        <th>Compte de depôt <br><span><?=ucfirst($panier->nomBanquefecth($payement["comptedep"])); ?></span></th>
      </tr>
      <tr>
        <th colspan="2" style="font-weight:bold;">Ancien Solde <?=strtoupper($payement['devisevers']);?> <br>
          <span>
            <?=number_format(($panier->soldeclientgen($client,$payement['devisevers'])-$payement['montant']),0,',',' ');?>
          </span>
        </th>
        <th colspan="2" style="font-weight:bold; color:<?=$panier->colorPaiement($panier->soldeclientgen($client,$payement['devisevers']));?>">Nouveau Solde <?=strtoupper($payement['devisevers']);?> <br><span><?=number_format(($panier->soldeclientgen($client,$payement['devisevers'])),0,',',' ');?></span></th>
      </tr>
      <tr>
        <th style="font-weight:bold; color:<?=$panier->colorPaiement($panier->soldeclientgen($client,'gnf'));?>">Compte GNF <br><span><?=number_format(($panier->soldeclientgen($client,'gnf')),0,',',' ');?></span></th>
        <th style="font-weight:bold; color:<?=$panier->colorPaiement($panier->soldeclientgen($client,'eu'));?>">Compte € <br><span><?=number_format(($panier->soldeclientgen($client,'eu')),0,',',' ');?></span></th>
        <th style="font-weight:bold; color:<?=$panier->colorPaiement($panier->soldeclientgen($client,'us'));?>">Compte $ <br><span><?=number_format(($panier->soldeclientgen($client,'us')),0,',',' ');?></span></th>
        <th style="font-weight:bold; color:<?=$panier->colorPaiement($panier->soldeclientgen($client,'cfa'));?>">Compte CFA <br><span><?=number_format(($panier->soldeclientgen($client,'cfa')),0,',',' ');?></span></th>
      </tr>
      <tr>
        <th colspan="4"><?php
          $adress = $DB->querys("SELECT * FROM adresse where lieuvente='{$lieuvente}' ");
          if ($panier->soldeclientgen($client,'gnf')<0) {?>

            <p>Madame/Monsieur, à la date du <?= date("d/m/Y");?>, vous nous devez <span style="font-weight:bold; color:<?=$panier->colorPaiement($panier->soldeclientgen($client,'gnf'));?>"><?=number_format(-($panier->soldeclientgen($client,'gnf')),0,',',' '); ?> GNF</span></p><?php

          }else{?>

            <p>Madame/Monsieur, à la date du <?=date("d/m/Y");?>, nous vous devons <span style="font-weight:bold; color:<?=$panier->colorPaiement($panier->soldeclientgen($client,'gnf'));?>"><?=number_format(-($panier->soldeclientgen($client,'gnf')),0,',',' '); ?> GNF</span></p><?php
          }?>

          <p>**************<?=$adress['nom_mag']. " vous souhaite une excellente journée**************"; ?></p>
        </th>
      </tr>
    </thead>
    <tbody><?php
      $pers1=$DB->querys('SELECT *from login where id=:type', array('type'=>$_SESSION['idpseudo']));?>
      <tr>
        <td colspan="2"><?=strtoupper($pers1['statut']);?></td>
        <td colspan="2">Le Client</td>
      </tr>

      <tr>
        <td colspan="2" style="padding-top: 70px;"><?=ucwords($pers1['nom']);?></td>
        <td colspan="2" style="padding-top: 70px;"><?=$panier->adClient($client)[0]; ?></td>
      </tr>
    </tbody>
  </table>
</page>

<?php
  $content = ob_get_clean();
  try {
    $pdf = new HTML2PDF("p","A4","fr", true, "UTF-8" , 0);
    $pdf->pdf->SetAuthor('Amadou');
    $pdf->pdf->SetTitle(date("d/m/y"));
    $pdf->pdf->SetSubject('Création d\'un Portfolio');
    $pdf->pdf->SetKeywords('HTML2PDF, Synthese, PHP');
    $pdf->pdf->IncludeJS("print(true);");
    $pdf->writeHTML($content);
    $pdf->Output('recu de paiement'.date("d/m/y").date("H:i:s").'.pdf');
    // $pdf->Output('Devis.pdf', 'D');    
  } catch (HTML2PDF_exception $e) {
    die($e);
  }
?>