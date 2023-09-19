<?php
require 'header.php';

if (!empty($_SESSION['pseudo'])) {

	if ($products['level']>=3) {

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

		    $_SESSION['date1']=$_POST['j1'];
		    $_SESSION['date1'] = new DateTime($_SESSION['date1']);
		    $_SESSION['date1'] = $_SESSION['date1']->format('Ymd');
		    
		    $_SESSION['date2']=$_POST['j2'];
		    $_SESSION['date2'] = new DateTime($_SESSION['date2']);
		    $_SESSION['date2'] = $_SESSION['date2']->format('Ymd');

		    $_SESSION['dates1']=$_SESSION['date1'];
		    $_SESSION['dates2']=$_SESSION['date2'];   
		}

    	require 'navbulletin.php';
    	$client=$_GET['bclient'];
    	$devise=$_GET['devise'];

    	$soldea=0;
      	$solded=0;
      	$soldes=0;
      	$soldet=0;
      	$solde=0;
      	$zero=0;
      	if (isset($_POST['j1']) or isset($_POST['j2'])) {

        	$prod =$DB->query("SELECT client.nom_client as client, libelles, numero, bulletin.montant as montant, date_versement, bulletin.devise as devise, bulletin.lieuvente as lieuvente, numedit as numfact, libelle, bl, nature  FROM bulletin inner join client on client.id=bulletin.nom_client inner join editionfacture on numero=numedit WHERE bulletin.nom_client='{$client}' and DATE_FORMAT(date_versement, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(date_versement, \"%Y%m%d\") <='{$_SESSION['date2']}' ORDER BY (date_versement)");

      	}else{

        	$prod =$DB->query("SELECT editionfacture.id as idedit, client.nom_client as client, libelles, numero, bulletin.montant as montant, date_versement, bulletin.devise as devise, bulletin.lieuvente as lieuvente, numedit as numfact, libelle, bl, nature  FROM bulletin inner join client on client.id=bulletin.nom_client left join editionfacture on numero=numedit WHERE bulletin.nom_client='{$client}' and bulletin.devise='{$devise}' ORDER BY (date_versement)");
      	}?>

    	<table class="payement">
    		<thead>
    			<tr>
    				<th colspan="7" style="font-size: 20px;">Relevé de <?=$panier->nomClient($client);?> Tel: <?=$panier->nomClientad($client)[1];?><a style="margin-left: 10px;"href="printcompte.php?compte=<?=$client;?>&tel=<?=$panier->nomClientad($client)[1];?>&devise=<?=$devise;?>&transitaire" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/pdf.jpg"></a></th>

    				<th style="font-size: 20px; color: orange;">Compte <?=strtoupper($devise);?></th>
    			</tr>

    			<tr>
    				<th>N°</th>
    				<th>Date</th>
    				<th>BL</th>
    				<th>Nature</th>
    				<th>Désignation</th>
    				<th>Entrées</th>
    				<th>Sorties</th>
    				<th>Solde</th>
    			</tr>
    		</thead>
    		<tbody><?php 
    			$solde=0;
    			foreach ($prod as $key => $value) {

    				$solde+=$value->montant;?>

    				<tr>
						<td style="text-align: center;"><?=$key+1;?></td>

						<td style="text-align: center;"><?=(new dateTime($value->date_versement))->format('d/m/Y');?></td><?php 

						if($value->libelles!='reste à payer' and $value->montant>=0){

							$solded+=$value->montant;?>							

							<td style="text-align: center"><?=strtoupper($value->bl);?> <?php
			                  $num='fact'.$value->idedit;
			                  $nom_dossier="editfacture/".'fact'.$value->idedit."/";
			                  if (file_exists($nom_dossier)) {

			                      $dossier=opendir($nom_dossier);
			                      while ($fichier=readdir($dossier)) {

			                          if ($fichier!='.' && $fichier!='..') {?>

			                              <a href="editfacture/<?='fact'.$value->idedit;?>/<?=$fichier;?>" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/pdf.jpg"></a><?php
			                          }
			                      }closedir($dossier);
			                  }?>
                			</td>

							<td><?=strtoupper($value->nature);?></td>

							<td><?=strtoupper($value->libelles);?></td>

							<td style="font-size: 20px; text-align: right; color: white; background-color: <?=$panier->color($value->montant);?>"><?=number_format($value->montant,0,',',' ');?></td>
							<td></td>
							<td style="font-size: 20px; text-align: right; color: white; background-color: <?=$panier->color($solde);?>"><?=number_format(-$solde,0,',',' ');?></td><?php

						}else{

							$soldes+=$value->montant;?>

							<td><a style="color: red;" target="_blank" href="printcmdfrais.php?print=<?=$value->numero;?>&client=<?=$value->client;?>&lieuvente=<?=$value->lieuvente;?>&montant=<?=$value->montant;?>&client=<?=$client;?>"><?=strtoupper($value->bl);?></a></td>

							<td><?=strtoupper($value->nature);?></td>

							<td><?=strtoupper($value->libelles);?></td>

							<td></td>

							<td style="font-size: 20px; text-align: right; color: white; background-color: <?=$panier->color($value->montant);?>"><?=number_format((-1)*$value->montant,0,',',' ');?></td>

							<td style="font-size: 20px; text-align: right; color: white; background-color: <?=$panier->color($solde);?>"><?=number_format(-$solde,0,',',' ');?></td><?php

						}?>
					</tr><?php 
				}?>
			</tbody>

			<tfoot>
				<tr>
					<th colspan="5">Totaux</th>

					<th style="font-size: 20px; text-align: right; color: white; background-color: <?=$panier->color($solded);?>"><?=number_format($solded,0,',',' ');?></th>

					<th style="font-size: 20px; text-align: right; color: white; background-color: <?=$panier->color($soldes);?>"><?=number_format(-$soldes,0,',',' ');?></th>

					<th style="font-size: 20px; text-align: right; color: white; background-color: <?=$panier->color($solde);?>"><?=number_format(-$solde,0,',',' ');?></th>
				</tr>
			</tfoot>

    	</table><?php


    }else{?>

    	<div class="alertes">Vous n'avez pas toutes les autorisations réquises</div><?php

    }
	// code...
}