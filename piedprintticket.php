<?php

  $pers1=$DB->querys('SELECT *from login where id=:type', array('type'=>$payement['vendeur']));

  if ($_SESSION['init']=='oum') {?>

    <div style="margin-top:5px;"> 
    

      <div  style="margin-top: 0px; color: grey;">
        <label style="margin-left: 90px;">Responsable</label>

        <label style="margin-left: 290px;">Le Client</label>
      </div>

      <div class="pied" style="margin-top: 10px; color: grey;">
        <label style="margin-left: 100px;"><img src="css/img/signature.jpg" width="130" height="80"></label>

        <label style="margin-left: 230px;"></label>
      </div>

      <div class="pied" style="margin-top: 0px; color: grey;">
        <label style="margin-left: 60px; font-style: italic;">DIALLO Mamadou Oury</label>

        <label style="margin-left: 230px; font-style: italic;"><?=$panier->adClient($_SESSION['reclient'])[0]; ?></label>
      </div>
    </div><?php
     # code...
  }else{?>

    <div style="margin-top:5px;"> 
    

      <div  style="margin-top: 0px; color: grey;">
        <label style="margin-left: 90px;"><?=strtoupper($pers1['statut']);?></label>

        <label style="margin-left: 270px;">Le Client</label>
      </div><?php 

        if ($adress['nom_mag']!='SOGUICOM SARLU') {?>

          <div class="pied" style="margin-top: 90px; color: grey;">
            <label style="margin-left: 80px;"><?=ucwords($pers1['nom']);?></label>

            <label style="margin-left: 250px;"><?=$panier->adClient($_SESSION['reclient'])[0]; ?></label>
          </div><?php 
        }?>
    </div><?php 
  }?>
</page>