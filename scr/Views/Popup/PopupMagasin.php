<?php
// Initialisation
$ID_Magasin = 0;
$NomMagasin = "";
$CodePostal = "";
$Ville = "";
$typeEdition = "'AddMagasin'";
$LibelléEdition = "Ajouter";

// Traitement des données
if (isset($Magasin)) {
     $ID_Magasin = $Magasin->__get("ID_Magasin");
     $NomMagasin = $Magasin->__get("Nom");
     $CodePostal = $Magasin->__get("CP");
     $Ville = $Magasin->__get("Ville");
     $typeEdition = "'EditMagasin'";
     $LibelléEdition = "Modifier";
}

?>
<button id="ClosePopup">X</button>
<h1>Magasin</h1>
<input type="text" id="Nom" placeholder="Nom magasin" value="<?= $NomMagasin ?>">
<input type="text" id="CP" placeholder="Code postal magasin" maxlength=2 value="<?= $CodePostal ?>">
<input type="text" id="Ville" placeholder="Ville magasin" value="<?= $Ville ?>">
<button type="submit" value="<?= $ID_Magasin ?>" class="btn btn-success d-block mx-auto btn-submit" id="ButtonValiderMagasin"><?= $LibelléEdition ?></button>