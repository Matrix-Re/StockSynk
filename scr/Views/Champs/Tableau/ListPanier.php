<?php
require_once './Models/ModelCatalogue.php';
$indice = 0;
$PrixTotal = 0;
if (isset($_SESSION['newVente']))
     {
foreach ($_SESSION['newVente']->__get('Panier') as $produit) {
     $PrixTotal += $produit->__get('PU') * $produit->__get('Quantite');
     $Catalogue = new catalogue($produit->__get('ID_Catalogue'));
?>
     <b> <?= $Catalogue->__get('Nom') ?></b>
     <?= $produit->__get('PU') . "€" ?> 
     <?= "x". $produit->__get('Quantite') ?> 
     <button class="ButtonModifierProduitPanier btn btn-secondary" value="<?=$indice?>" >Modifier</button>
     <button class="ButtonSupprimerProduitPanier btn btn-danger" value="<?=$indice?>">Supprimer du panier</button>
     <br>

<?php 
     $indice++; }}
?>
<hr>
<h4>Prix total : <?= $PrixTotal ?> €</h4>
<form method="POST"><button type="submit" name="AjouterVente" class="btn btn-success">Vendre</button></form>