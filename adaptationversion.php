<?php
require 'header.php';

//Attention sauvegarder la base de données avant de commencer

//Ajouter une colonne nbrevente dans la table catégorie
//Importer la table productslist de Abda puis vider la table
//Ajouter un champs qui sappelle name dans productslist

//renommer la table bulletin en bulletin1
//importer la table bulletin de la nouvelle version puis la vider



$products=$DB->query("SELECT *FROM products");

foreach ($products as $value) {
        
	$DB->insert("UPDATE productslist SET codeb=?, name=?, Marque=?, designation=?, pventel=?, nbrevente=?, type=?, qtiteint=?, qtiteintp=? WHERE idprod = ?", array($value->codeb, $value->name, $value->Marque, $value->designation, $value->prix_vente, $value->nbrevente, $value->type, $value->quantite_int, 0, $value->id));
}

$products=$DB->query("SELECT *FROM categorie");

foreach ($products as $value) {
        
	$DB->insert("UPDATE productslist SET codecat=? WHERE name = ?", array($value->id, $value->nom));
}

/*

// supprimer le champs name dans productslist

//renommer le name en idprod puis transformer en entier
//renommer le champs designation en qtiteintp puis le mettre en entier
//supprimer les champs Marque
//modifier quantite_int en qtiteintd

foreach ($products as $value) {
        
	$DB->insert("UPDATE products SET idprod=? WHERE idprod = ?", array($value->id, $value->id));
}

// Exécuter le fichier
*/

//***********************adaptation bulletin*******************************

$prodclient=$DB->query("SELECT *FROM client");

foreach ($prodclient as $client) {
	
	$prodbul=$DB->querys("SELECT nom_client as client, sum(montant) as montant FROM bulletin1 where nom_client='{$client->id}' ");

	$DB->insert('INSERT INTO bulletin (nom_client, libelles, numero, montant, devise, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array($prodbul['client'], 'report solde', '0000', $prodbul['montant'], 'gnf', '1', '1'));
}
