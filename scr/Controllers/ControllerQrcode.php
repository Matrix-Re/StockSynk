<?php

require_once './Controllers/Controller.php';
require_once './Models/ModelConnexion.php';
require_once './Models/ModelVente.php';

/**
 * Class ControllerQrcode
 *
 * This class extends the Controller class and provides methods for managing QR codes.
 */
class ControllerQrcode extends Controller
{
    /**
     * ControllerQrcode constructor.
     *
     * Starts the session, initializes a QR code, includes the necessary models, retrieves information, determines the user type (employee/client), and handles actions.
     */
     public function __construct()
     {
          session_start();
          //Initialisation
          $qrcode = null;
          // On inclut les models
          require_once './Models/ModelQRCode.php';
          // On récupère les informations
          global $paramterUrl;
          if (!empty($paramterUrl)) {
               if ($paramterUrl != "" && (int)$paramterUrl != 0) {
                    $qrcode = new qrcodeimage($paramterUrl);
               }
          }
          // On determine quel type d'utilisateur accède à la page (Salarie / Client)
          if (isset($_SESSION['Connexion'])) {
               ChangeStore();
               if (!isset($_POST['ActionAjax'])) {                    
                    // On inclut les models
                    require_once './Models/ModelCatalogue.php';                    

                    // On execute les actions
                    $this->Action();

                    // On récupère les données
                    $ContratSalarie = $_SESSION['Connexion']->listContratSalarie();
                    $listCatalogue = listCatalogue($_SESSION['Connexion']->__get("ID_Store"));

                    // On compact les données
                    $Data = compact("ContratSalarie", "listCatalogue", "qrcode");

                    // On appelle la vue
                    $this->Render('Vente', 'Vente', $Data);
               } else {
                    $this->ActionJQuery();
               }
          } else {
               $Data = compact("qrcode");
               $this->Render('Qrcode', 'Qrcode', $Data);
          }
     }

    /**
     * Handles AJAX actions.
     *
     * Depending on the action, it displays a cart, adds a product to the cart, modifies a product in the cart, deletes a product from the cart, or gets the price of a product.
     */
     private static function ActionJQuery()
     {
          $Data = [''];
          switch ($_POST['ActionAjax']) {
               case 'AfficherPanier':
                    require "./Views/Champs/Tableau/ListPanier.php";
                    break;
               case 'AjouterProduitPanier':
                    if (!empty($_POST['ID_Catalogue'] && $_POST['Prix'] && $_POST['Quantite'])) {
                         $_SESSION['newVente']->AddPanier($_POST['ID_Catalogue'], $_POST['Prix'], $_POST['Quantite']);
                    } else {
                         self::Message("Erreur", "Veuillez remplir tout les champs");
                    }
                    break;
               case 'ModifierProduitPanier':
                    if (isset($_POST['IndicePanier'])) {
                         $Produit = $_SESSION['newVente']->Panier[$_POST['IndicePanier']];

                         echo json_encode(array(
                              "ID_Catalogue" => $Produit->__get("ID_Catalogue"),
                              "PU" => $Produit->__get("PU"),
                              "Quantite" => $Produit->__get("Quantite"),
                         ));
                    } else {
                         Controller::Message("Erreur", "Indice du produit introuvable");
                    }
                    break;
               case 'SupprimerProduitPanier':
                    $_SESSION['newVente']->DeletePanier($_POST['IndicePanier']);
                    break;
               case 'GetPrixProduit':
                    require_once './Models/ModelCatalogue.php';
                    $Catalogue = new catalogue($_POST['ID_Catalogue']);
                    echo json_encode(array("PU" => $Catalogue->__get("PrixReference")));
                    break;
          }
     }

    /**
     * Handles non-AJAX actions.
     *
     * If the logout action is set and the connection is established, it logs out the user. If the new sale object does not exist in the session, it creates one. If the add sale action is set, it records the sale.
     */
     private static function Action()
     {
          // Action
          // Déconnexion de l'application
          if (isset($_POST['Deconnexion']) && isset($_SESSION['Connexion'])) {
               $_SESSION['Connexion']->Logout();
          }
          // On crée l'objet vente s'il n'existe pas dans la session
          if (empty($_SESSION['newVente'])) {
               $_SESSION['newVente'] = new vente(
                    $_SESSION['Connexion']->__get("ID_Store")
               );
          }
          // On enregistre la vente
          if (isset($_POST['AjouterVente'])) {
               if (!empty($_SESSION['newVente']->__get('Panier'))) {
                    $_SESSION['newVente']->AddVente();
               } else {
                    self::Message("Erreur", "Impossible d'enregistrer la vente car le panier est vide");
               }
          }
     }
}
