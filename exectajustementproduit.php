<?php require 'header.php';

$prod1 = $DB->query('SELECT * FROM stock1');

foreach ($prod1 as $key => $value) {
    
	$pa=$value->prix_achat;
	$pr=$value->prix_revient;

	$id=$value->idprod;

	$prodverif = $DB->querys("SELECT * FROM productslist where id='{$id}' ");

	if (empty($prodverif['id'])) {
        $prodachat = $DB->querys("SELECT * FROM achat where id_produitfac='{$id}' ");
        $designationachat=$prodachat['designation'];
        $prodcmd = $DB->querys("SELECT * FROM commande where id_produit='{$id}' ");
        $prod = $DB->querys("SELECT * FROM productslist where designation='{$designationachat}' ");
        $idproducts=$prod['id'];
        

        if (empty($prodcmd['id'])) {
            echo $id." ".$designationachat;?><br/><?php 
            $DB->delete("DELETE FROM stock1 where idprod='{$id}' ");             
        }else{
            echo $id." ".$designationachat." new id ".$idproducts;?><br/><?php 
		    $DB->insert('UPDATE commande SET id_produit=? WHERE id_produit = ?', array($idproducts, $id));
        }
	}
}

$prod2 = $DB->query('SELECT * FROM stock2');

foreach ($prod2 as $key => $value) {
    
	$pa=$value->prix_achat;
	$pr=$value->prix_revient;

	$id=$value->idprod;

	$prodverif = $DB->querys("SELECT * FROM productslist where id='{$id}' ");

	if (empty($prodverif['id'])) {
        $prodachat = $DB->querys("SELECT * FROM achat where id_produitfac='{$id}' ");
        $designationachat=$prodachat['designation'];
        $prodcmd = $DB->querys("SELECT * FROM commande where id_produit='{$id}' ");
        $prod = $DB->querys("SELECT * FROM productslist where designation='{$designationachat}' ");
        $idproducts=$prod['id'];
        

        if (empty($prodcmd['id'])) {
            echo $id." produit supprime ".$value->quantite." ".$designationachat;?><br/><?php            
            $DB->delete("DELETE FROM stock2 where idprod='{$id}' ");           
        }else{
            echo $id." ".$designationachat." new id ".$idproducts;?><br/><?php 
		    //$DB->insert('UPDATE commande SET id_produit=? WHERE id_produit = ?', array($idproducts, $id));
        }
	}
}


$prodp1 = $DB->query('SELECT * FROM productslist');

foreach ($prodp1 as $key => $value) {
	$id=$value->id;
    $designation=$value->designation;

	$zero=0;

	$prodverif = $DB->querys("SELECT * FROM stock1 where idprod='{$id}' ");

	if (empty($prodverif['id'])) {

        echo $id." ".$designation;?><br/><?php

        $DB->insert("INSERT INTO stock1 (idprod, codeb, prix_achat, prix_revient, prix_vente, nbrevente, type, qtiteintd, qtiteintp) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)", array($id, $value->codeb, 0, 0, $value->pventel, 0, $value->type, $value->qtiteint, $value->qtiteintp));

		//$DB->insert('UPDATE commande SET prix_revient=? WHERE id_produit = ?', array($pr, $id));
	}
}

$prodp2 = $DB->query('SELECT * FROM productslist');

foreach ($prodp2 as $key => $value) {
	$id=$value->id;
    $designation=$value->designation;

	$zero=0;

	$prodverif = $DB->querys("SELECT * FROM stock2 where idprod='{$id}' ");

	if (empty($prodverif['id'])) {

        echo $id." ".$designation;?><br/><?php

        $DB->insert("INSERT INTO stock2 (idprod, codeb, prix_achat, prix_revient, prix_vente, nbrevente, type, qtiteintd, qtiteintp) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)", array($id, $value->codeb, 0, 0, $value->pventel, 0, $value->type, $value->qtiteint, $value->qtiteintp));

		//$DB->insert('UPDATE commande SET prix_revient=? WHERE id_produit = ?', array($pr, $id));
	}
}