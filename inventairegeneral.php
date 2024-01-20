<?php require 'header.php';

if (!isset($_POST['j1'])) {

    $_SESSION['date']=date("Ymd");  
    $dates = $_SESSION['date'];
    $dates = new DateTime( $dates );
    $dates = $dates->format('Ymd'); 
    $_SESSION['date']=$dates;
    $_SESSION['date1']=$dates;
    $_SESSION['date2']=$dates;
    $_SESSION['dates1']=$dates; 

  }else{

    $_SESSION['date1']=$_POST['j1'];
    $_SESSION['date1'] = new DateTime($_SESSION['date1']);
    $_SESSION['date1'] = $_SESSION['date1']->format('Ymd');
    $_SESSION['date2'] = new DateTime($_SESSION['date2']);
    $_SESSION['date2'] = $_SESSION['date2']->format('Ymd');

    $_SESSION['dates1']=$_SESSION['date1'];
    $_SESSION['dates2']=$_SESSION['date2']; 

      
  }

  if (isset($_POST['j2'])) {

    $datenormale='entre le '.$_SESSION['dates1'].' et le '.$_SESSION['dates2'];

  }else{

    $datenormale=(new DateTime($dates))->format('d/m/Y');
  }

require 'headercompta.php';

if (!isset($_POST['annee'])) {

  $_SESSION['date']=date("Y");
  
}else{

  $_SESSION['date']=$_POST['annee'];
  
}

if (!isset($_GET['cloture'])) { 
  $bdd='inventaire';   

  $DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `dettegnf` double DEFAULT '0',
    `creancegnf` double DEFAULT '0',
    `comptegnf` double DEFAULT '0',
    `stock` double DEFAULT '0',
    `pertes` double DEFAULT '0',
    `gain` double DEFAULT '0',
    `solde` double DEFAULT '0',
    `depenses` double DEFAULT '0',
    `lieuvente` int(2) DEFAULT '1',
    `anneeinv` int(4) DEFAULT '0',
    `dateop` date DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ");
}

$prod = $DB->query('SELECT * FROM bulletin');

foreach ($prod as $key => $value) {

  $prodverif = $DB->querys("SELECT * FROM client where id='{$value->nom_client}'  ");
  /*

  $fraisup = $DB->querys("SELECT sum(prix_vente*quantity) as totalcmd, id_client FROM commande where num_cmd='{$value->num_cmd}'  ");

  $pv=$prodverif['totalcmd'];
  $tot=$value->Total;
  $difference=$tot-$pv;

  */

  if (empty($prodverif['id'])) {

    $DB->delete('DELETE FROM bulletin WHERE nom_client = ?', array($value->nom_client));

    //$DB->delete('DELETE FROM versement WHERE nom_client = ?', array($value->nom_client));

    //$DB->delete('DELETE FROM decaissement WHERE client = ?', array($value->nom_client));
  }
}

  $tot_achat=0;
  $tot_vente=0;
  $tot_revient=0;
  foreach ($panier->listeStock() as $valueS) {

    $prodstock = $DB->querys("SELECT sum(prix_revient*quantite) as pr, sum(prix_achat*quantite) as pa, sum(prix_vente*quantite) as pv FROM `".$valueS->nombdd."`");

    $tot_achat+=$prodstock['pa'];
    $tot_vente+=$prodstock['pv'];
    $tot_revient+=$prodstock['pr'];
  }

  if (isset($_POST['tauxeu'])) {
    $_SESSION['tauxeu']=$_POST['tauxeu'];
    $_SESSION['tauxus']=$_POST['tauxus'];
    $_SESSION['tauxcfa']=$_POST['tauxcfa'];
  }
  if (empty($_SESSION['tauxeu'])) {
    $_SESSION['tauxeu']=1;
  }

  if (empty($_SESSION['tauxus'])) {
    $_SESSION['tauxus']=1;
  }

  if (empty($_SESSION['tauxcfa'])) {
    $_SESSION['tauxcfa']=1;
  }?>

  <form id='naissance' method="POST" action="inventairegeneral.php"> 

    <ol style="display: flex;">
      <li><label>Taux €</label><?php 
        if (isset($_POST['tauxeu'])) {?>
          <input style="text-align: center; font-size: 25px;" type="text" name="tauxeu" value="<?=$_SESSION['tauxeu'];?>" onchange="this.form.submit()"/><?php
        }else{?>
          <input style="text-align: center; font-size: 25px;" type="text" name="tauxeu" onchange="this.form.submit()"/><?php          
        }?>        
      </li>

      <li><label>Taux $</label><?php 
        if (isset($_POST['tauxeu'])) {?>
          <input style="text-align: center; font-size: 25px;" type="text" name="tauxus" value="<?=$_SESSION['tauxus'];?>" onchange="this.form.submit()"/><?php
        }else{?>
          <input style="text-align: center; font-size: 25px;" type="text" name="tauxus" onchange="this.form.submit()"/><?php          
        }?>        
      </li>

      <li><label>Taux CFA</label><?php 
        if (isset($_POST['tauxeu'])) {?>
          <input style="text-align: center; font-size: 25px;" type="text" name="tauxcfa" value="<?=$_SESSION['tauxcfa'];?>" onchange="this.form.submit()"/><?php
        }else{?>
          <input style="text-align: center; font-size: 25px;" type="text" name="tauxcfa" onchange="this.form.submit()"/><?php          
        }?>        
      </li>
    </ol>

    <ol>
      <li>
        <select  name="lieuIvent" onchange="this.form.submit()"><?php

          if (isset($_POST['lieuIvent']) and $_POST['lieuIvent']=='general') {?>

            <option value="<?=$_POST['lieuIvent'];?>">Général</option><?php
            
          }elseif (isset($_POST['lieuIvent'])) {?>

            <option value="<?=$_POST['lieuIvent'];?>"><?=$panier->nomStock($_POST['lieuIvent'])[0];?></option><?php
            
          }else{?>

            <option value="<?=$_SESSION['lieuvente'];?>"><?=$panier->nomStock($_SESSION['lieuvente'])[0];?></option><?php

          }

          if ($_SESSION['level']>6) {

            foreach($panier->listeStock() as $product){?>

              <option value="<?=$product->id;?>"><?=strtoupper($product->nomstock);?></option><?php

            }?>

            <option value="general">Général</option><?php
          }?>
        </select>
      </li>
    </ol>
  </form>

  <form id='liquide' method="POST" action="inventairegeneral.php">

    <div class="tbord" style="background-color: red;"><?php 

      if (!empty($panier->dette('gnf', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">DETTE GNF
            <div class="descriptm"><?=number_format($panier->dette('gnf', $_SESSION['date'])[0],0,',',' ') ;?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dette('eu', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">DETTE €
            <div class="descriptm"><?=number_format($panier->dette('eu', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dette('us', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">DETTE $
            <div class="descriptm"><?=number_format($panier->dette('us', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dette('cfa', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">DETTE CFA
            <div class="descriptm"><?=number_format($panier->dette('cfa', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php
      }      

      $tauxeu=$_SESSION['tauxeu'];
      $tauxus=$_SESSION['tauxus'];
      $tauxcfa=$_SESSION['tauxcfa'];
      $totaldettegnf=$panier->dette('gnf', $_SESSION['date'])[0]+$panier->dette('eu', $_SESSION['date'])[0]*$tauxeu+$panier->dette('us', $_SESSION['date'])[0]*$tauxus+$panier->dette('cfa', $_SESSION['date'])[0]*$tauxcfa;

      $totalcreancegnf=$panier->dette('gnf', $_SESSION['date'])[1]+$panier->dette('eu', $_SESSION['date'])[1]*$tauxeu+$panier->dette('us', $_SESSION['date'])[1]*$tauxus+$panier->dette('cfa', $_SESSION['date'])[1]*$tauxcfa;?>

      <div class="casem">
        <div class="descriptd">TOTAL DETTE GNF
          <div class="descriptm"><?=number_format($totaldettegnf,0,',',' ') ; ?></div>
        </div>
      </div>

    </div>

    <div class="tbord" style="background-color:green;"><?php 

      if (!empty($panier->dette('gnf', $_SESSION['date'])[1])) {?>

        <div class="casem">
          <div class="descriptd">CREANCE GNF
            <div class="descriptm"><?=number_format(-$panier->dette('gnf', $_SESSION['date'])[1],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dette('eu', $_SESSION['date'])[1])) {?>
        <div class="casem">
          <div class="descriptd">CREANCE €
            <div class="descriptm"><?=number_format(-$panier->dette('eu', $_SESSION['date'])[1],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dette('us', $_SESSION['date'])[1])) {?>

        <div class="casem">
          <div class="descriptd">CREANCE $
            <div class="descriptm"><?=number_format(-$panier->dette('us', $_SESSION['date'])[1],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dette('cfa', $_SESSION['date'])[1])) {?>

        <div class="casem">
          <div class="descriptd">CREANCE CFA
            <div class="descriptm"><?=number_format(-$panier->dette('cfa', $_SESSION['date'])[1],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }?>

      <div class="casem">
        <div class="descriptd">TOTAL CREANCE GNF
          <div class="descriptm"><?=number_format(-$totalcreancegnf,0,',',' ') ; ?></div>
        </div>
      </div>

    </div>


    <div class="tbord" style="background-color:orange;"><?php

      $totalcomptegnf=$panier->dispoCompte('gnf', $_SESSION['date'])[0]+$panier->dispoCompte('eu', $_SESSION['date'])[0]*$tauxeu+$panier->dispoCompte('us', $_SESSION['date'])[0]*$tauxus+$panier->dispoCompte('cfa', $_SESSION['date'])[0]*$tauxcfa;

      if (!empty($panier->dispoCompte('gnf', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">CAISSE GNF
            <div class="descriptm"><?=number_format($panier->dispoCompte('gnf', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dispoCompte('eu', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">CAISSE €
            <div class="descriptm"><?=number_format($panier->dispoCompte('eu', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dispoCompte('us', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">CAISSE $
            <div class="descriptm"><?=number_format($panier->dispoCompte('us', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dispoCompte('cfa', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">CAISSE CFA
            <div class="descriptm"><?=number_format($panier->dispoCompte('cfa', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }?>

      <div class="casem">
        <div class="descriptd">TOTAL CAISSE GNF
          <div class="descriptm"><?=number_format($totalcomptegnf,0,',',' ') ; ?></div>
        </div>
      </div>

    </div>

    <div class="tbord" style="background-color:orange;"><?php

      $totalcomptebanquegnf=$panier->dispoCompteBanque('gnf', $_SESSION['date'])[0]+$panier->dispoCompteBanque('eu', $_SESSION['date'])[0]*$tauxeu+$panier->dispoCompteBanque('us', $_SESSION['date'])[0]*$tauxus+$panier->dispoCompteBanque('cfa', $_SESSION['date'])[0]*$tauxcfa;

      if (!empty($panier->dispoCompteBanque('gnf', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">BANQUE GNF
            <div class="descriptm"><?=number_format($panier->dispoCompteBanque('gnf', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dispoCompteBanque('eu', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">BANQUE €
            <div class="descriptm"><?=number_format($panier->dispoCompteBanque('eu', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dispoCompteBanque('us', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">BANQUE $
            <div class="descriptm"><?=number_format($panier->dispoCompteBanque('us', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }

      if (!empty($panier->dispoCompteBanque('cfa', $_SESSION['date'])[0])) {?>

        <div class="casem">
          <div class="descriptd">BANQUE CFA
            <div class="descriptm"><?=number_format($panier->dispoCompteBanque('cfa', $_SESSION['date'])[0],0,',',' ') ; ?></div>
          </div>
        </div><?php 
      }?>

      <div class="casem">
        <div class="descriptd">TOTAL BANQUE GNF
          <div class="descriptm"><?=number_format($totalcomptebanquegnf,0,',',' ') ; ?></div>
        </div>
      </div>

    </div>

    <div class="tbord">

      <div class="casem">
        <div class="descriptd">COMPTE
          <div class="descriptm"><?=number_format($totalcomptegnf+$totalcomptebanquegnf,0,',',' ') ; ?></div>
        </div>
      </div>

      <div class="descripts">+</div><?php

      $totalstock=0;
      if (isset($_POST['lieuIvent']) and $_POST['lieuIvent']=='general') {
        
        $prodstock= $DB->query("SELECT *FROM stock order by(nomstock)"); 

      }elseif (isset($_POST['lieuIvent']) and $_POST['lieuIvent']!='general') {

        $prodstock= $DB->query("SELECT *FROM stock where lieuvente='{$_POST['lieuIvent']}' "); 
      }else{

        $prodstock= $DB->query("SELECT *FROM stock WHERE lieuvente='{$_SESSION['lieuvente']}' "); 
      }
      
      foreach ($prodstock as $values) {?>
        <div class="casem">
          <div class="descriptd">Stock <?=$values->nomstock;?>
            <div class="descriptm"><?=number_format($panier->dispoStock($values->nombdd, 0)[0],0,',',' ');?></div>
          </div>
        </div>

        <?php

        $totalstock+=$panier->dispoStock($values->nombdd, 0)[0];
      }

      $solde=$totalcreancegnf+$totaldettegnf; ?>

      <div class="descripts"><?=$panier->colorSigne($solde)[1];?></div>

      <div class="casem">
        <div class="descriptd">SOLDE CREDITS
          <div class="descriptm" style="background-color: <?=$panier->color($solde);?>"><?=number_format($panier->colorSigne($solde)[2]*$solde,0,',',' '); ?></div>
        </div>
      </div>

      <div class="descripts">-</div>

      <div class="casem"><?php $perte=$panier->perteSolde(0, 0)[0];?>
        <div class="descriptd">PERTES
          <div class="descriptm" style="background-color: <?=$panier->color($perte);?>"><?= number_format($perte,0,',',' '); ?></div>
        </div>
      </div>

      <div class="descripts">+</div>

      <div class="casem"><?php $gain=$panier->gainSolde(0, 0)[0];?>
        <div class="descriptd">GAIN DEVISE
          <div class="descriptm" style="background-color: <?=$panier->color(-$gain);?>"><?= number_format($gain,0,',',' '); ?></div>
        </div>
      </div>      
      

    </div>
    <div class="descripts">| |</div>

    <div class="tbord" style="display: flex; margin: auto; margin-top: 20px;"><?php 
      $anneeinv=date('Y');

      if (!empty($panier->inventaire($anneeinv, 0)[0])) {?>

        <div class="casem" style="margin-top: 0px; margin-left: 10px;">
          <div class="descriptd">SOLDE COMPTE <?= date("Y")-1;?></br>
            <div class="descriptm" style="background-color: green;"><?=number_format($panier->inventaire($anneeinv, 0)[0],0,',',' ');?></div>
          </div>
        </div><?php 

      }else{?>

        <div class="casem" style="margin-top: 0px;">
          <div class="descriptd">SOLDE COMPTE <?= date("Y")-1;?></br>
            <div class="descriptm" style="background-color: green;">INCONNU</div>
          </div>
        </div><?php 
      }

      $chiffrea=$totalstock+$totalcomptegnf+$totalcomptebanquegnf-$solde+$perte+$gain;

      $situation=$chiffrea-$panier->inventaire($anneeinv, 0)[0];?>

      <div class="casem">

        <div class="descriptd">SOLDE COMPTE <?= date("Y");?>
          <div class="descriptm" style="background-color: <?=$panier->color(-$chiffrea);?>"><?=number_format($chiffrea,0,',',' ') ; ?></div>
        </div>

      </div>

      <div class="casem"><?php 
        if ($chiffrea>0) {
          $zakat=((2.50*$chiffrea)/100);
        }else{
          $zakat=0;
        }?>

        <div class="descriptd">ZAKÄT(2,50%) <?= date("Y");?>
          <div class="descriptm" style="background-color: <?=$panier->color(-$chiffrea);?>"><?=number_format($zakat,0,',',' ') ; ?></div>
        </div>

      </div>

      <div class="casem">
        <div class="descriptd">DEPENSES
          <div class="descriptm"><?=number_format($panier->totdepense($_SESSION['date']),0,',',' ') ; ?></div>
        </div>
      </div>

    </div>
  </form>        

  <div class="tbord"><?php 
    if (!empty($panier->inventaire($anneeinv, 0)[0])) {?>

      <div class="casem" style="margin-left: 20px;"><?php          
        if (($situation)<0) {?>
          
          <div class="descriptd">PERTE NET <?= date("Y");?>
          <div class="descriptmbn"><?=number_format($situation,0,',',' ');?></div></div><?php

        }else{?>

          <div class="descriptd">BENEFICE NET <?= date("Y");?>
          <div class="descriptmbp"><?=number_format($situation,0,',',' ');?></div></div><?php
        }?>

      </div><?php 
    }else{?>

      <div class="casem" style="margin-left: 20px;">
          
          <div class="descriptd">BENEFICE <?= date("Y");?>
          <div class="descriptmbn">SOLDE 2021 INCONNU</div></div>

      </div><?php 
    }?>

    <div class="casem">

    <?php 
    if ($user['statut'] != 'superviseur') {?> <a href="inventairegeneral.php?cloture" onclick="return alerteV();"><div class="descriptd">CLOTURER L'ANNEE <?= date("Y");?>
      </div> </a> <?php } ?>
    </div>
  </div><?php 

  if (!isset($_GET['cloture'])) { 
    $bdd='inventaire';   

    $DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `dettegnf` double DEFAULT '0',
      `creancegnf` double DEFAULT '0',
      `comptegnf` double DEFAULT '0',
      `stock` double DEFAULT '0',
      `pertes` double DEFAULT '0',
      `gain` double DEFAULT '0',
      `solde` double DEFAULT '0',
      `depenses` double DEFAULT '0',
      `lieuvente` int(2) DEFAULT '1',
      `anneeinv` int(4) DEFAULT '0',
      `dateop` date DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ");

    $depenses=$panier->totdepense($_SESSION['date']);
    $annee=date('Y');

    $DB->delete("DELETE from inventaire where anneeinv='{$annee}'");

    $comtetotal=($totalcomptegnf+$totalcomptebanquegnf);

    $DB->insert('INSERT INTO inventaire(dettegnf, creancegnf, comptegnf, stock, pertes, gain, solde, depenses, anneeinv, dateop) values(?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array($totaldettegnf, -$totalcreancegnf, $comtetotal, $totalstock, $perte, $gain, $chiffrea, $depenses, $annee));    
  }?>

  <script type="text/javascript">
    function alerteS(){
        return(confirm('Valider la suppression'));
    }

    function alerteV(){
        return(confirm('Confirmez la cloture de cette année'));
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