<table class="table table-hover table-bordered table-striped table-responsive text-center bg-light">

    <thead>

      <tr>
        <tr><th colspan="3"><?="Top 5 des Produits Vendus";?></th></tr>

        <tr>
          <th>N°</th>
          <th>Désignation</th>
          <th>Qtités</th>
        </tr>

    </thead>

    <tbody><?php 
        $cumul=0;
        $cumulben=0;

        $prod = $DB->query('SELECT *FROM productslist');

        foreach ($prod as $product ){

          $nbre=$panier->nbreprodstatpardate($product->id, 0, 0);

          //$benefice=$panier->beneficeprodstatpardate($product->id, $_SESSION['date1'], $_SESSION['date2']);

          if (!empty($nbre)) {
            
            $DB->insert('INSERT INTO intertopproduit (idprod, quantite, benefice, pseudo) VALUES(?, ?, ?, ?)', array($product->id, $nbre, 0, $_SESSION['idpseudo']));
          }
        }

        $products = $DB->query("SELECT *FROM intertopproduit where pseudo='{$_SESSION['idpseudo']}' order by(quantite) desc");


        foreach ($products as $key=> $product ){

          if ($key<=4) {

            $cumul+=$product->quantite;
            $cumulben+=$product->benefice;?>

            <tr>

              <td style="text-align: center;"><?= $key+1; ?></td>

              <td><?=ucwords(strtolower($panier->nomProduit($product->idprod))); ?></td>

              <td style="text-align: center;"><?=number_format($product->quantite,0,',',' ');?></td>

            </tr><?php
          }

        }?>

    </tbody>

    <tfoot>
        <tr>
            <th colspan="2">Totaux</th>
            <th style="text-align: center;"><?=number_format($cumul,0,',',' ');?></th>
        </tr>
    </tfoot>

  </table><?php 
        $DB->delete("DELETE FROM intertopproduit where pseudo='{$_SESSION['idpseudo']}'");?>