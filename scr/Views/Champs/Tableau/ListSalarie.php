<table class="text-center table">
     <thead>
          <tr>
               <th>id</th>
               <th>user</th>
               <th>Status</th>
               <th>Nombre de Magasin pour lequel il travaille</th>
               <th>Modifier</th>
               <th>rendre inactif</th>
          </tr>
     </thead>
     <tbody>
          
          <?php if(isset($listSalarie)){ foreach ($listSalarie as $Salarie) { ?>
               <tr>
                    <td> <?= $Salarie->__get("ID_Salarie"); ?>  </td>
                    <td> <?= $Salarie->__get("Identifiant"); ?>  </td>
                    <td> <?= $Salarie->__get("Status"); ?> </td>
                    <td> <?= count($Salarie->listContratSalarie()); ?> </td>
                    <td><button type="submit" class="ButtonAfficherSalarie" value="<?= $Salarie->__get("ID_Salarie"); ?>">Modifier</button></td>
                    <td><button type="submit" class="ButtonEtatSalarie" value="<?= $Salarie->__get("ID_Salarie"); ?>"><?php if($Salarie->__get('Actif')){echo "Rendre Inactif";}else{echo "Rendre Actif";} ?></button></td>
               </tr>
          <?php }} ?>
          
     </tbody>
</table>