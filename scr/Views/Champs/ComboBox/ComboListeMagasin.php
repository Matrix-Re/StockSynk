<form method="POST">
     <select name="ChangeStore" onchange="this.form.submit()" class="form-select">

          <?php 
          if (!empty($ContratSalarie)) {
               foreach ($ContratSalarie as $magasin) { 
                    $Selected = "";
                    if ($_SESSION['Connexion']->__get("ID_Store") == $magasin->__get('ID_Magasin')) {
                         $Selected = "selected=\"selected\"";
                    }                    
                    ?>
               
                    <option value="<?= $magasin->__get('ID_Magasin') ?>" <?= $Selected ?> ><?= $magasin->__get('Nom') ?></option>
          <?php }} ?>          

     </select>
</form>