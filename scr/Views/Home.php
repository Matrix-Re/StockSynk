<?php require './Views/Champs/Header.php'; ?>

<div class="Content">
     <!-- PARTIE CATALOGUE -->
     <br>
     <h2>Liste catalogue :</h2>
     <?php
     require './Views/Champs/Bouton/BtnAjouterCatalogue.php';
     require './Views/Champs/Filtre/FiltreCatalogue.php';
     ?>
     <div id="TableauCatalogue">
          <?php require './Views/Champs/Tableau/ListCatalogue.php'; ?>
     </div>

     <!-- PARTIE QRCODE -->
     <br>
     <h2>Liste des Qrcode :</h2>
     <?php
     require './Views/Champs/Bouton/BtnAjouterQRCode.php';
     require './Views/Champs/Filtre/FiltreQRCode.php';
     ?>
     <div id="TableauQRCode">
          <?php require './Views/Champs/Tableau/ListQRCode.php'; ?>
     </div>

     <!-- PARTIE SCAN -->
     <?php if (isset($_SESSION['Connexion'])) {
          if ($_SESSION['Connexion']->Status == 'Administrateur' || $_SESSION['Connexion']->Status == 'Direction') { ?>
               <br>
               <h2>Liste des Scans :</h2>
               <?php require './Views/Champs/Filtre/FiltreScan.php'; ?>
               <div id="TableauScan">
                    <?php require './Views/Champs/Tableau/ListScan.php'; ?>
               </div>
     <?php }
     } ?>

     <!-- PARTIE VENTE -->
     <br>
     <h2>Liste des ventes :</h2>
     <?php
     require './Views/Champs/Filtre/FiltreVente.php';
     ?>
     <div id="TableauVente">
          <?php require './Views/Champs/Tableau/ListVente.php'; ?>
     </div>

</div>

<?php require './Views/Champs/Footer.php'; ?>