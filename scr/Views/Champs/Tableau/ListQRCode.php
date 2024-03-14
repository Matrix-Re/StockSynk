<table class="text-center table">
     <thead>
          <tr>
               <th>ID QRCode</th>
               <th>Nom QRCode</th>
               <th>Nom Catalogue</th>
               <th>Actif</th>
               <th>QRCode</th>
               <th>Modifier</th>
               <th>Etat</th>
          </tr>
     </thead>
     <tbody>
          <?php if(isset($listQRCode)) {
               foreach ($listQRCode as $qrcode) { 
                    $Catalogue = new catalogue($qrcode->__get("ID_Catalogue")) ?>

                    <tr>
                         <th><?= $qrcode->__get("ID_QRCode") ?></th>
                         <th><?= $qrcode->__get("Nom") ?></th>
                         <th><?= $Catalogue->__get("Nom") ?></th>
                         <th><?php if($qrcode->__get("Actif") == 1){echo "vrai";}else{echo "faux";} ?></th>
                         <td><a href="<?= $qrcode->GenererQRCode(); ?>" target="_blank">Générer</a></td>
                         <td><button type="submit" class="ButtonAfficherQRCode" value="<?= $qrcode->__get("ID_QRCode") ?>">Modifier</button></td>
                         <td><button type="submit" class="ButtonEtatQRCode" value="<?= $qrcode->__get("ID_QRCode") ?>"><?php if($qrcode->__get('Actif')){echo "Rendre Inactif";}else{echo "Rendre Actif";} ?></button></td>
                    </tr>

          <?php }} ?>
     </tbody>
</table>