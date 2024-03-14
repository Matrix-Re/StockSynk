<?php
// Initialisation
$ID_QRCode = 0;
$NomQRCode = "";
$Etat = "";
$ID_Catalogue = "";
$typeEdition = "'AddQRCode'";
$LibelléEdition = "Ajouter";

// Traitement des données
if (isset($QRCode)) {
     if ($QRCode->__get('Actif')) $Etat = "checked";
     $ID_QRCode = $QRCode->__get("ID_QRCode");
     $NomQRCode = $QRCode->__get("Nom");
     $ID_Catalogue = $QRCode->__get("ID_Catalogue");
     $typeEdition = "'EditQRCode'";
     $LibelléEdition = "Modifier";
}

?>

<button id="ClosePopup">X</button>
<h1>QRCode</h1>
<input type="text" id="Nom" placeholder="Nom QRCode" value="<?= $NomQRCode ?>">
<input type="checkbox" id="Actif" placeholder="QRCode Actif" <?= $Etat ?>>

<select id="ID_Catalogue" class="form-select">
     <?php foreach ($Catalogue as $produit) {
          $checked = "";
          if (isset($QRCode)) {
               if ($ID_Catalogue == $produit->__get("ID_Catalogue")) {
                    $checked = "selected";
               }
          }
     ?>
          <option value="<?= $produit->__get("ID_Catalogue") ?>" <?= $checked ?>> <?= $produit->__get("Nom") ?></option>
     <?php } ?>
</select>

<button type="submit" class="btn btn-success d-block mx-auto btn-submit" id="ButtonValiderQRCode" value="<?= $ID_QRCode ?>"><?= $LibelléEdition ?></button>