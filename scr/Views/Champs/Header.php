<?php

$Identifiant = "";
$isAdmin = false;

if(isset($_SESSION['Connexion'])){
     $Identifiant = $_SESSION['Connexion']->Identifiant;
     if ($_SESSION['Connexion']->Status == "Administrateur") {
          $isAdmin = true;
     }     
}

?>
<header class="Header d-flex justify-content-center py-3">
     <ul class="nav nav-pills">

          <b class="UtilisateurConnecter"><?= "Utilisateur : " . $Identifiant; ?></b>

          <li class="nav-item">
               <?php require './Views/Champs/ComboBox/ComboListeMagasin.php'; ?>
          </li>
          <li class="nav-item">
               <?php require './Views/Champs/Bouton/BtnInterfaceAccueil.php'; ?>
          </li>
          <li class="nav-item">
               <?php require './Views/Champs/Bouton/BtnInterfaceVente.php'; ?>
          </li>
          <li class="nav-item">
               <?php if ($isAdmin) {
                    require './Views/Champs/Bouton/BtnInterfaceAdminstration.php';
               } ?>
          </li>
          <li class="nav-item">
               <?php require './Views/Champs/Bouton/BtnDeconnexion.php'; ?>
          </li>

     </ul>
</header>