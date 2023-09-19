<?php 

if (isset($_GET['delete'])) {

	$DB->delete('DELETE FROM clientrelance WHERE id = ?', array($_GET['delete']));
}

if (isset($_POST['valid']) and !empty($_POST['message'])) {

	$message=$panier->h($_POST['message']);
	$relance=$_POST['relance'];
	$idclient=$panier->h($_POST['client']);
	$idpers=$panier->h($_SESSION['idpseudo']);

	if (empty($relance)) {

		$DB->insert('INSERT INTO clientrelance (idclient, idpers, message, dateop) VALUES(?, ?, ?, now())', array($idclient, $idpers, $message));
	}else{

		$DB->insert('INSERT INTO clientrelance (idclient, idpers, message, daterelance, dateop) VALUES(?, ?, ?,  ?, now())', array($idclient, $idpers, $message, $relance));
	}

}

if (isset($_GET['ajout'])) {?>

	<form method="POST" action="clientgestion.php?suiviclient=<?=$_GET['suiviclient'];?>&nomclient=<?=$_GET['nomclient'];?>">
	  <fieldset>
	    <legend>Compte rendu d'échange</legend>

	    <div class="row">

		    <div class="col-sm-12 col-md-6">							    

			    <div class="mb-1">
			      <label class="form-label"><?=$_GET['nomclient'];?></label>
			      <input type="hidden"  class="form-control" name="client" value="<?=$_GET['suiviclient'];?>">
			    </div>

			    <div class="mb-1">
			      <label class="form-label">Message*</label>
			      <textarea type="text"  class="form-control" name="message" maxlength="300" required=""></textarea>
			    </div>

			    <div class="mb-1">
			      <label class="form-label">Prochaine Relance</label>
			      <input type="date"  class="form-control" name="relance">
			    </div>
			</div>
		</div>

	    <button type="submit" name="valid" class="btn btn-primary">Valider</button>
	  </fieldset>
	</form><?php 
}?>


<table class="table table-hover table-bordered table-striped table-responsive bg-light">
  <thead>

  	<tr>
  		<th colspan="6" scope="col" class="text-center bg-info"><a class="text-white" href="clientgestion.php?ajout&suiviclient=<?=$_GET['suiviclient'];?>&nomclient=<?=$_GET['nomclient'];?>">Ajouter une conversation</a></th>
        
    </tr>
  	<tr>
  		<th colspan="6" scope="col" class="text-center bg-info">Liste des Conversations</th>        
    </tr>
    <tr>
      <th scope="col" class="text-center">N°</th>
      <th scope="col" class="text-center">Messages</th>
      <th scope="col" class="text-center">Saisi le</th>
      <th scope="col">Date de relance</th>
      <th class="text-center">Saisi par</th>
      <th scope="col" class="text-center">Actions</th>
    </tr>
  </thead>
  <tbody><?php 

  	$prod= $DB->query("SELECT * FROM clientrelance where idclient='{$_GET['suiviclient']}' ORDER BY (id) desc");
  	foreach ($prod as $key => $value) {?>
	    <tr>
	      <th scope="row" class="text-center"><?=$key+1;?></th>

	      <td><?=$value->message;?></td>

	      <td><?=(new dateTime($value->dateop))->format("d/m/Y");?></td>

	      <td><?=(new dateTime($value->daterelance))->format("d/m/Y");?></td>

	      <td><?=$panier->nomPersonnel($value->idpers);?></td>
	      
	      <td><?php if ($_SESSION['level']>1) {?><a class="btn btn-danger" href="clientgestion.php?delete=<?=$value->id;?>&suiviclient=<?=$_GET['suiviclient'];?>&nomclient=<?=$_GET['nomclient'];?>" onclick="return alerteS();">Supprimer</a><?php }?></td>
	    </tr><?php 
	}?>
    
  </tbody>
</table>		