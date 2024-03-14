<?php
// Initialisation
$ID_Salarie = 0;
$Identifiant = "";
$IndicationPassword = "mot de passe";
$Status = ["", "", ""];

// Traitement des données
if (isset($Salarie)) {
     $ContratSalarie = $Salarie->listContratSalarie();
     $ID_Salarie = $Salarie->__get("ID_Salarie");
     $Identifiant = $Salarie->__get("Identifiant");
     $IndicationPassword = "Vide si aucune modification";
     switch ($Salarie->__get("Status")) {
          case 'Employer':
               $Status[0] = "Checked";
               break;
          case 'Direction':
               $Status[1] = "Checked";
               break;
          case 'Administrateur':
               $Status[2] = "Checked";
               break;
     }
}

?>
<form method="POST">
     <button id="ClosePopup">X</button>
     <h1>Salarié</h1>
     <input type="text" name="Identifiant" placeholder="Identifiant" value="<?= $Identifiant ?>">
     <input type="text" name="Password" placeholder="<?= $IndicationPassword ?>">
     <div id="status">
          <label><input type="radio" name="Status" value="Employer" <?= $Status[0] ?>>Employer</label>
          <label><input type="radio" name="Status" value="Direction" <?= $Status[1] ?>>Direction</label>
          <label><input type="radio" name="Status" value="Administrateur" <?= $Status[2] ?>>Administrateur</label>
     </div>

     <div class="multiselect">
          <div class="selectBox">
               <select class="form-select">
                    <option>Select an option</option>
               </select>
               <div class="overSelect">
               </div>
          </div>
          <div id="checkboxes">
               <?php if (isset($listMagasin)) {
                    foreach ($listMagasin as $Magasin) {
                         $checked = "";

                         // Si c'est une modification on recheche le magasin dans la liste des contrats
                         if (isset($Salarie))
                              if (in_array($Magasin->__get('ID_Magasin'), $ContratSalarie))
                                   $checked = "checked";
               ?>
                         <label for="">
                              <input type="checkbox" name="Store[]" value="<?= $Magasin->__get('ID_Magasin') ?>" <?= $checked; ?>> <?= $Magasin->__get('Nom') ?>
                         </label>
               <?php }
               } ?>
          </div>
     </div>

     <button type="submit" class="btn btn-success d-block mx-auto btn-submit" name="ButtonValiderSalarie" value="<?= $ID_Salarie ?>"><?= 1 ?></button>
</form>