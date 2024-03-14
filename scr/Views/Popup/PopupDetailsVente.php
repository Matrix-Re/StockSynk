<?php
require_once './Models/ModelCatalogue.php';
?>
<button id="ClosePopup">X</button>
<table>
     <thead>
          <tr>
               <th>Nom Catalogue</th>
               <th>Quantite</th>
               <th>Prix référence</th>
               <th>Prix de vente</th>
               <th>Total</th>
          </tr>
     </thead>
     <tbody>
          <?php
          $prixTotal = 0;
          foreach ($listArticleVente as $Article) {
               $prixTotal += $Article->__get('PU') * $Article->__get('Quantite');
               $Catalogue = new catalogue($Article->__get('ID_Catalogue'));
               $Nom = $Catalogue->__get("Nom");
               $PrixReference = $Catalogue->__get("PrixReference");
               ?>
               <tr>
                    <td> <?= $Nom ?> </td>
                    <td> <?= $Article->__get('Quantite') ?> </td>
                    <td> <?= $PrixReference . "€" ?> </td>                    
                    <td> <?= $Article->__get('PU') . "€" ?> </td>
                    <td> <?= $Article->__get('PU') * $Article->__get('Quantite') . "€" ?> </td>
               <tr>
               <?php } ?>
     </tbody>
</table>
<b><?= "Prix Total : " . $prixTotal . " €" ?></b>