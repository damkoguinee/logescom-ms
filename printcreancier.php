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
    margin: auto;
  }
  
  .border th {
    border: 1px solid black;
    padding: 5px;
    font-weight: bold;
    font-size: 16px;
    color: black;
    background: white;
    text-align: center;
    height: 12px; }
  .border td {
    border: 1px solid black;    
    font-size: 14px;
    color: black;
    background: white;
    text-align: left;
    height: 14px;
  }
  .footer{
    font-size: 14px;
    font-style: italic;
  }

</style>

<page backtop="5mm" backleft="5mm" backright="10mm" backbottom="5mm" footer="page;"><?php
    require 'headerticket.php';?></div>

    <table class="border">
        <thead>
            <tr>
                <th colspan="5" scope="col" class="text-center bg-danger text-white"><label>Liste des créanciers à relancer / Aucune Opération depuis 30 jours</label></th>
            </tr>
            <tr>
                <th scope="col" class="text-center">N°</th>
                <th scope="col" class="text-center">Prénom & Nom</th>
                <th scope="col" class="text-center">Téléphone</th>
                <th scope="col" class="text-center">Solde</th>
                <th scope="col" class="text-center">Dernière Op</th>
            </tr>
        </thead>
        <tbody><?php
            $soldeCumul=0;
            $type1='client';
            $type2='clientf';
            if ($_SESSION['level']>6) {
                $prodclient = $DB->query("SELECT *FROM client where (typeclient='{$type1}' or typeclient='{$type2}') order by(nom_client) ");
            }else{
                $prodclient = $DB->query("SELECT *FROM client where positionc='{$_SESSION['lieuvente']}' and (typeclient='{$type1}' or typeclient='{$type2}') order by(nom_client) ");
            }
            $i=1;
            foreach ($prodclient as $key => $value) {
                $prodmax= $DB->querys("SELECT max(date_versement) as datev FROM bulletin where nom_client='{$value->id}' ");

                $now = date('Y-m-d');
                $datederniervers = $prodmax['datev'];

                $now = new DateTime( $now );
                $now = $now->format('Ymd');       

                $datederniervers = new DateTime($datederniervers);
                $datederniervers = $datederniervers->format('Ymd');

                $jourd=(new dateTime($now))->format("d");
                $moisd=(new dateTime($now))->format("m");
                $anneed=(new dateTime($now))->format("Y");

                $datealertemin = date("Ymd", mktime(0, 0, 0, $moisd, $jourd-$panier->delaialerte,   $anneed));

                $datealerte = date("Ymd", mktime(0, 0, 0, $moisd, $jourd-30,   $anneed));

                $delai=$panier->delai;

                $delaialerte=$panier->delaialerte;
                if ($panier->compteClient($value->id, 'gnf')<0) {
                    if ($datealerte>=$datederniervers) {
                        $soldeCumul+=(-$panier->compteClient($value->id, 'gnf')); ?>
                        <tr>
                            <td style="text-align: center;"><?=$i;?></td>
                            <td ><?=ucwords(strtolower($value->nom_client));?></td>
                            <td style="text-align: center; padding:5px;"><?=$value->telephone;?></td>
                            <td style="text-align: right; padding-right:10px;"><?=number_format(-$panier->compteClient($value->id, 'gnf'),0,',',' ');?></td>
                            <td style="text-align: center; padding-right:10px;"><?=(new dateTime($datederniervers))->format("d/m/Y");?></td>
                        </tr><?php
                        $i++;
                    }
                }
                
            }?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Cumul Créances</th>
                <th style="text-align:right; padding-right:10px;"><?=number_format($soldeCumul,0,',',' ');?></th>
            </tr>
        </tfoot>
    </table>

  
</page><?php 
  $content = ob_get_clean();
  try {
    $pdf = new HTML2PDF("p","A4","fr", true, "UTF-8" , 0);
    $pdf->pdf->SetAuthor('Amadou');
    $pdf->pdf->SetTitle("creancier");
    $pdf->pdf->SetSubject('Création d\'un Portfolio');
    $pdf->pdf->SetKeywords('HTML2PDF, Synthese, PHP');
    $pdf->pdf->IncludeJS("print(true);");
    $pdf->writeHTML($content);
    $pdf->Output(date("d/m/y").date("H:i:s").'.pdf');
    // $pdf->Output('Devis.pdf', 'D');    
  } catch (HTML2PDF_exception $e) {
    die($e);
  }
//header("Refresh: 10; URL=index.php");
?>