<?php
// Initialisation
$ID_CatalogueScan = 0;
$Quantite = 0;
$PrixUnitaire = 0;

// Traitement des données
if (isset($ProduitPanier)) {
     $ID_CatalogueScan = $ProduitPanier->__get('ID_Catalogue');
     $Quantite = $ProduitPanier->__get('Quantite');
     $PrixUnitaire = $ProduitPanier->__get('PU');
}

if (!empty($qrcode)) {
     //On préselection le produit associé au QRCode
     $ID_CatalogueScan = $qrcode->__get("ID_Catalogue");
     $Catalogue = new catalogue($ID_CatalogueScan);
     $PrixUnitaire = $Catalogue->__get("PrixReference");     
}

?>
<select id="SelectionCatalogue" class="form-select">
     <option value="">Selectionnez un produit</option>
     <?php foreach ($listCatalogue as $produit) {
          // Initialisation
          $selected = "";
          $ID_Catalogue = $produit->__get("ID_Catalogue");
          $Nom = $produit->__get("Nom");
          $PrixRef = $produit->__get("PrixReference");
          $QuantiteStock = $produit->__get("Quantite");

          // Traitement des données
          if ($ID_CatalogueScan == $ID_Catalogue) {
               if ($produit->__get("Actif")) {
                    $selected = "selected";
               }
          }
     ?>
          <option value="<?= $ID_Catalogue ?>" <?= $selected; ?>> <?= $Nom . " - " . $PrixRef . "€ - " . $QuantiteStock?>
          <?php } ?>
</select><br>

<label>Quantité : </label>
<input type="number" min="1" id="Quantite" value="<?= $Quantite ?>"><br>

<label>Prix unitaire : </label>
<input type="number" id="Prix" step="1" value="<?= $PrixUnitaire ?>"><br>

<label id="PrixTotal">Prix total : <?= $PrixUnitaire * $Quantite ?> €</label><br>

<button id="ButtonValiderProduit" class="btn btn-primary">Ajouter au panier</button>