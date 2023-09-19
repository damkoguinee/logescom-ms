<?php
if ($_SESSION['init']=='oum') {?>

  <div style="margin-top:5px;">    

    <div  style="margin-top: 0px; color: grey;">
      <label style="margin-left: 20px;">Responsable</label>
      <label style="margin-left: 200px;">Transporteur</label>
      <label style="margin-left: 140px;">Le Client</label>
    </div>

    <div class="pied" style="margin-top: 10px; color: grey;">
      <label style="margin-left: 30px;"><img src="css/img/signature.jpg" width="130" height="80"></label>

      <label style="margin-left: 30px;"></label>
    </div>

    <div class="pied" style="margin-top: 0px; color: grey;">
    </div>

    <div class="pied" style="margin-top: 0px; color: grey;">
      <label style="margin-left: 10px; font-style: italic;">DIALLO Mamadou Oury</label>
      <label style="margin-left: 350px; font-style: italic;"><?=$panier->adClient($_SESSION['reclient'])[0]; ?></label>
    </div>
  </div><?php 
}else{

  $pers1=$DB->querys('SELECT *from login where id=:type', array('type'=>$_SESSION['idpseudo']));?>

  <div style="margin-top:20px;">

    <div  style="margin-top: 20px; color: grey;">
      <label style="margin-left: 20px; font-style: italic;"><?=ucwords($pers1['statut']);?></label>

      <label style="margin-left: 150px;">Transporteur</label>

      <label style="margin-left: 100px;">Le Client</label>
    </div>
  

    <div class="pied" style="margin-top: 80px; color: grey;">
      <label style="margin-left: 10px;"><?=ucwords($pers1['nom']);?></label>

      <label style="margin-left: 50px;"></label>

      <label style="margin-left: 280px;"><?=$panier->adClient($_SESSION['reclient'])[0]; ?></label>
    </div>
  </div><?php

}?>

   
</page>