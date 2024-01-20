
<?php
require 'header.php';

$zero=0;

$DB->insert("UPDATE stocklamb SET quantite = ? ",array($zero));

//$DB->delete('DELETE FROM stockmouv WHERE id > ?', array($zero));