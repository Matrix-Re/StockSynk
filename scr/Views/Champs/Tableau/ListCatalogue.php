<table class="text-center table">
     <thead class="thead-dark">
          <tr>
               <th>Nom Catalogue</th>
               <th>URL Catalogue</th>
               <th>Prix référence</th>
               <th>Quantité en stock</th>
               <th>Modifier</th>
               <th>Etat</th>
          </tr>
     </thead>
     <tbody>
          <?php if(!empty($Catalogue)){
               foreach ($Catalogue as $produit) { 
               preg_match('/https:\/\/?([^\/]+)/', $produit->__get("UrlDescription"), $matches);
               if (!empty($matches[1])){ $LienRacourcie = $matches[1]; }
               else{$LienRacourcie = $produit->__get("UrlDescription");}

               preg_match('/^(http:\/\/|https:\/\/)*/', $produit->__get("UrlDescription"), $matches);
               if (!empty($matches[1])){ $LienRacourcie = $matches[1]; }
               else{$Lien = "https://".$produit->__get("UrlDescription");}
               ?>
               <tr>
                    <th><?= $produit->__get("Nom") ?></th>
                    <th><a href="<?=$Lien?>" target="_blank"><?= $LienRacourcie ?></a></th>
                    <th><?= $produit->__get("PrixReference") ?> €</th>
                    <th><?= $produit->__get("Quantite") ?></th>
                    <td><button type="submit" class="ButtonAfficherCatalogue" value="<?= $produit->__get("ID_Catalogue") ?>">Modifier</button></td>
                    <td><button type="submit" class="ButtonEtatCatalogue" value="<?= $produit->__get("ID_Catalogue") ?>" ><?php if($produit->__get('Actif')){echo "Rendre Inactif";}else{echo "Rendre Actif";} ?></button></td>
               </tr>
          <?php }} ?>
     </tbody>
</table>