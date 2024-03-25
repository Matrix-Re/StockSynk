<?php

require_once './Controllers/Controller.php';
require_once './Models/ModelConnexion.php';

/**
 * Class ControllerAdmin
 *
 * This class extends the Controller class and provides methods for managing the admin panel.
 */
class ControllerAdmin extends Controller
{

    /**
     * ControllerAdmin constructor.
     *
     * Starts the session, manages the connection, checks if the user is an admin, and handles actions.
     */
     public function __construct()
     {
          session_start();
          ManageConnetion();
          IsAdmin();
          if (!isset($_POST['ActionAjax'])) {
               // On inclut les models
               require_once './Models/ModelMagasin.php';
               require_once './Models/ModelSalarie.php';

               // On execute les actions
               $this->Action();

               // On récupère les données
               $ContratSalarie = $_SESSION['Connexion']->listContratSalarie();
               $listMagasin = listMagasin();
               $listSalarie = listSalarie();

               // On compact les données
               $Data = compact("ContratSalarie", "listMagasin", "listSalarie");

               // On appelle la vue
               $this->Render('Admin', 'Administration', $Data);
          } else {
               $this->ActionJQuery();
          }
     }

    /**
     * Handles AJAX actions.
     */
     private static function ActionJQuery()
     {
          $Data = [''];
          switch ($_POST['ActionAjax']) {
               case 'AfficherMagasin':
                    if (!empty($_POST['ID_Magasin'])) {
                         require_once './Models/ModelMagasin.php';
                         $Magasin = new Magasin($_POST['ID_Magasin']);
                         $Data = compact("Magasin");
                    }
                    self::DisplayPopup("PopupMagasin", $Data);
                    break;
               case 'ValiderMagasin':
                    require_once './Models/ModelMagasin.php';
                    if (!empty($_POST['Nom']) && !empty($_POST['CP']) && !empty($_POST['Ville'])) {

                         $Magasin = new magasin($_POST['ID_Magasin']);

                         $Magasin->__set("Nom", $_POST['Nom']);
                         $Magasin->__set("CP", $_POST['CP']);
                         $Magasin->__set("Ville", $_POST['Ville']);

                         $Magasin->Enregister();
                    } else {
                         self::Message("Erreur", "Veuillez remplir tout les champs");
                    }
                    break;
               case 'ChangeEtatMagasin':
                    require_once './Models/ModelMagasin.php';
                    if (!empty($_POST['ID_Magasin'])) {
                         $Magasin = new magasin($_POST['ID_Magasin']);
                         $Magasin->ChangeEtat();
                    } else {
                         self::Message("Erreur", "Le Magasin n'a pas était définit");
                    }
                    break;
               case 'MiseAJourTableauMagasin':
                    require_once './Models/ModelMagasin.php';
                    $listMagasin = listMagasin();
                    require "./Views/Champs/Tableau/ListMagasin.php";
                    break;
//
               case 'AfficherSalarie':
                    $Salarie = null;
                    require_once './Models/ModelMagasin.php';
                    $listMagasin = listMagasin();
                    if (!empty($_POST['ID_Salarie'])) {                         
                         require_once './Models/ModelSalarie.php';                         
                         $Salarie = new Salarie($_POST['ID_Salarie']);                         
                    }
                    $Data = compact("Salarie","listMagasin");
                    self::DisplayPopup("PopupSalarie", $Data);
                    break;               
               case 'ChangeEtatSalarie':
                    require_once './Models/ModelSalarie.php';
                    if (!empty($_POST['ID_Salarie'])) {
                         $Salarie = new salarie($_POST['ID_Salarie']);
                         $Salarie->ChangeEtat();
                    } else {
                         self::Message("Erreur", "Le salarie n'a pas était définit");
                    }
                    break;
               case 'MiseAJourTableauSalarie':
                    require_once './Models/ModelSalarie.php';
                    $listSalarie = listSalarie();
                    require "./Views/Champs/Tableau/ListSalarie.php";
                    break;
          }
     }

    /**
     * Handles non-AJAX actions.
     */
     private static function Action()
     {
          // Action
          // Déconnexion de l'application
          if (isset($_POST['Deconnexion']) && isset($_SESSION['Connexion'])) {
               $_SESSION['Connexion']->Logout();
          }     
          // On valide la information saisie dans la popup
          if (isset($_POST['ButtonValiderSalarie'])) {
               if (!empty($_POST['Identifiant']) && !empty($_POST['Store']) && !empty($_POST['Status'])) {

                    $Salarie = new salarie($_POST['ButtonValiderSalarie']);
          
                    if (!empty($_POST['Password'])) {
                         $Salarie->__set("Password", password_hash($_POST['Password'], PASSWORD_DEFAULT));
                    }
          
                    $Salarie->__set("Identifiant", $_POST['Identifiant']);
                    $Salarie->__set("Status", $_POST['Status']);
          
                    $Salarie->Enregister();
               } else {
                    self::Message("Erreur","Veuillez remplir tout les champs");
               }
          }
          
     }
}
