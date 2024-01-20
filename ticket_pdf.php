<?php
require_once "lib/html2pdf.php";
ob_start(); ?>

<?php require '_header.php';?>

<style type="text/css">

body{
  margin: 0px;
  width: 100%;
  height:68%;
  padding:0px;}
  .ticket{
    margin:0px;
    width: 100%;
  }
  table {
    width: 100%;
    color: #717375;
    font-family: helvetica;
    line-height: 10mm;
    border-collapse: collapse;
  }
  
  .border th {
    border: 2px solid black;
    padding: 0px;
    font-weight: bold;
    font-size: 18px;
    color: black;
    background: white;
    text-align: right;
    height: 30px; }
  .border td {
    line-height: 15mm;
    border: 2px solid black;    
    font-size: 18px;
    color: black;
    background: white;
    text-align: center;
    height: 18px;
  }
  .footer{
    font-size: 18px;
    font-style: italic;
  }

</style>

<page backtop="10mm" backleft="10mm" backright="10mm" backbottom="10mm" footer="page;"><?php 

    $prodnum=$DB->querys("SELECT date_cmd, max(num_cmd), montantpaye, remise, reste FROM payement "); 

    $Num_cmd=$prodnum['max(num_cmd)'];    

    $payement=$DB->querys('SELECT num_cmd, montantpaye, remise, reste, etat, num_client, typeclient, client, mode_payement, DATE_FORMAT(date_cmd, \'%d/%m/%Y \à %H:%i:%s\')AS DateTemps, vendeur FROM payement WHERE num_cmd= ?', array($Num_cmd));

    $frais=$DB->querys('SELECT numcmd, montant, motif  FROM fraisup WHERE numcmd= ?', array($Num_cmd));

      if ($payement['typeclient']=='VIP') {
        $_SESSION['reclient']=$payement['num_client'];
        require 'headerticketclient.php';
      }else{
        $_SESSION['reclient']=$payement['client'];
        require 'headerticketclient0.php';
      }?>

      <table style="margin:0px; font-size: 18px;color: black; background: white;" >

        <tr>
          <td><strong><?php echo "Facture N°: " .$Num_cmd; ?></strong></td>
        </tr>      

        <tr>
          <td><?php echo "Payement:  " .$payement['mode_payement']; ?></td>
        </tr>

        <tr>
          <td><?php echo "Date:  " .$payement['DateTemps']; ?></td>
        </tr>

        <tr>
          <td><?php echo "Vendeur:  " .$payement['vendeur']; ?></td>  
        </tr>

      </table>

      <table style="margin-top: 30px; margin-left:0px;" class="border">

        <tbody>

          <tr>
            <th style="width: 10%; padding-right: 5px;">Qtité</th>
            <th style="width: 40%; text-align: left;">Désignation</th>
            <th style="width: 22%;">Prix Unitaire</th>
            <th style="width: 28%; padding-right: 10px;">Prix Total</th>
          </tr>

        </tbody>
      </table>

      <table style="margin-top: 1px; margin-left:0px;" class="border">

        <tbody><?php

          $total=0;

            $products=$DB->query('SELECT quantity, commande.prix_vente as prix_vente, designation FROM commande inner join products on products.id=commande.id_produit WHERE num_cmd= ?', array($Num_cmd));
          $totqtite=0;
          foreach ($products as $product){

            $totqtite+=$product->quantity;?>

            <tr>

              <td style="width: 10%;"><?= $product->quantity; ?></td>

                <td style="width: 40%;text-align:left"><?=strtolower($product->designation); ?></td>

                <td style="width: 22%; text-align:right"><?=number_format($product->prix_vente,0,',',' '); ?></td>

                <td style="width: 28%; text-align:right; padding-right: 10px;"><?= number_format($product->prix_vente*$product->quantity,0,',',' '); ?></td><?php

              $price=($product->prix_vente*$product->quantity);

              $total += $price;?>

            </tr><?php
          }

          if (!empty($frais['motif'])) {?>

            <tr>

              <td style="width: 10%;">1</td>

              <td style="width: 40%;text-align:left"><?=ucwords($frais['motif']); ?></td>

              <td style="width: 22%; text-align:right"><?=number_format($frais['montant'],0,',',' '); ?></td>

              <td style="width: 28%; text-align:right; padding-right: 10px;"><?= number_format($frais['montant'],0,',',' '); ?></td>
            </tr><?php
          }

          $total=$total+$frais['montant'];

          $montantverse=$payement['montantpaye'];

          $Remise=$payement['remise'];

          $ttc = $total-$Remise;

          $tot_Rest = $payement['reste'];?>
        
        <tr>

          <td colspan="4" style="border:0px; padding-top: 50px;" class="space"></td>
        </tr>

        <tr>
          <td colspan="2" rowspan="4" style="padding: 2px; text-align: left; font-size:18px;"><?=$totqtite;?></td>
        </tr>

        <tr>
          <td style="text-align: right;" class="no-border">Total </td>
          <td style="text-align:right; padding-right: 5px;"><?php echo number_format($total,0,',',' ') ?></td>
        </tr>

        <tr>
          <td style="text-align: right;" class="no-border">Remise</td>               
          <td style="text-align:right; padding-right: 5px;"><?php echo number_format($Remise,0,',',' ') ?></td>        
        </tr>

        <tr>
          <td style="text-align: right; margin-bottom: 5px" class="no-border">Total Net </td>
          <td style="text-align:right; padding-right: 5px;"><?php echo number_format($ttc,0,',',' ') ?></td>
        </tr>

      </tbody>

    </table>

    <table style="margin-top: 30px; margin-left:0px;" class="border">

    <thead>

      <tr>
        <th style="width: 0%; border-right: 0px; border-bottom: 0px;"></th>
        <th style="width: 0%; text-align: left; border-right: 0px; border-bottom: 0px;"></th>
        <th style="width: 10%; border-right: 0px; border-bottom: 0px;"></th>
        <th style="width: 90%; padding-right: 10px;border-bottom: 0px;"></th>
      </tr>

    </thead>

    <tbody><?php

      if ($tot_Rest<=0) {?>

        <tr style="margin-top: 10px; width: 100%">
          <td style="border-right: 0px;"></td>

          <td style="border-right: 0px;"></td>
          <td colspan="2" rowspan="4" style=" padding-right: 15px; text-align: right; font-size:22px;"><?="Montant payé: ".number_format($montantverse,0,',',' ');?><br/>
            <?php echo "Rendu client: ".number_format(($tot_Rest*(-1)),0,',',' '); ?>
          </td>

        </tr><?php

      }else{?>

        <tr style="margin-top: 10px; width: 100%">

          <td style="border-right: 0px;"></td>
          <td style="border-right: 0px;"></td>
          <td colspan="2" rowspan="4" style="padding-right: 15px; text-align: right; font-size:22px;"><?php echo"Montant versé: ".number_format($montantverse,0,',',' ');?><br/>
              <?php echo "Reste à payer: ".number_format($tot_Rest,0,',',' '); ?>
          </td>

        </tr><?php

      }?>

    </tbody>
  </table>
</div>

</page>
<?php
  $content = ob_get_clean();
  try {
    $pdf = new HTML2PDF("p","A4","fr", true, "UTF-8" , 0);
    $pdf->pdf->SetAuthor('Amadou');
    $pdf->pdf->SetTitle("facture");
    $pdf->pdf->SetSubject('Création d\'un Portfolio');
    $pdf->pdf->SetKeywords('HTML2PDF, Synthese, PHP');
    $pdf->pdf->IncludeJS("print(true);");
    $pdf->writeHTML($content);
    $pdf->Output('ticket'.date("d/m/y").date("H:i:s").'.pdf');
    // $pdf->Output('Devis.pdf', 'D');    
  } catch (HTML2PDF_exception $e) {
    die($e);
  }
//header("Refresh: 10; URL=index.php");
?>