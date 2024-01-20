<?php
require 'header.php';


$prodenseig=$DB->query('SELECT * from modifcommande');

foreach ($prodenseig as $value) {

	$prodpaie=$DB->querys("SELECT * from payement where num_cmd='{$value->num_cmd}' ");

	if (empty($prodpaie)) {?>

		<div><?= $value->num_cmd;?></div><br/><?php
	}


}