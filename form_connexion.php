<?php require '_header.php'?>

<!DOCTYPE html>
<html>
<head>
    <title>logescom-ms@</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">  
</head>

<body>
	<div class="container-fluid" style="background-image: url('css/img/fouta.jpg');">

		<div class="row" style="height:100vh;"><?php

			$products = $DB->query('SELECT num_licence, nom_societe, DATE_FORMAT(date_souscription, \'%d/%m/%Y\') AS datesouscript, DATE_FORMAT(date_fin, \'%d/%m/%Y\') AS datefin, date_fin FROM licence');

		    foreach ( $products as $product ):?>

		   	<?php endforeach; ?><?php
		   	$now = date('Y-m-d');
		   	$datefin = $product->date_fin;

		   	$now = new DateTime( $now );
		   	$now = $now->format('Ymd');
		   	$datefin = new DateTime( $datefin );
		   	$datefin = $datefin->format('Ymd');?><?php

		   	if ($datefin-$now<6 AND $datefin-$now>0 ) {?>

		   		<div class="alert alert-warning">Votre Licence expire dans <?=$datefin-$now ;?> jour(s)</div><?php				

		   	}elseif ($panier->licence()=="expiree") {?>

		   		<div class="alert alert-danger">Votre Licence est expirée Contacter DAMKO</div><?php
		   		
		   	}else{

		   	}?> 

			<div class="col-sm-12 col-md-10 pt-3 pb-3 m-auto" style="background-image: url('css/img/fond.jpg'); box-shadow: 2px 1px 10px;">
				
				<div class="row">
		          <a style="text-decoration: none" href="table.php?surplace">
		              <div class="card m-auto" style="width: 8rem;">
		                <img src="css/img/logodamko.jpg" class="card-img-top m-auto" alt="..." style="width: 6rem; height: 6rem">
		                <div class="card-bod m-auto text-center">
		                  <h5 class="card-title">LOGESCOM MS</h5>
		                </div>
		              </div>
		          </a>
		        </div>

				<!-- <div class="alert alert-danger text-center fw-bold">Chers Client, votre licence annuelle prend fin le 07/12 2023.<br>Merci de prendre contact avec votre gestionnaire DAMKO afin de declencher la procédure de renouvellement.<br>Nous vous remercions de votre confiance !!! 
				</div> -->

				<div  style="background-color: #253553; color: white; display:flex; justify-content:space-around;align-items: center; ">
					
					<img src="css/img/logo.jpg" class="card-img-top m-auto" alt="..." style="width: 6rem; height: 6rem; border-radius:10px;">				

					<div class="col-10 m-auto p-3 " style="background-color: #253553; color: white">
						<form class="form" action="connexion1.php" method="post" name="connexion">
							<fieldset><legend class="text-center ">Acceder à votre espace <?=ucwords(strtolower($panier->adresse()));?></legend>

								<div class="col-sm-12 col-md-6 m-auto">

									<div class="mb-1">
										<label class="form-label">Nom d'utilisateur*</label> 
										<input class="form-control"  type="text" name="pseudo" id="pseudo" required=""  />
									</div>

									<div class="mb-1">
										<label class="form-label">Mot de passe*</label>
										<input  class="form-control" type="password" name="mdp" id="mdp" required=""  />
									</div>

									<input class="btn btn-info" type="submit" value="Connexion" style="cursor: pointer;" />

								</div>
							</fieldset>
						</form>
					</div>

					
				</div>


			    <div class="col p-3 mt-5 bg-opacity-5 border border-danger border-3 rounded" style="box-shadow: 2px 1px 10px;">

		        	<legend class="text-center" style="background-color: #253553; color: white; font-size: 18px;">À Propos de la licence et du logiciel LOGESCOM-MS</legend>
		        	<div class="col">Développé par la société DAMKO</div>
		            <div class="col">Siège Social: Labé République de Guinée</div>
		            <div class="col">Matricule N°: 11978 </div>
		            <div class="col">NIF: 482907474</div>		            
		            <div class="col">Tel:(00224) 628 19 66 28</div>
		            <div class="col">Email:gestcomdev@gmail.com</div>
		            <div class="col">Numéro licence: <?= $product->num_licence; ?> </div>
		            <div class="col">Date de souscription: <?= $product->datesouscript; ?> </div>
		            <div class="col" style="color: red;">Valable jusqu'au: <?= $product->datefin; ?> à 23H59</div>
		            <div class="copyright"><img src="img/copyright.jpg"> </div>	
		        </div>
	        </div>
		</div>
	</div>   	
</body>

</html>