<?php require 'header.php';



$prod=$DB->query("SELECT DISTINCT nom_client, montant, date_versement
FROM versement t1
WHERE EXISTS (
              SELECT *
              FROM versement t2
              WHERE t1.id <> t2.id
              AND   t1.montant = t2.montant
              AND   t1.nom_client = t2.nom_client
              AND   t1.motif = t2.motif
              AND   t1.date_versement = t2.date_versement )");?>

<table class="payement">
    <thead>
        <tr>
            <th>Client</th>
            <th>Monatnt</th>
            <th>Date</th>
        </tr>
    </thead>

    <tbody><?php 
        foreach ($prod as $key => $value) {?>
            <tr>
                <td><?=$panier->nomClient($value->nom_client);?></td>
                <td style="text-align:center;"><?=number_format($value->montant,0,',',' ');?></td>
                <td><?=(new dateTime($value->date_versement))->format("d/m/Y");?></td>
            </tr><?php
            
        }?>

    </tbody>
</table>           
