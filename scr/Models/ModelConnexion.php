<?php

require_once './Models/Model.php';
require_once './Models/ModelMagasin.php';
class ModelConnexion extends Model{

     // Atributs
     private $Identifiant = "";
     private $ID_User = 0;
     private $ID_Store = 0;
     private $Status = "";
     private $IsAdmin = false;

     // Accesseurs
     public function __get($name) { return $this->$name;  }
     public function __set($name, $value) { $this->$name = $value; }

     // Méthodes
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

     public function Logout(){
          $this->ID_User = 0;
          $this->ID_Store = 0;
          $_SESSION['Connexion'] = NULL;
          header("Location:".UrlSite);
     }

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

// Fonction Hors Class
function ManageConnetion(){
     IsConnected();
     ChangeStore();
}

function ChangeStore(){
     if (isset($_POST['ChangeStore'])) {
          $_SESSION['Connexion']->__set('ID_Store',$_POST['ChangeStore']);
     }     
}

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

function IsAdmin(){
     if ($_SESSION['Connexion']->__get('Status') == "Admin") {
          header("Location:".UrlSite."/Home");
          exit;
     }
}