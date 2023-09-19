<?php require 'header.php';



$prod=$DB->query("SELECT DISTINCT *
FROM decaissement t1
WHERE EXISTS (
              SELECT *
              FROM decaissement t2
              WHERE t1.id <> t2.id
              AND   t1.montant = t2.montant
              AND   t1.client = t2.client
              AND   t1.coment = t2.coment
              AND   t1.date_payement = t2.date_payement )");?>

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
                <td><?=$panier->nomClient($value->client);?></td>
                <td style="text-align:center;"><?=number_format($value->montant,0,',',' ');?></td>
                <td><?=(new dateTime($value->date_payement))->format("d/m/Y");?></td>
            </tr><?php
            
        }?>

    </tbody>
</table>           
