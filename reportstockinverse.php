<?php
require 'header.php';

/*

$quantite=0;
$qtiteint=0;
$nbre=0;

$products=$DB->query('SELECT id, quantite from magmadina');

$DB->delete('DELETE FROM stockmouv ');

foreach ($products as $key => $value) {

	$DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, idnomstock, dateop) VALUES(?, ?, ?, ?, ?, now())', array($value->id, 'report stock', 'entree', $value->quantite, 1));
}

*/

$quantite=0;
$qtiteint=0;
$stock=1;
$dateop="20230706";

$nomtab=$panier->nomStock($stock)[1];

$products=$DB->query("SELECT idprod as id, quantite from stock1 ");

foreach ($products as $key => $value) {

	$prodmouv=$DB->querys("SELECT id, sum(quantitemouv) as qtitem from stockmouv where idstock='{$value->id}' and idnomstock='{$stock}' and DATE_FORMAT(dateop, \"%Y%m%d\")='{$dateop}' ");

	$qtites=$value->quantite;
	$qtitem=$prodmouv['qtitem'];

	$difference=$qtites+$qtitem;
    //var_dump($value->id." q".$qtites.' qtite'.$qtitem.' dif'.$difference);

	//$DB->insert("UPDATE stock1 SET quantite='{$difference}' where idprod='{$value->id}' ");
}










