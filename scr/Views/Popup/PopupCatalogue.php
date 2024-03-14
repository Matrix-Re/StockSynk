<?php 
// Initialisation
$ID_Catalogue = 0;
$NomCatalogue = "";
$UrlDescription = "";
$PrixRéférence = "";
$Quantite = "";
$typeEdition = "'AddCatalogue'";
$LibelléEdition = "Ajouter";

// Traitement des données
if (isset($Catalogue)) {
     $ID_Catalogue = $Catalogue->__get("ID_Catalogue");
     $NomCatalogue = $Catalogue->__get("Nom");
     $UrlDescription = $Catalogue->__get("UrlDescription");
     $PrixRéférence = $Catalogue->__get("PrixReference");
     $Quantite = $Catalogue->__get("Quantite");
     $typeEdition = "'EditCatalogue'";
     $LibelléEdition = "Modifier";
}

?>

<button class="btn btn-danger" id="ClosePopup">X</button>
<h1>Catalogue</h1>
<input type="text"  class="form-control" id="Nom"            placeholder="Nom catalogue"   value="<?= $NomCatalogue ?>">
<input type="url"   class="form-control" id="UrlDescription" placeholder="URL catalogue"   value="<?= $UrlDescription ?>">
<input type="text"  class="form-control" id="PrixReference"  placeholder="Prix reference"  value="<?= $PrixRéférence ?>">
<input type="text"  class="form-control" id="Quantite"       placeholder="Quantité"        value="<?= $Quantite ?>" >
<button type="submit" value="<?= $ID_Catalogue ?>" class="btn btn-success d-block mx-auto btn-submit" id="ButtonValiderCatalogue"><?= $LibelléEdition ?></button>
