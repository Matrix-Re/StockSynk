<?php require './Views/Champs/Header.php'; ?>

<div class="Content">

     <!-- PARTIE MAGASIN -->
     <?php 
     require './Views/Champs/Bouton/BtnAjouterMagasin.php'; 
     require './Views/Champs/Filtre/FiltreMagasin.php';
     ?>
     <div id="TableauMagasin">
          <?php require './Views/Champs/Tableau/ListMagasin.php'; ?>
     </div> 

     <!-- PARTIE SALARIE -->
     <?php 
     require './Views/Champs/Bouton/BtnAjouterSalarie.php'; 
     require './Views/Champs/Filtre/FiltreSalarie.php';
     ?>
     <div id="TableauSalarie">
          <?php require './Views/Champs/Tableau/ListSalarie.php'; ?>
     </div> 

</div>

<?php require './Views/Champs/Footer.php'; ?>