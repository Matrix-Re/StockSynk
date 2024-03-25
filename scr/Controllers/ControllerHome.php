<?php

require_once './Controllers/Controller.php';
require_once './Models/ModelConnexion.php';

/**
 * Class ControllerHome
 *
 * This class extends the Controller class and provides methods for managing the home page.
 */
class ControllerHome extends Controller
{
    /**
     * ControllerHome constructor.
     *
     * Starts the session, manages the connection, and handles actions.
     */
     public function __construct()
     {
          session_start();
          ManageConnetion();
          if (!isset($_POST['ActionAjax'])) {
               // On inclut les models
               require_once './Models/ModelCatalogue.php';
               require_once './Models/ModelQRCode.php';
               require_once './Models/ModelMagasin.php';
               require_once './Models/ModelVente.php';

               // On execute les actions
               $this->Action();

               // On récupère les données
               $Catalogue = listCatalogue($_SESSION['Connexion']->__get("ID_Store"));
               $ContratSalarie = $_SESSION['Connexion']->listContratSalarie();
               $listQRCode = listQRCode($_SESSION['Connexion']->__get("ID_Store"));
               $listVente = listVente($_SESSION['Connexion']->__get("ID_Store"));

               // On compact les données
               $Data = compact("Catalogue", "ContratSalarie", "listQRCode","listVente");

               // On appelle la vue
               $this->Render('Home', 'Accueil', $Data);
          } else {
               $this->ActionJQuery();
          }
     }

    /**
     * Handles AJAX actions.
     *
     * Depending on the action, it displays a popup, validates a catalogue, changes the state of a catalogue, updates a catalogue table, etc.
     */
     private static function ActionJQuery()
     {
          $Data = [''];
          switch ($_POST['ActionAjax']) {
               case 'AfficherCatalogue':
                    if (!empty($_POST['ID_Catalogue'])) {
                         require './Models/ModelCatalogue.php';
                         $Catalogue = new catalogue($_POST['ID_Catalogue']);
                         $Data = compact("Catalogue");
                    }
                    self::DisplayPopup("PopupCatalogue", $Data);
                    break;

               case 'ValiderCatalogue':
                    require './Models/ModelCatalogue.php';
                    if (!empty($_POST['Nom']) && !empty($_POST['UrlDescription']) && !empty($_POST['PrixReference']) && !empty($_POST['Quantite'])) {

                         $Catalogue = new catalogue($_POST['ID_Catalogue']);

                         $Catalogue->__set("Nom", $_POST['Nom']);
                         $Catalogue->__set("UrlDescription", $_POST['UrlDescription']);
                         $Catalogue->__set("PrixReference", $_POST['PrixReference']);
                         $Catalogue->__set("Quantite", $_POST['Quantite']);

                         $Catalogue->Enregister();
                    } else {
                         self::Message("Erreur", "Veuillez remplir tout les champs");
                    }

                    break;
               case 'ChangeEtatCatalogue':
                    require './Models/ModelCatalogue.php';
                    if (!empty($_POST['ID_Catalogue'])) {
                         $Catalogue = new catalogue($_POST['ID_Catalogue']);
                         $Catalogue->ChangeEtat();
                    } else {
                         self::Message("Erreur", "Le Catalogue n'a pas était définit");
                    }
                    break;
               case 'MiseAJourTableauCatalogue':
                    require_once './Models/ModelCatalogue.php';
                    $Catalogue = listCatalogue($_SESSION['Connexion']->__get("ID_Store"));
                    require "./Views/Champs/Tableau/ListCatalogue.php";
                    break;
//
               case 'AfficherQRCode':
                    $QRCode = null;
                    require_once './Models/ModelCatalogue.php';
                    $Catalogue = listCatalogue($_SESSION['Connexion']->__get("ID_Store"));                    
                    if (!empty($_POST['ID_QRCode'])) {
                         require './Models/ModelQRCode.php';                         
                         $QRCode = new qrcodeimage($_POST['ID_QRCode']);                         
                    }
                    $Data = compact("QRCode", "Catalogue");
                    self::DisplayPopup("PopupQRCode", $Data);
                    break;
               case 'ValiderQRCode':
                    require './Models/ModelQRCode.php';

                    if (!empty($_POST['Nom']) && !empty($_SESSION['Connexion']->__get("ID_Store") && !empty($_POST['ID_Catalogue']))) {

                         $QRCode = new qrcodeimage($_POST['ID_QRCode']);

                         $QRCode->__set("Nom", $_POST['Nom']);
                         $QRCode->__set("ID_Catalogue", $_POST['ID_Catalogue']);
                         $QRCode->__set("Actif", filter_var($_POST['Actif'], FILTER_VALIDATE_BOOLEAN));

                         $QRCode->Save();
                    } else {
                         self::Message("Erreur", "Veuillez remplir tout les champs");
                    }

                    break;
               case 'ChangeEtatQRCode':
                    require './Models/ModelQRCode.php';
                    if (!empty($_POST['ID_QRCode'])) {
                         $QRCode = new qrcodeimage($_POST['ID_QRCode']);
                         $QRCode->ChangeEtat();
                    } else {
                         self::Message("Erreur", "Le QRCode n'a pas était définit");
                    }
                    break;
               case 'MiseAJourTableauQRCode':
                    require_once './Models/ModelQRCode.php';
                    require_once './Models/ModelCatalogue.php';
                    $listQRCode = listQRCode($_SESSION['Connexion']->__get("ID_Store"));
                    require "./Views/Champs/Tableau/ListQRCode.php";
                    break;                        
//
               case 'MiseAJourTableauVente':
                    require_once './Models/ModelVente.php';
                    $listVente = listVente($_SESSION['Connexion']->__get("ID_Store"));
                    require "Views/Champs/Tableau/ListVente.php"; 
                    break;
               case 'DetailsVente':
                    if (isset($_POST['ID_Vente'])) {
                         require_once './Models/ModelVente.php';
                         $Vente = new vente($_SESSION['Connexion']->__get("ID_Store"),$_POST['ID_Vente']);
                         $Vente->DetailVente();
                         $listArticleVente = $Vente->__get('Panier');
                         $Data = compact("listArticleVente");
                         self::DisplayPopup("PopupDetailsVente",$Data);
                    }                
                    break;
//                    
               case 'MiseAJourTableauScan':
                    require_once './Views/Champs/Tableau/ListScan.php';
                    break;
          }
     }

    /**
     * Handles non-AJAX actions.
     *
     * If the logout action is set and the connection is established, it logs out the user.
     */
     private static function Action()
     {
          // Action
          // Déconnexion de l'application
          if (isset($_POST['Deconnexion']) && isset($_SESSION['Connexion'])) {
               $_SESSION['Connexion']->Logout();
          }
     }
}
