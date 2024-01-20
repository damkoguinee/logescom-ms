<?php require 'header.php';

$prod = $DB->query('SELECT * FROM commande');

foreach ($prod as $key => $value) {
	$qtite=$value->quantity;
	$id=$value->id;
	$DB->insert('UPDATE commande SET qtiteliv=? WHERE id = ?', array($qtite, $id));	
	$DB->insert('UPDATE payement SET etatliv=? ', array('livre'));	
}