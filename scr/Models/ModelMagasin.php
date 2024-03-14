<?php

class magasin extends Model{

     // Attribut
     private $ID_Magasin = 0;
     private $Nom = "";
     private $CP = "";
     private $Ville = "";
     private $Actif = true;

     // Constructeur
     public function __construct($id_magasin = 0){
          if ($id_magasin != 0) {
               $this->ID_Magasin = $id_magasin;
               $this->getInformation();
          }
     }

     // Accesseur
     public function __get($name) { return $this->$name;  }
     public function __set($name, $value) { $this->$name = $value; }


     // Methode
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

     public function Enregister(){
          if ($this->ID_Magasin == 0) {
               $this->AddStore();
          }else {
               $this->EditStore();
          }
     }

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
     
     private function EditStore(){
          $reqUpdate = "UPDATE Magasin SET NomMagasin= '" . $this->Nom . "',CodePostal= '" . $this->CP . "',Ville= '". $this->Ville . "' WHERE ID_Magasin = " . $this->ID_Magasin; 
          
          self::ExecuteQuery($reqUpdate);
          
          Controller::Message("Information","Magasin " . $this->Nom . " à été mise à jour");
     }

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

     public function listContratMagasin(){
          
          $resultat = [];
     
          $reqSelect = "SELECT ID_Salarie FROM TRAVAILLER WHERE ID_Magasin = '" . $this->ID_Magasin  . "'";
     
          $resultatReq = self::ExecuteQuery($reqSelect);
     
          foreach ($resultatReq as $contrat) {
               array_push($resultat,$contrat[0]);
          }
     
          return $resultat;
     }

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

//-----------------------//
//  Fonction hors class  //
//-----------------------//

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