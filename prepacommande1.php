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
    border-collapse: collapse;
  }
  
  .border th {
    height: 20px;
    border: 1px solid black;
    padding: 3px;
    font-weight: bold;
    font-size: 12px;
    color: black;
    background: white;
    text-align: right;
  }
  .border td {
    height: 20px;
    padding: 3px;
    border: 1px solid black;    
    font-size: 12px;
    color: black;
    background: white;
    text-align: center;
  }
  .footer{
    font-size: 18px;
    font-style: italic;
  }

</style>

<page backtop="10mm" backleft="10mm" backright="10mm" backbottom="10mm" footer="page;">

  <?php 


  
    $Num_cmd=$_SESSION['rechercher'];

    $array=$DB->querys("SELECT date_cmd, num_cmd, montantpaye,remise, reste, telephone FROM payement inner join client on client.id=num_client WHERE num_cmd= ?", array($Num_cmd));

    $payement = $DB->querys('SELECT num_cmd, montantpaye, remise, reste, etat, nom_client as client, typeclient, num_client, DATE_FORMAT(date_cmd, \'%d/%m/%Y \à %H:%i:%s\')AS DateTemps, vendeur FROM payement inner join client on client.id=num_client WHERE num_cmd= ?', array($Num_cmd));

    $lieuvente=$_SESSION['lieuvente'];

    $adress = $DB->querys("SELECT * FROM adresse where lieuvente='{$lieuvente}' ");?>

    <table style="margin:auto; width: 100%;  text-align: center;color: black; background: white; line-height: 5mm;" >

    <tr>
      <th style="font-weight: bold; font-size: 22px; padding: 5px"><?php 

      if ($adress['nom_mag']=='SOGUICOM SARLU') {?>
        <img src="css/img/logo.jpg" width="300" height="80"><?php

      }elseif ($adress['initial']=='um') {?>
          <img src="css/img/logo.jpg" width="550" height="80"><?php

      }elseif ($adress['initial']=='sog') {?>
          <img src="css/img/logo.jpg" width="650" height="80"><?php

      }else{?>
        <img src="css/img/logo.jpg" width="150" height="50"><?php echo $adress['nom_mag'];
      }?></th>
    </tr>

    <tr>
      <td style="font-size: 14px;"><?=$adress['type_mag']; ?></td>
    </tr>

    <tr>
      <td style="font-size: 14px;"><?=$adress['adresse']; ?></td>
    </tr>
  </table><?php

    $total=0;
    $products = $DB->query('SELECT commande.id as id, designation, quantity, qtiteliv FROM commande inner join productslist on productslist.id=id_produit WHERE num_cmd= ?', array($Num_cmd));
    
    foreach ($products as $product){?>

      <div style="margin-left: 500px; margin-top: 15px;">

          <div><?=$panier->adClient($_SESSION['reclient'])[0]; ?></div>
      </div>

      <table style="margin:0px; font-size: 18px;color: black; background: white;" >

        <tr><th style="color: red; font-size:14px;">Bon de Livraion N°: <?=$product->id;?></th></tr>

        <tr>
          <td><?php echo "N°Facture: " .$Num_cmd; ?></td>
        </tr> 

        <tr>
          <td><?php echo "Date de vente:  " .$payement['DateTemps']; ?></td>
        </tr>

        <tr>
          <td><?php echo "Vendeur:  " .$panier->nomPersonnel($payement['vendeur']); ?></td>  
        </tr>
      </table>


      <table style="margin-top: 5px; margin-left:0px; margin-bottom: 50px;" class="border">

        <thead>



          <tr>
            <th style="text-align: center;">Reste</th>
            <th style="text-align: center;">Désignation</th><?php 

            foreach ($panier->listeStock() as $valueS) {?>
              <th style="text-align: center; font-size: 15px;"><?=ucwords($valueS->nomstock);?></th><?php 
            }?>
          </tr>

        </thead>

        <tbody>

          <tr>

            <td><?= $product->quantity-$product->qtiteliv; ?></td>

            <td style="text-align:left, margin-left:5px; font-size: 12px;"><?= ucwords(strtolower($product->designation)); ?></td><?php 

            foreach ($panier->listeStock() as $valueS) {?>
              <td></td><?php 
            }?>
          </tr>
        </tbody>
      </table>
      <div style="text-align:center;" >Bon édité le <?=date("d/m/Y à H:i");?></div>

      <div  style="margin-top: 0px; margin-bottom: 120px; color: grey;">
        <label style="margin-left: 20px; font-style: italic;">Emetteur</label>

        <label style="margin-left: 250px;">Réception</label>

        <label style="margin-left: 150px;">Client</label>
      </div>

      <div>****************************************************************************************************************************</div><?php
    }?>
  </page><?php

  $content = ob_get_clean();
  try {
    $pdf = new HTML2PDF("p","A4","fr", true, "UTF-8" , 0);
    $pdf->pdf->SetAuthor('Amadou');
    $pdf->pdf->SetTitle(date("d/m/y"));
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