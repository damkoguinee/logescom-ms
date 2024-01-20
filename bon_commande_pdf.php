<?php
require_once "lib/html2pdf.php";

ob_start(); ?>

<?php require '_header.php';?>

<style type="text/css">

body{
  margin: auto;
  width: 100%;
  height:68%;
  padding:0px;}
  .ticket{
    margin:auto;
    width: 100%;
  }
  table {
    width: 100%;
    color: #717375;
    font-family: helvetica;
    line-height: 5mm;
    border-collapse: collapse;
  }
  
  .border th {
    border: 1px solid black;
    padding: 0px;
    font-weight: bold;
    font-size: 15px;
    color: black;
    background: white;
    text-align: center;
    height: 12px;
    padding: 10px;
 }
  .border td {
    border: 1px solid black;    
    font-size: 14px;
    color: black;
    background: white;
    text-align: right;
    height: 14px;
    padding: 5px 25px;

  }
  .footer{
    font-size: 14px;
    font-style: italic;
  }

</style>
<page backtop="5mm" backleft="10mm" backright="10mm" backbottom="10mm" footer="page;">
  <?php $adress = $DB->querys("SELECT * FROM adresse ");
  $fournisseur=$_GET['client'];
  $cmd=$_GET['id_bon'];
  ?>

  <table style="margin:auto; width: 100%;  text-align: center;color: black; background: white; line-height: 5mm;" >

    <tr>
      <th style="font-weight: bold; color:#185c8d; font-size: 18px; padding: 5px; padding-left: 0px;">
        <img src="css/img/logo.jpg" width="100" height="80"><?=$adress['nom_mag'];?>
      </th>
    </tr>
    <tr>
      <td style="font-size: 14px;"><?=$adress['type_mag']; ?></td>
    </tr>
    <tr>
      <td style="font-size: 14px; padding-bottom:30px;"><?=$adress['adresse']; ?></td>
    </tr>      
  </table>

  <table style="margin:0px; font-size: 14px;color: black; background: white;" >

    <tr>
      <td><strong>Bon de Commande N°: <?=$cmd ?></strong></td>
    </tr>
    <tr>
      <td style="text-align:left; font-size: 16px;">Fournisseur: <?=$panier->nomClientad($fournisseur)[0];?></td>
    </tr>
    <tr>
      <td style="text-align:left; font-size: 16px;">Téléphone: <?=$panier->nomClientad($fournisseur)[1];?></td>
    </tr>
    <tr>
      <td style="text-align:left; font-size: 16px;">Adresse: <?=$panier->nomClientad($fournisseur)[2];?></td>
    </tr>

  </table><?php 
  if (isset($_GET['prix'])) {?>
    <table class="border" style="margin: auto; margin-top:30px;">
        <thead>
        <tr>
            <th>N°</th>
            <th>Désignation</th>
            <th>P.Unitaire</th>
            <th>Qtite</th>
            <th>Prix Total</th>
        </tr>
        </thead>
        <tbody><?php 
        $montantTotal=0;
        $prod=$DB->query("SELECT *from bon_commande_produit inner join bon_commande on id_bon = bl inner join productslist on productslist.id=id_prod where id_bon='{$cmd}' and  id_client='{$fournisseur}' ");

        foreach ($prod as $key => $value) {
            $montantTotal+=$value->prix_achat*$value->quantite;?>
            <tr>
            <td><?=$key+1;?></td>
            <td style="text-align: left; width:200px;"><?=$value->designation;?></td>
            <td><?=number_format($value->prix_achat,0,',',' ');?></td>
            <td><?=$value->quantite;?></td>
            <td><?=number_format($value->prix_achat*$value->quantite,0,',',' ');?></td>
            </tr><?php 
        }?>

        <tr>
            <td colspan="5" style="padding: 30px;"></td>
        </tr>

        <tr>
            <th colspan="4">Montant Total</th>
            <th><?=number_format($montantTotal,0,',',' ');?></th>
        </tr>

        <tr>
            <th colspan="2" style="border:0px solid white; font-style:italic;">Le responsable</th>
        </tr>
        </tbody>
    </table><?php 
  }else{?>
    <table class="border" style="margin: auto; margin-top:30px;">
        <thead>
        <tr>
            <th>N°</th>
            <th>Désignation</th>
            <th>Qtite</th>
        </tr>
        </thead>
        <tbody><?php 
        $montantTotal=0;
        $prod=$DB->query("SELECT *from bon_commande_produit inner join bon_commande on id_bon = bl inner join productslist on productslist.id=id_prod where id_bon='{$cmd}' and  id_client='{$fournisseur}' ");

        foreach ($prod as $key => $value) {
            $montantTotal+=$value->prix_achat*$value->quantite;?>
            <tr>
            <td><?=$key+1;?></td>
            <td style="text-align: left; width:200px;"><?=$value->designation;?></td>
            <td><?=$value->quantite;?></td>
            </tr><?php 
        }?>

        <tr>
            <td colspan="3" style="padding: 30px;"></td>
        </tr>
        <tr>
            <th colspan="2" style="border:0px solid white; font-style:italic;">Le responsable</th>
        </tr>
        </tbody>
    </table><?php
  } ?>               
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
    $pdf->Output('bon_de_commande'.date("d/m/y").date("H:i:s").'.pdf');
    // $pdf->Output('Devis.pdf', 'D');    
  } catch (HTML2PDF_exception $e) {
    die($e);
  }
?>