<?php
require 'header.php';

if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];
  $products = $DB->querys('SELECT statut, level FROM login WHERE pseudo= :PSEUDO',array('PSEUDO'=>$pseudo));

  if ($products['level']>=3) {

    if(isset($_GET["delete"])){

      $numero=$_GET["delete"];

      $DB->delete("DELETE FROM categorie WHERE id = ?", array($numero));
    }

    if(isset($_POST["id"])){

      $numero=$panier->h($_POST['id']);
      $nom=$panier->h($_POST['nom']);
      $DB->insert("UPDATE categorie SET nom= ? WHERE id = ?", array($nom, $numero)); 
    }?>

    <div style="display: flex;" >

      <div><?php require 'navstock.php';?></div>

      <div><?php 

        $prodm=$DB->query("SELECT *from categorie order by(nom)");?>
            
        <table class="payement">
          <thead>

            <tr>
              
              <th height="25" colspan="4" style="text-align: center">Liste des Catégories</th>

            </tr>

            <tr>
              <th>N°</th>
              <th>Désignation</th>
              <th></th>
              <th></th>
            </tr>

          </thead>

          <tbody><?php

            if (empty($prodm)) {
              # code...
            }else{
              $cumultranche=0;
              foreach ($prodm as $key=> $formation) {

                $prodverif=$DB->querys("SELECT *from categorie inner join productslist on codecat=categorie.id inner join commande on productslist.id=id_produit where categorie.id='{$formation->id}'"); ?>

                

                  <tr>
                    <td style="text-align: center;"><?=$key+1;?></td>

                    <form method="POST" action="categorie.php">

                      <td><input style="text-align: left" type="text" name="nom" value="<?=ucwords(strtolower($formation->nom));?>"/><input type="hidden" name="id" value="<?=$formation->id;?>"></td>

                      <td><input type="submit" name="update" value="Modifier" style="cursor: pointer; background-color: orange; color: white;"></td>
                    </form>

                    <td colspan="1">

                      <?php if ($products['statut']=='admin' and empty($prodverif['id'])) {?>
                        
                        <a href="categorie.php?delete=<?=$formation->id;?>" onclick="return alerteS();"><input type="button" value="Supprimer" style="width: 95%; font-size: 16px; background-color: red; color: white; cursor: pointer"></a><?php 
                      }?>
                    </td>

                  </tr><?php
              }

            }?>          
          </tbody>

              
        </table>

      </div>
    </div><?php

  }else{?>

    <div class="alertes">VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES</div><?php
  }

}else{

}?>

<script type="text/javascript">
    function alerteS(){
        return(confirm('Valider la suppression'));
    }

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }

</script>
</body>

</html>
