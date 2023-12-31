<?php require 'header.php';
 require 'headercmd.php';?>

<script>
  function suivant(enCours, suivant, limite){
    if (enCours.value.length >= limite)
    document.term[suivant].focus();
  }
</script>

<div class="box_stockinv" style="margin-top: 30px; width: 100%;"><?php  

  if (isset($_POST['qtiter'])) {

    $nomtab1=$panier->nomStock($_POST['departs'])[1];

    $idstock1=$panier->nomStock($_POST['departs'])[2];

    $id=$panier->h($_POST['id']);

    $qtite=$panier->h($_POST['qtiter']);

    $pachat=$_POST['pa'];

    $montantachat=$_POST['pa']*$_POST['qtiter'];

    $fournisseur=$_POST['client'];

    $depart = $DB->querys("SELECT quantite as qtite FROM `".$nomtab1."` WHERE idprod=?", array($id));

    $qtited=$depart['qtite']-$qtite;

    $dateop=$panier->h($_POST['dateop']);


    $prodnbre = $DB->querys("SELECT max(id) as nbre FROM retourlist ");

    $nbre=$prodnbre['nbre']+1;

    $DB->insert("UPDATE `".$nomtab1."` SET quantite= ? WHERE idprod = ?", array($qtited, $id));

    if (empty($dateop)) {
      
      $DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, idnomstock, dateop) VALUES(?, ?, ?, ?, ?, now())', array($id, 'retourp'.$nbre, 'retourp', -$qtite, $idstock1));

      $DB->insert('INSERT INTO retourlist (numero, idprod, stockret, quantiteret, pa, client, exect, dateop) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array('retourp'.$nbre, $id, $idstock1, $qtite, $pachat, $fournisseur, $_SESSION['idpseudo']));

      $DB->insert('INSERT INTO bulletin (nom_client, montant, devise, libelles, numero, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array($fournisseur, -$montantachat, 'gnf', "retour produit", 'retourp'.$nbre, 1, $idstock1));

    }else{

      $DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, idnomstock, dateop) VALUES(?, ?, ?, ?, ?, ?)', array($id, 'retourp'.$nbre, 'sortie', -$qtite, $idstock1, $dateop));

      $DB->insert('INSERT INTO retourlist (numero, idprod, stockret, quantiteret, pa, client, exect, dateop) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array('retourp'.$nbre, $id, $idstock1, $qtite, $pachat, $fournisseur, $_SESSION['idpseudo'], $dateop));

      $DB->insert('INSERT INTO bulletin (nom_client, montant, devise, libelles, numero, caissebul, lieuvente, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array($fournisseur, -$montantachat, 'gnf', "retour produit", 'retourp'.$nbre, 1, $idstock1, $dateop));

    }

  }?>

  <table class="payement">

    <form method="GET" action="retourproduit.php" id="suite" name="term">

      <thead>

        <tr>          
          <th colspan="8" height="30">Effectuez un Retour <a href="retourlist.php?ajout" style="color:orange; font-size:25px;">Voir les retours</a></th>
        </tr>      

        <tr>
          <th colspan="4">
            <input type = "search" name = "terme" placeholder="rechercher un produit" onKeyUp="suivant(this,'s', 10)" onchange="document.getElementById('suite').submit()"/>
          <input name = "s"  style="width: 0px; height: 0px;" /></th>
          <th colspan="4"><input  id="search-user" type="text" name="client" placeholder="rechercher dans une liste" />
            <div style="color:white; background-color: black; font-size: 11px;" id="result-search"></div></th>
        </tr>
       

        <tr>
          <th>N°</th>
          <th>Désignation</th>         
          <th>Magasin Retraît</th>
          <th>Qtite à Retourner</th>
          <th>Prix Achat</th>
          <th>Fournisseur</th>
          <th>Date</th>
          <th></th>
        </tr>

      </thead>
    </form>

    <tbody>

      <?php
      $tot_achat=0;
      $tot_revient=0;
      $tot_vente=0;
      $qtiteR=0;
      $qtiteS=0;

      $type="en_gros";

      if (!isset($_GET['termeliste'])) {

        if (isset($_GET['terme'])) {

          if (isset($_GET["terme"])){

              $_GET["terme"] = htmlspecialchars($_GET["terme"]); //pour sécuriser le formulaire contre les failles html
              $terme = $_GET['terme'];
              $terme = trim($terme); //pour supprimer les espaces dans la requête de l'internaute
              $terme = strip_tags($terme); //pour supprimer les balises html dans la requête

              $_SESSION['terme']=$terme;
          }

          if (isset($terme)){

            $terme = strtolower($terme);
            $products = $DB->query("SELECT * FROM productslist WHERE (designation LIKE ? OR Marque LIKE ?) and type LIKE ? order by(designation)",array("%".$terme."%", "%".$terme."%", $type));
          }else{

           $message = "Vous devez entrer votre requete dans la barre de recherche";

          }

          if (empty($products)) {?>

            <div class="alertes">Produit indisponible<a href="ajout.php">Ajouter le produit</a></div><?php

          }

        }else{

          if (!empty($_SESSION['terme'])) {
            
            $products = $DB->query("SELECT * FROM productslist WHERE (designation LIKE ? OR Marque LIKE ?) and type LIKE ? order by(designation)",array("%".$_SESSION['terme']."%", "%".$_SESSION['terme']."%", $type));

          }else{
            

            $products = $DB->query("SELECT * FROM productslist where type='{$type}' order by(designation) LIMIT 50");
          }
        }
      }else{

        $products = $DB->query("SELECT * FROM productslist WHERE id= ? order by(designation)",array($_GET['termeliste']));
      }

      if (!empty($products)) {

        foreach ($products as $key=> $product){

          if ($product->type=='paquet') {
            $color='green';
          }elseif ($product->type=='detail') {
            $color='blue';
          }else{
            $color='';
          }?>

          <tr>
            <td><?=$key+1;?></td>  

            <td style="font-size: 15px; color:<?=$color;?>"><?= ucwords(strtolower($product->designation)); ?></td>

            <form action="retourproduit.php" method="POST">

              <td>
                <select name="departs" required="">
                  <option></option><?php 

                  foreach ($panier->listeStock() as $value) {

                    $reststock=$DB->querys("SELECT quantite as qtite FROM `".$value->nombdd."` WHERE idprod='{$product->id}'");

                      if (!empty($reststock['qtite'])) {?>

                        <option style="font-size:18px; color:orange;" value="<?=$value->id;?>"><?=$value->nomstock.' dispo '.$reststock['qtite'];?></option><?php
                      }
                  }?>
                </select>
              </td>

              <td><input type="number" name="qtiter" min="0" style="width: 95%;" /><input type="hidden" name="id" value="<?=$product->id;?>"></td>

              <td><input type="text" name="pa" min="0"  style="width: 95%;" /></td>

              <td>
                <select  type="text" name="client" required="">
                  <option></option><?php
                  $type1='Fournisseur';
                  $type2='Clientf';
                  foreach($panier->clientF($type1, $type2) as $product){?>

                    <option value="<?=$product->id;?>"><?=$product->nom_client;?></option><?php

                  }?>
                </select>
              </td>

              <td><input type="date" name="dateop"></td>

              <td><input type="submit" name="valids" value="retourner" style="width: 95%; font-size: 16px; background-color: orange;color: white; cursor: pointer;" onclick="return alerteT();" ></td>

            </form>


          </tr><?php
        }
      }?>


    </tbody>

  </table>

  
</div> 

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function(){
        $('#search-user').keyup(function(){
            $('#result-search').html("");

            var utilisateur = $(this).val();

            if (utilisateur!='') {
                $.ajax({
                    type: 'GET',
                    url: 'rechercheproduit.php?retourp',
                    data: 'user=' + encodeURIComponent(utilisateur),
                    success: function(data){
                        if(data != ""){
                          $('#result-search').append(data);
                        }else{
                          document.getElementById('result-search').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
                        }
                    }
                })
            }
      
        });
    });
</script>


<script type="text/javascript">
    function alerteS(){
        return(confirm('Valider la suppression'));
    }

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }

    function alerteT(){
        return(confirm('Confirmer le transfert des produits'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }


    window.onload = function() { 
        for(var i = 0, l = document.getElementsByTagName('input').length; i < l; i++) { 
            if(document.getElementsByTagName('input').item(i).type == 'text') { 
                document.getElementsByTagName('input').item(i).setAttribute('autocomplete', 'off'); 
            }; 
        }; 
    };

</script>  