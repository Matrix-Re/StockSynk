<?php

require_once './Models/Model.php';
require_once './Models/ModelMagasin.php';

/**
 * Class ModelConnexion
 *
 * This class extends the Model class and provides methods for managing user connections.
 */
class ModelConnexion extends Model{

     // Atributs
     private $Identifiant = "";
     private $ID_User = 0;
     private $ID_Store = 0;
     private $Status = "";
     private $IsAdmin = false;

    /**
     * Gets the value of a property.
     *
     * @param string $name The property name.
     * @return mixed The property value.
     */
     public function __get($name) { return $this->$name;  }

    /**
     * Sets the value of a property.
     *
     * @param string $name The property name.
     * @param mixed $value The property value.
     */
     public function __set($name, $value) { $this->$name = $value; }

    /**
     * Logs in a user.
     *
     * If the login and password are not null, it checks if the password is correct and if the user is active.
     * If the password is correct and the user is active, it sets the user ID, identifier, status, and store ID, and redirects to the home page.
     * Otherwise, it displays an error message.
     *
     * @param string $Login The user login.
     * @param string $Password The user password.
     */
     public function Login($Login,$Password){
          if ($Login != null && $Password != null) {
               $MotDePasseCorrecte = true;

               $reqConnexion = "SELECT * FROM Salarie WHERE Identifiant = '" . $Login . "'";
               
               $resultat = self::ExecuteQuery($reqConnexion);

               // On vérifie si le mot de passe est correcte
               if ($resultat != null){
                    $MotDePasseCorrecte = password_verify($Password,$resultat[0]['Password']);
               }

               if ($MotDePasseCorrecte && $resultat[0]['Actif']) {  
                    // On enregistre l'ID Client dans la session
                    $this->ID_User = $resultat[0]['ID_Salarie'];
                    $this->Identifiant = $resultat[0]['Identifiant'];
                    // On défini le status de l'utilisateur
                    $this->Status = $resultat[0]['Status'];
                    if($resultat[0]['Status'] == "Administrateur"){
                         $this->IsAdmin = true;
                    }

                    // Selectionner la liste des magasin pour lequel le salarie travaille
                    $listMagasin = $this->listContratSalarie();
                    if (!empty($listMagasin)) {
                         $this->ID_Store = $listMagasin[0]->__get("ID_Magasin");
                    }                                       
     
                    // On redirige vers la page d'accueil  
                    $_SESSION['Connexion'] = $this;
                    echo json_encode(array("Status" => "Approuved", "Link" => UrlSite."home"));           
               }else {      
                    Controller::Message("Erreur","Identifiant ou mot de passe incorrecte");
                    $_SESSION['connexion'] = null;
               }
          }
          else {
              Controller::Message("Erreur","Veuillez saisir un identifiant et un mot de passe");
          }
     }

    /**
     * Logs out a user.
     *
     * It resets the user ID and store ID, and redirects to the site URL.
     */
     public function Logout(){
          $this->ID_User = 0;
          $this->ID_Store = 0;
          $_SESSION['Connexion'] = NULL;
          header("Location:".UrlSite);
     }

    /**
     * Lists the contracts of a salaried employee.
     *
     * If the user is an administrator, it selects all stores.
     * Otherwise, it selects the stores where the salaried employee works.
     * It returns the list of stores.
     *
     * @return array The list of stores.
     */
     public function listContratSalarie(){
          // Initialisation
          $ListMagasin = [];
     
          if ($this->Status == "Administrateur") {
               $reqSelect = "SELECT NomMagasin, ID_Magasin FROM Magasin";
          }else {
               $reqSelect = "SELECT NomMagasin, Magasin.ID_Magasin 
                         FROM 
                              Magasin, travailler
                         WHERE
                              magasin.ID_Magasin = travailler.ID_Magasin
                              AND
                              travailler.ID_Salarie = " . $this->ID_User;
          }
               
          $resultatReq = self::ExecuteQuery($reqSelect);
     
          foreach ($resultatReq as $contrat) {

               $magasin = new magasin();

               $magasin->__set("Nom",$contrat['NomMagasin']);
               $magasin->__set("ID_Magasin",$contrat['ID_Magasin']);

               array_push($ListMagasin,$magasin);
          }
     
          return $ListMagasin;
     }

}

/**
 * Manages the user connection.
 *
 * This function checks if the user is connected and changes the store if necessary.
 */
function ManageConnetion(){
     IsConnected();
     ChangeStore();
}

/**
 * Changes the store.
 *
 * This function checks if the 'ChangeStore' POST variable is set.
 * If it is, it changes the 'ID_Store' attribute of the 'Connexion' session variable to the value of the 'ChangeStore' POST variable.
 */
function ChangeStore(){
     if (isset($_POST['ChangeStore'])) {
          $_SESSION['Connexion']->__set('ID_Store',$_POST['ChangeStore']);
     }     
}

/**
 * Checks if the user is connected.
 *
 * This function checks if the 'Connexion' session variable is null or if the 'ID_User' attribute of the 'Connexion' session variable is 0.
 * If either condition is true, it redirects the user to the site URL and exits the script.
 */
function IsConnected(){
     if ($_SESSION['Connexion'] == NULL) {
          header("Location:".UrlSite);
          exit;
     }else {
          if($_SESSION['Connexion']->__get('ID_User') == 0){
               header("Location:".UrlSite);
               exit;
          }
     }
}

/**
 * Checks if the user is an admin.
 *
 * This function checks if the 'Status' attribute of the 'Connexion' session variable is "Admin".
 * If it is, it redirects the user to the home page of the site URL and exits the script.
 */
function IsAdmin(){
     if ($_SESSION['Connexion']->__get('Status') == "Admin") {
          header("Location:".UrlSite."/Home");
          exit;
     }
}