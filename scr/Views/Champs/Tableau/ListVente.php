<table class="text-center table">
     <thead>
          <tr>
               <th>ID Ventes</th>
               <th>Date</th>
               <th>Nombre de produit</th>
               <th>Prix total</th>
          </tr>
     </thead>
     <tbody>
          <?php if(isset($listVente)){ foreach ($listVente as $vente) { ?>

                    <tr class="AfficherDétailsVente" data-value="<?= $vente->__get('ID_Vente') ?>">
                         <th><?= $vente->__get("ID_Vente") ?></th>
                         <th><?= $vente->__get("DateVente") ?></th>
                         <th><?= $vente->__get("NbArticle") ?></th>
                         <th><?= $vente->__get("PrixTotal") ?></th>                         
                    </tr>

          <?php }} ?>
     </tbody>
</table>