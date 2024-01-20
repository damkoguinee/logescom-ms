<?php $adress = $DB->querys("SELECT * FROM adresse where lieuvente='{$_SESSION['lieuvente']}'");

  $total = 0;
  $total_tva = 0; ?>

  <div class="ticket">

    <table style="margin:auto; text-align: center;color: black; background: white;" >

      <tr>
        <th style="font-weight: bold; font-size: 22px; padding: 5px"><?php 

        if ($adress['nom_mag']=='SOGUICOM SARLU') {?>
          <img src="css/img/logo.jpg" width="300" height="80"><?php

        }elseif ($adress['initial']=='um') {?>
          <img src="css/img/logo.jpg" width="550" height="80"><?php

        }elseif ($adress['initial']=='sog') {?>
          <img src="css/img/logo.jpg" width="650" height="80"><?php

      }elseif ($adress['initial']=='mam') {?>
          <img src="css/img/logo.jpg" width="180" height="90"><?php

      }elseif ($adress['initial']=='dko') {?>
          <img src="css/img/logo.jpg" width="180" height="90"><?php

      }elseif ($adress['initial']=='kmco') {?>
        <img src="css/img/logo.jpg" width="380" height="100"><?php

      }elseif ($adress['initial']=='osa') {?>
          <img src="css/img/logo.jpg" width="180" height="120"><?php

      }elseif ($adress['initial']=='asc') {?>

          <img src="css/img/logo.jpg" width="0" height="0"><?php echo $adress['nom_mag'];

      }elseif ($adress['initial']=='chel') {?>

          <img src="css/img/logo.jpg" width="180" height="100"><?php echo $adress['nom_mag'];

      }elseif ($adress['initial']=='kbt') {?>
          <img src="css/img/logo.jpg" width="250" height="100"><?php

      }elseif ($adress['initial']=='afb') {?>
        <img src="css/img/logo.jpg" width="400" height="80"><?php

      }elseif ($adress['initial']=='ibd') {?>
        <img src="css/img/logo.jpg" width="500" height="70"><?php

      }elseif ($adress['initial']=='oum') {?>
        <img src="css/img/logo.jpg" width="200" height="120"><?php

      }elseif ($adress['initial']=='kla') {?>
        <img src="css/img/logo.jpg" width="150" height="140"><?php echo $adress['nom_mag'];

      }else{?>
        <img src="css/img/logo.jpg" width="0" height="0"><?php echo $adress['nom_mag'];
      }?></th>
      </tr>

      <tr><td style="font-size: 14px;"><?=ucwords(strtolower($adress['type_mag'])); ?></td></tr>

      <tr><td style="font-size: 14px;"><?=ucwords(strtolower($adress['adresse'])); ?><br/> <br/></td></tr>

    </table>