<?php

/**
 * Lists the QR codes.
 *
 * @param int $storeID The store ID.
 * @return array The list of QR codes.
 */
class salarie extends Model{

     // Attribut
     private $ID_Salarie = 0;
     private $Identifiant = "";
     private $Password = "";
     private $Status = "";
     private $Actif = true;

    /**
     * salarie constructor.
     *
     * Initializes a salarie object. If an ID is provided, it retrieves the employee information.
     *
     * @param int $id_salarie The employee ID.
     */
     public function __construct($id_salarie = 0){
          if ($id_salarie != 0) {
               $this->ID_Salarie = $id_salarie;
               $this->getInformation();
          }
     }

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
     * Retrieves the employee information.
     */
     private function getInformation(){
          $reqSelect = "SELECT * FROM salarie WHERE ID_Salarie = ". $this->ID_Salarie;

          $resultatReq = self::ExecuteQuery($reqSelect);

          if ($resultatReq != NULL) {
               $this->ID_Salarie   = $resultatReq[0]['ID_Salarie'];
               $this->Identifiant  = $resultatReq[0]['Identifiant'];
               $this->Password     = $resultatReq[0]['Password'];
               $this->Status       = $resultatReq[0]['Status'];
               $this->Actif        = $resultatReq[0]['Actif'];
          }

     }

    /**
     * Saves the employee.
     *
     * If the employee ID is 0, it adds the employee. Otherwise, it edits the employee.
     */
     public function Enregister(){
          if ($this->ID_Salarie == 0) {
               $this->AddUser();
          }else {
               $this->EditUser();
          }
     }

    /**
     * Adds an employee.
     *
     * It adds the employee to the database and displays a message.
     */
     private function AddUser(){              
          // On enregistre l'utilisateur
          $reqInsert = "INSERT INTO Salarie(Identifiant, Password, Status) VALUES ('" . $this->Identifiant . "','" . $this->Password . "','" . $this->Status . "')"; 
          
          self::ExecuteQuery($reqInsert);
     
          // On enregistre les magasin pour lesquel il travaille
          $this->ID_Salarie = self::GetID();
          foreach ($_POST['Store'] as $store) {
               $reqInsert = "INSERT INTO TRAVAILLER(ID_Magasin, ID_Salarie) VALUES ('". $store ."','" . $this->ID_Salarie . "')";
               self::ExecuteQuery($reqInsert);
          }
          
          Controller::Message("Information","Utilisateur " . $this->Identifiant . " enregistrer");
     }

    /**
     * Edits an employee.
     *
     * It updates the employee and displays a message.
     */
     private function EditUser(){
          // Initialisation          
          $ContratSalarie = $this->listContratSalarie(); // On récupère la liste des contrats
     
          // On supprime les contrat qui n'on pas était garder
          foreach ($ContratSalarie as $contrat) {
                    
               // si un contrat n'est pas présent la liste des magasin envoyer on supprime le contrat
               if (!in_array($contrat,$_POST['Store'])) {
                         
                    $reqDelete = "DELETE FROM TRAVAILLER WHERE ID_Salarie = " . $this->ID_Salarie . " and ID_Magasin = " . $contrat;
                    self::ExecuteQuery($reqDelete);
     
               }
     
          }
          // On Ajoute les nouveaux contrats
          foreach ($_POST['Store'] as $store) {
     
               // si un contrat est inexistant dans la liste des magasin envoyer on ajoute le contrat
               if (!in_array($store,$ContratSalarie)) {
                    $reqInsert = "INSERT INTO TRAVAILLER(ID_Magasin, ID_Salarie) VALUES ('". $store ."','" . $this->ID_Salarie . "')";
     
                    self::ExecuteQuery($reqInsert);
               }
                    
          }
     
          // On Met à jour les informations du salarie
          $reqUpdate = "UPDATE Salarie SET Identifiant= '" . $this->Identifiant . "',Password= '" . $this->Password . "',Status= '" . $this->Status . "'  WHERE ID_Salarie = " . $this->ID_Salarie; 
          
          self::ExecuteQuery($reqUpdate);
          
          Controller::Message("Information","Utilisateur " . $this->Identifiant . " à été mise à jour");
     }

    /**
     * Changes the state of the employee.
     *
     * It updates the employee state and displays a message.
     */
     public function ChangeEtat(){        
          $NouvelleEtat = 0;
          $NomEtat = "inactif";
          if (!$this->Actif) {
               $NouvelleEtat = 1;
               $NomEtat = "actif";
          }

          $reqDelete = "UPDATE Salarie SET Actif = $NouvelleEtat WHERE ID_Salarie = " . $this->ID_Salarie; 
          self::ExecuteQuery($reqDelete);
          Controller::Message("Information","Le Salarie " . $this->Identifiant . " est maintenant " . $NomEtat);
     }

    /**
     * Lists the contracts of an employee.
     *
     * @return array The list of contracts.
     */
     public function listContratSalarie(){
          // Initialisation
          $resultat = [];
     
          if ($this->Status == "Administrateur") {
               $reqSelect = "SELECT ID_Magasin FROM Magasin";
          }else {
               $reqSelect = "SELECT ID_Magasin FROM TRAVAILLER WHERE ID_Salarie = '" . $this->ID_Salarie . "'";
          }          
     
          $resultatReq = self::ExecuteQuery($reqSelect);
     
          foreach ($resultatReq as $contrat) {
               array_push($resultat,$contrat[0]);
          }
     
          return $resultat;
     }

}

/**
 * Lists the employees.
 *
 * @return array The list of employees.
 */
function listSalarie(){
     global $database;
     $users = [];
     $pID = "%";
     $pNom = "%";
     $pStatus = "%";
     $pActif = "1";

     if(!empty($_POST['FiltreSalarieID'])){
          $pID = "%" . $_POST['FiltreSalarieID'] . "%";
     }
     if(!empty($_POST['FiltreSalarieNom'])){
          $pNom = "%" . $_POST['FiltreSalarieNom'] . "%";
     }
     if(!empty($_POST['FiltreSalarieStatus'])){
          $pStatus = $_POST['FiltreSalarieStatus'];
     }
     if(isset($_POST['FiltreSalarieEtat'])){
          $pActif = $_POST['FiltreSalarieEtat'];
     }

     $parameters = array($pID,$pNom,$pStatus,$pActif);

     $reqSelect = "SELECT * FROM Salarie WHERE 
     ID_Salarie LIKE ? AND 
     Identifiant LIKE ? AND
     Status LIKE ? AND
     Actif = ?"; 

     $resultReq = Model::ExecuteQuery($reqSelect,$parameters);

     foreach ($resultReq as $user) {

          $Salarie = new salarie();
          
          $Salarie->__set("ID_Salarie",$user['ID_Salarie']);
          $Salarie->__set("Identifiant",$user['Identifiant']);
          $Salarie->__set("Actif",$user['Actif']);
          $Salarie->__set("Status",$user['Status']);

          array_push($users,$Salarie);

     }

     return $users;
}

?>