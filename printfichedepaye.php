<?php
require_once "lib/html2pdf.php";

ob_start(); ?>

<?php require '_header.php';?>

<style type="text/css">

body{
  margin: 0px;
  width: 100%;
  height:100%;
  padding:0px;
}
  .entete{
    width: 100%;
    margin-bottom: 20px;

  }

  .pied{
    text-align: center;
    margin-top: 40px;
    margin-right: 80px;
    font-size: 20px;
    font-style: italic;
  }

  .symbole{
    margin: 30px;
    margin-top: 500px;
    margin-left: 0px;
    margin-right: 100px;

  }

  .etat{
    margin-top: 20px;
    margin-left: 10px;
    font-weight: bold;
    font-size: 12px;
    color: #717375;
  }

  table.tablistebul{
    width: 100%;
    margin:auto;
    margin-top: 20px;
    color: #717375;
    border-collapse: collapse;
  }

  .tablistebul th {
    line-height: 7mm;
    border: 1px solid black;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    margin-top: 30px;
    padding-right: 5px;
    padding-left: 5px;
  }
  .tablistebul td {
    border: 1px solid black;
    line-height: 7mm;
    text-align: left;
    padding-right: 5px;
    padding-left: 5px;
    font-size: 16px;
  }

  table.tablistel{
    width: 99%;
    margin-left: 2px;
    margin-top: 30px;
    color: #717375;
    border-collapse: collapse;
  }

  .tablistel th {
    line-height: 7mm;
    border: 1px solid black;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    margin-top: 30px;
    padding-right: 2px;
    padding-left: 2px;
  }

  .tablistel td {
    border: 1px solid black;
    line-height: 7mm;
    text-align: left;
    padding-right: 5px;
    padding-left: 5px;
    font-size: 10.88px;
  }


  table.border {
    color: #717375;
    font-family: helvetica;
    line-height: 10mm;
    border-collapse: collapse;
  }


  .border th {
    border: 1px solid black;
    padding: 2px;
    font-size: 15px;
    background: white;
    text-align: center; }
  .border td {
    padding: 2px;
    border: 1px solid black;    
    font-size: 16px;
    background: white;
    text-align: center;
  }

  label {
    float: right;
    font-size: 14px;
    font-weight: bold;
    width: 200px;
  }

  ol{
    list-style: none;
  }
</style><?php

$month = array(
  10  => 'Octobre',
  11  => 'Novembre',
  12  => 'Décembre',
  1   => 'Janvier',
  2   => 'Février',
  3   => 'Mars',
  4   => 'Avril',
  5   => 'Mai',
  6   => 'Juin',
  7   => 'Juillet',
  8   => 'Août',
  9   => 'Septembre'
  
);

$mois=$_GET['mois'];

if (isset($_GET['idc'])) {

	$numdec = 'retd'.$_GET['numdec'];
	$prodpaye=$DB->querys("SELECT sum(montant) as montant, sum(montantav) as montantav, sum(montantpr) as montantpr, sum(montantcot) as montantcot, client, numdec, payement, date_payement, devisedep FROM decdepense where numdec='{$numdec}' ");      
  $matricule=$prodpaye['client'];
	$numdec=$prodpaye['numdec']; 
	$type=$prodpaye['payement'];
	$datep=(new dateTime($prodpaye['date_payement']))->format("d/m/Y");?>

	<page backtop="5mm" backleft="10mm" backright="10mm" backbottom="5mm"><?php



    require 'headerticket.php';?></div><?php

    $montantpaye=$prodpaye['montant'];
    $accompte=$prodpaye['montantav'];
    $prime=$prodpaye['montantpr'];
    $cotisation=$prodpaye['montantcot'];?>

    <table style="margin: auto; margin-top: 30px; border-bottom: 0px;" class="border" >

    	<thead>

        <tr>
          <th colspan="4">BULLETIN DE PAIE <?=$panier->obtenirLibelleMois($mois);?> </th>
        </tr>

        <tr>
          <th><?="Matricule:  " .$matricule; ?></th>
          <th colspan="3" style="font-size: 18px;"><?=$panier->nomPersonnel($matricule); ?></th>
        </tr>

        <tr>
          <th><?php echo "Paiement N°: " .$numdec; ?></th>
          <th><?php echo "Type de Paiement:  " .$type; ?></th>
          <th colspan="2"><?php echo "Date de paiement:  " .$datep; ?></th>
        </tr>

        <tr>
          <th colspan="4"></th>
        </tr>

        <tr>
          <th style="width: 10%;" height="30">Heure(s)</th>
          <th style="width: 48%; text-align: center;">Libellé</th>
          <th style="width: 14%;">Montant</th>
        </tr>

			</thead>

    	<tbody><?php
        $total=0;

        $salaire=$montantpaye-$prime+$accompte+$cotisation;?>

        <tr>

          <td style="width: 10%;border:2px ; border-bottom: 0px;">--</td>

          <td style="width: 48%;border:2px;text-align:left; border-bottom: 0px;"><?=ucfirst('Salaire Brut'.' '); ?></td>

          <td style="width: 14%;border:2px; text-align:right; border-bottom: 0px;"><?=number_format($salaire,0,',',' '); ?></td>
        </tr>

        <tr>

          <td style="width: 10%;border:2px ; border-bottom: 0px;">--</td>

          <td style="width: 48%;border:2px;text-align:left; border-bottom: 0px;"><?=ucfirst('Prime'.' '); ?></td>

          <td style="width: 14%;border:2px; text-align:right; border-bottom: 0px;"><?=number_format($prime,0,',',' '); ?></td>
        </tr>

        <tr>

          <td style="width: 10%;border:2px ; border-bottom: 0px;">--</td>

          <td style="width: 48%;border:2px;text-align:left; border-bottom: 0px;"><?=ucfirst('Avance sur Salaire'.' '); ?></td>

          <td style="width: 14%;border:2px; text-align:right; border-bottom: 0px;"><?=number_format($accompte,0,',',' '); ?></td>
        </tr>

        <tr>

          <td style="width: 10%;border:2px ; border-bottom: 0px;">--</td>

          <td style="width: 48%;border:2px;text-align:left; border-bottom: 0px;"><?=ucfirst('Cotisation Sociale'.' '); ?></td>

          <td style="width: 14%;border:2px; text-align:right; border-bottom: 0px;"><?=number_format($cotisation,0,',',' '); ?></td>
        </tr>

        <?php

        $total= $prodpaye['montant'];
        $Remise=0;

        $ttc = $total-$Remise;

        $tot_Rest = 0; ?>

      	<tr>
        	<td style="border:2px; padding-top: 350px;" class="space"></td>
        	<td style="border:2px; padding-top: 50px;" class="space"></td>
        	<td style="border:2px; padding-top: 50px;" class="space"></td>
      	</tr>

				        
				        


        <tr>
          <td style="text-align: center; margin-bottom: 5px; font-size:20px;" colspan="3">Salaire Net Payé: <?php echo number_format($total,0,',',' '); ?> <?=strtoupper($prodpaye['devisedep']);?></td>
        </tr>

  	 </tbody>

		</table>

  </page><?php	
  
	$content = ob_get_clean();
	try {
	    $pdf = new HTML2PDF("p","A4","fr", true, "UTF-8" , 0);
	    $pdf->pdf->SetAuthor('Amadou');
	    $pdf->pdf->SetTitle(date("d/m/y"));
	    $pdf->pdf->SetSubject('Création d\'un Portfolio');
	    $pdf->pdf->SetKeywords('HTML2PDF, Synthese, PHP');
	    //$pdf->pdf->IncludeJS("print(true);");
	    $pdf->writeHTML($content);
	    $pdf->Output('fiche_paye_'.date("d/m/y").date("H:i:s").'.pdf');
	    // $pdf->Output('Devis.pdf', 'D');    
  	} catch (HTML2PDF_exception $e) {
    	die($e);
  	}//header("Refresh: 10; URL=index.php");
}
	?>