<?php

/**
 * Class magasin
 *
 * This class extends the Model class and provides methods for managing stores.
 */
class magasin extends Model{

     private $ID_Magasin = 0;
     private $Nom = "";
     private $CP = "";
     private $Ville = "";
     private $Actif = true;

    /**
     * magasin constructor.
     *
     * Initializes a magasin object. If an ID is provided, it retrieves the store information.
     *
     * @param int $id_magasin The store ID.
     */
     public function __construct($id_magasin = 0){
          if ($id_magasin != 0) {
               $this->ID_Magasin = $id_magasin;
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
     * Retrieves the store information.
     */
     private function getInformation(){
          $reqSelect = "SELECT * FROM magasin WHERE ID_Magasin = ". $this->ID_Magasin;

          $resultatReq = self::ExecuteQuery($reqSelect);

          if ($resultatReq != NULL) {
               $this->ID_Magasin   = $resultatReq[0]['ID_Magasin'];
               $this->Nom          = $resultatReq[0]['NomMagasin'];
               $this->CP           = $resultatReq[0]['CodePostal'];
               $this->Ville        = $resultatReq[0]['Ville'];
               $this->Actif        = $resultatReq[0]['Actif'];
          }

     }

    /**
     * Saves the store.
     *
     * If the store ID is 0, it adds the store. Otherwise, it edits the store.
     */
     public function Enregister(){
          if ($this->ID_Magasin == 0) {
               $this->AddStore();
          }else {
               $this->EditStore();
          }
     }

    /**
     * Adds a store.
     *
     * It adds the store to the database and displays a message.
     */
     private function AddStore(){               
          $reqInsert = "INSERT INTO Magasin(NomMagasin, CodePostal, Ville) VALUES ('" . $this->Nom . "','" . $this->CP . "','" . $this->Ville . "')"; 
          
          self::ExecuteQuery($reqInsert);
          $this->ID_Magasin = self::GetID();

          $reqSelect = "SELECT ID_Catalogue FROM Catalogue";
          $resultReq = self::ExecuteQuery($reqSelect);
          foreach ($resultReq as $Store) {          
               $reqInsert = "INSERT INTO proposer(ID_Magasin, ID_Catalogue, Quantite) VALUES ('" . $this->ID_Magasin . "','" . $Store['ID_Catalogue'] . "','" . 0 . "')";
               self::ExecuteQuery($reqInsert);
          }   
          
          Controller::Message("Information","Magasin " . $this->Nom . " enregistrer");
     }

    /**
     * Edits a store.
     *
     * It updates the store and displays a message.
     */
     private function EditStore(){
          $reqUpdate = "UPDATE Magasin SET NomMagasin= '" . $this->Nom . "',CodePostal= '" . $this->CP . "',Ville= '". $this->Ville . "' WHERE ID_Magasin = " . $this->ID_Magasin; 
          
          self::ExecuteQuery($reqUpdate);
          
          Controller::Message("Information","Magasin " . $this->Nom . " à été mise à jour");
     }

    /**
     * Changes the state of the store.
     *
     * It updates the store state and displays a message.
     */
     public function ChangeEtat(){
          $NouvelleEtat = 0;
          $NomEtat = "inactif";
          if (!$this->Actif) {
               $NouvelleEtat = 1;
               $NomEtat = "actif";
          }
          // On change l'etat du catalogue
          $reqUpdate = "UPDATE magasin SET Actif = " . $NouvelleEtat . 
                    " WHERE ID_Magasin = " . $this->ID_Magasin;
          
          self::ExecuteQuery($reqUpdate);

          Controller::Message("Information","Le magasin " . $this->Nom . " est maintenant " . $NomEtat);
     }

    /**
     * Lists the contracts of a store.
     *
     * @return array The list of contracts.
     */
     public function listContratMagasin(){
          
          $resultat = [];
     
          $reqSelect = "SELECT ID_Salarie FROM TRAVAILLER WHERE ID_Magasin = '" . $this->ID_Magasin  . "'";
     
          $resultatReq = self::ExecuteQuery($reqSelect);
     
          foreach ($resultatReq as $contrat) {
               array_push($resultat,$contrat[0]);
          }
     
          return $resultat;
     }

    /**
     * Gets the number of articles.
     *
     * @return int The number of articles.
     */
     public function ObtenirNombreArticle(){
          // TODO : Selectionner le nombre d'article avec quantité suppérieur à 0
          
          $resultat = 0;

          $reqSelect = "SELECT COUNT(Catalogue.ID_Catalogue) as NombreCatalogue FROM Catalogue, PROPOSER 
                         WHERE Catalogue.ID_Catalogue = PROPOSER.ID_Catalogue 
                         AND PROPOSER.ID_Magasin = " . $this->ID_Magasin;
     
          $resultatReq = self::ExecuteQuery($reqSelect);

          if (isset($resultatReq[0]['NombreCatalogue'])) {
               $resultat = $resultatReq[0]['NombreCatalogue'];
          }

          return $resultat;
     }

    /**
     * Gets the number of QR codes.
     *
     * @return int The number of QR codes.
     */
     public function ObtenirNombreQRCode(){
          
          $resultat = 0;

          $reqSelect = "SELECT COUNT(ID_QRCode) as NombreQRCode FROM QRCode 
                         WHERE ID_Magasin = " . $this->ID_Magasin;
     
          $resultatReq = self::ExecuteQuery($reqSelect);

          if (isset($resultatReq[0]['NombreQRCode'])) {
               $resultat = $resultatReq[0]['NombreQRCode'];
          }

          return $resultat;
     }

}

/**
 * Lists the stores.
 *
 * @return array The list of stores.
 */
function listMagasin(){
     
     $listMagasin = [];
     $pNomMagasin = "%";
     $pCodePostal = "%";
     $pVille = "%";
     $pActif= "1";

     $reqSelect = "SELECT * FROM magasin WHERE NomMagasin LIKE ? AND CodePostal LIKE ? AND
               Ville LIKE ? AND Actif = ?"; 

     if (!empty($_POST['FiltreMagasinNom'])) {
          $pNomMagasin = "%" . $_POST['FiltreMagasinNom'] . "%";
     }
     if (!empty($_POST['FiltreMagasinCP'])) {
          $pCodePostal = "%" . $_POST['FiltreMagasinCP'] . "%";
     }
     if (!empty($_POST['FiltreMagasinVille'])) {
          $pVille = "%" . $_POST['FiltreMagasinVille'] . "%";
     }
     if (isset($_POST['FiltreMagasinEtat'])) {
          $pActif = $_POST['FiltreMagasinEtat'];
     }

     $parameters = array($pNomMagasin,$pCodePostal,$pVille,$pActif);

     $resultReq = Model::ExecuteQuery($reqSelect,$parameters);

     foreach ($resultReq as $store) {

          $Store = new magasin();

          $Store->__set("ID_Magasin",$store['ID_Magasin']);
          $Store->__set("Nom",$store['NomMagasin']);
          $Store->__set("CP",$store['CodePostal']);
          $Store->__set("Ville",$store['Ville']);
          $Store->__set("Actif",$store['Actif']);          

          array_push($listMagasin,$Store);

     }

     return $listMagasin;
}