<table class="text-center table">
     <thead>
          <tr>
               <th>Nom</th>
               <th>Département</th>
               <th>Ville</th>
               <th>Nombre Salarié</th>
               <th>Nombre Article</th>
               <th>Nombre QRCode</th>
               <th>Modifier</th>
               <th>rendre inactif</th>
          </tr>
     </thead>
     <tbody>
          <?php if(isset($listMagasin)) {foreach ($listMagasin as $Magasin) { ?>
               <tr>
                    <td> <?= $Magasin->__get('Nom'); ?>  </td>
                    <td> <?= $Magasin->__get('CP'); ?> </td>
                    <td> <?= $Magasin->__get('Ville'); ?> </td>
                    <td> <?= count($Magasin->listContratMagasin()); ?> </td>
                    <td> <?= $Magasin->ObtenirNombreArticle(); ?> </td>
                    <td> <?= $Magasin->ObtenirNombreQRCode(); ?> </td>
                    <td><button type="submit" class="ButtonAfficherMagasin" value="<?= $Magasin->__get("ID_Magasin") ?>">Modifier</button></td>
                         <td><button type="submit" class="ButtonEtatMagasin" value="<?= $Magasin->__get("ID_Magasin") ?>"><?php if($Magasin->__get('Actif')){echo "Rendre Inactif";}else{echo "Rendre Actif";} ?></button></td>
               <tr>
          <?php }} ?>
     </tbody>
</table>