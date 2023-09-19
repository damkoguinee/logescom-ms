<?php

  $pers1=$DB->querys('SELECT *from login where id=:type', array('type'=>$_SESSION['idpseudo']));?>

  <div style="margin-top:20px;">

  <div  style="margin-top: 20px; color: grey;">
    <label style="margin-left: 20px; font-style: italic;">Emetteur</label>

    <label style="margin-left: 250px;">Réception</label>

    <label style="margin-left: 150px;">Client</label>
  </div>
 

  
</div>

   
</page>