<?php
require_once("_header.php");
// require_once('vendor/autoload.php');
// require "createToken.php";

header("Access-Control-Allow-Origin: *");

// ici je vais preciser au client que nous allons retouer des données sous format json
header("Content-Type:application/json");

// ici je vais definir les methodes (modes d'access) acceptable pour acceder a cette api
header('Access-Control-Allow-Methods: GET, POST,PUT');
// ici je vais recuperer tous mes utilisateurs
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');
$getData=json_decode(file_get_contents("php://input"),true);
$_SESSION['lieuvente']=1;
$getBenefit=$panier->benefice($getData["date1"],$getData["date2"]);
unset($_SESSION['lieuvente']);
//var_dump($getBenefit);
// si mon utilisateur est bien authentifié, il nous a fourni le bon email et le bon mot de passe

    // je vais lui creer un token
    $createToken=0;
    // je vais renvoyer ce token pour que il puisse etre utilisé par mon front-end
    echo json_encode(["benefit"=>$getBenefit,"token"=>$createToken]);
?>