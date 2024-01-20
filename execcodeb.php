<?php require 'header.php';

$prod = $DB->query('SELECT * FROM productslist ');

foreach ($prod as $key => $value) {

	$codeb=$value->codeb;
	$id=$value->id;
	
	//$DB->insert('UPDATE productslist SET codeb = ? WHERE id = ?', array($codeb, $id));

	$DB->insert('UPDATE stock1 SET codeb = ? WHERE idprod = ?', array($codeb, $id));
}