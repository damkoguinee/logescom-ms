<?php require 'header.php';

$prod = $DB->query('SELECT * FROM bulletin');

foreach ($prod as $key => $value) {

	$prodverif = $DB->querys("SELECT * FROM client where id='{$value->nom_client}' ");
	/*

	$fraisup = $DB->querys("SELECT sum(prix_vente*quantity) as totalcmd, id_client FROM commande where num_cmd='{$value->num_cmd}'  ");

	$pv=$prodverif['totalcmd'];
	$tot=$value->Total;
	$difference=$tot-$pv;

	*/

    if (!empty($value->montant)) {

        if ($value->lieuvente!=$prodverif['positionc']) {?><br><?php

            echo $prodverif['id'].' '.$prodverif['nom_client'].' lieu de vente '.' '.$value->lieuvente.' '.$value->libelles.'  montant= '.$value->montant;?></br><?php

            //$DB->delete('DELETE FROM bulletin WHERE nom_client = ?', array($value->nom_client));
            //$DB->delete('DELETE FROM bulletin WHERE nom_client = ?', array($vide));

            //$DB->delete('DELETE FROM versement WHERE nom_client = ?', array($value->nom_client));

            //$DB->delete('DELETE FROM decaissement WHERE client = ?', array($value->nom_client));
        }
    }
}