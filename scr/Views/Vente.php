<?php require './Views/Champs/Header.php'; ?>

<div class="Content">

     <div class="d-flex">

          <!-- PARTIE FORMULAIRE PANIER -->
          <div id="FormulaireVente">
               <?php if ($qrcode != null) { ?>
                    <a href="<?= $qrcode->GetURLCatalogue(); ?>" target="_blank">redirection vers la page du produit</a>
               <?php } ?>
               <?php require './Views/Champs/Formulaire/FormulaireVente.php'; ?>
          </div>
          <!-- PARTIE CONTENUE PANIER -->
          <div id="ContenuePanier" style="padding-left: 40%;">
               <?php require './Views/Champs/Tableau/ListPanier.php'; ?>
          </div>
     </div>

</div>

<?php require './Views/Champs/Footer.php'; ?>