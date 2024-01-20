<fieldset style="margin-top: 10px;"><legend>Voulez-vous</legend>
  <div class="choixg">
    <div class="optiong">
      <a href="livraisonachat.php?nonlivre"><div class="descript_optiong">Achats non Livrés</div></a>
    </div>

    <div class="optiong">
      <a href="livraisonachat.php?livre"><div class="descript_optiong">Achats Livrés</div></a>
    </div>
    <?php 
    if ($user['statut'] != 'superviseur') {?>
      <div class="optiong">
          <a href="index.php?indexr"><div class="descript_optiong">Allez dans vente</div></a>
      </div> <?php 
    }?>
  </div>
</fieldset>