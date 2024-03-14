<?php

require_once './Models/Model.php';
class catalogue extends Model{

     // Attribut
     private $ID_Catalogue = 0;
     private $Nom = "";          
     private $UrlDescription = "";
     private $PrixReference = 0;
     private $Quantite = 0;
     private $Actif = true;
    
     // Constructeur
     public function __construct($id_catalogue = 0){
          if ($id_catalogue != 0) {
               $this->ID_Catalogue = $id_catalogue;
               $this->getInformation();
          }
     }

     // Accesseur
     public function __get($name) { return $this->$name;  }
     public function __set($name, $value) { $this->$name = $value; }

     private function getInformation(){
          $reqSelect = "SELECT PROPOSER.ID_Catalogue, PROPOSER.Quantite, catalogue.NomCatalogue, catalogue.URLCatalogue, catalogue.PrixReference, catalogue.Actif
               FROM PROPOSER, catalogue WHERE PROPOSER.ID_Catalogue = catalogue.ID_Catalogue AND
               catalogue.ID_Catalogue = ".$this->ID_Catalogue;

          $resultatReq = self::ExecuteQuery($reqSelect);

          if ($resultatReq != NULL) {
               $this->ID_Catalogue      = $resultatReq[0]['ID_Catalogue'];
               $this->Nom               = $resultatReq[0]['NomCatalogue'];
               $this->UrlDescription    = $resultatReq[0]['URLCatalogue'];
               $this->PrixReference     = $resultatReq[0]['PrixReference'];
               $this->Quantite          = $resultatReq[0]['Quantite'];
               $this->Actif             = $resultatReq[0]['Actif'];
          }

     }

     public function Enregister(){
          // On vérifie que la quantite et le prix ref est positif
          if ($this->Quantite >= 0 && $this->PrixReference >= 0) {
               if ($this->ID_Catalogue == 0) {
                    $this->AddCatalogue();
               }else {
                    $this->EditCatalogue();
               }
          }else {
               if ($this->Quantite < 0) {
                    Controller::Message("Erreur","La quantite ne peux pas être négative");
               }else {
                    Controller::Message("Erreur","Le prix de référence ne peux pas être négatif");
               }
          }                    
     }

     private function AddCatalogue(){       
          // On ajoute le produit dans la table catalogue
          $reqInsert = "INSERT INTO catalogue(NomCatalogue, URLCatalogue, PrixReference) 
                         VALUES ('" . $this->Nom . "','" .
                                    $this->UrlDescription . "','" .
                                    $this->PrixReference . "')"; 
          
          self::ExecuteQuery($reqInsert);

          $this->ID_Catalogue = self::GetID();
     
          // On ajoute le produit dans les proposition de tout les magasins
          $reqSelect = "SELECT ID_Magasin FROM Magasin";
          $resultReq = self::ExecuteQuery($reqSelect);
          foreach ($resultReq as $Store) {
               $Quantite = 0;
               if ($Store['ID_Magasin'] == $_SESSION['Connexion']->__get("ID_Store")) {
                    $Quantite = $this->Quantite;          
               }
               $reqInsert = "INSERT INTO proposer(ID_Magasin, ID_Catalogue, Quantite) VALUES ('" . $Store['ID_Magasin'] . "','" . $this->ID_Catalogue . "','" . $Quantite . "')";
               self::ExecuteQuery($reqInsert);
          }
          
          Controller::Message("Information","catalogue " . $this->Nom . " enregistrer");
     }
     
     private function EditCatalogue(){
          $reqUpdate = "BEGIN TRANSACTION;
          UPDATE catalogue
               SET NomCatalogue= '$this->Nom'
                    ,URLCatalogue= '$this->UrlDescription'
                    ,PrixReference= $this->PrixReference                                           
               WHERE 
                    ID_Catalogue = $this->ID_Catalogue
                    
          UPDATE PROPOSER
               SET Quantite = $this->Quantite
          WHERE
               ID_Catalogue = $this->ID_Catalogue
               AND
               ID_Magasin = " . $_SESSION['Connexion']->__get("ID_Store") . "
               
          COMMIT;"; 

          $reqUpdate = "UPDATE catalogue, proposer
                                        SET NomCatalogue= '" . $this->Nom . 
                                             "',URLCatalogue= '" . $this->UrlDescription .
                                             "',PrixReference= '" . $this->PrixReference .  
                                             "',Quantite= '" . $this->Quantite .                                             
                                             "' WHERE catalogue.ID_Catalogue = proposer.ID_Catalogue AND
                                             catalogue.ID_Catalogue = '" . $this->ID_Catalogue . "'";
          
          self::ExecuteQuery($reqUpdate);
          
          Controller::Message("Information","Le produit " . $this->Nom . " à été mise à jour");
     }

     public function ChangeEtat(){  
          $NouvelleEtat = 0;
          $NomEtat = "inactif";
          if (!$this->Actif) {
               $NouvelleEtat = 1;
               $NomEtat = "actif";
          }
          // On change l'etat du catalogue
          $reqUpdate = "UPDATE catalogue SET Actif = " . $NouvelleEtat . 
                    " WHERE ID_Catalogue = " . $this->ID_Catalogue;
          
          self::ExecuteQuery($reqUpdate);

          Controller::Message("Information","Le produit " . $this->Nom . " est maintenant " . $NomEtat);
     }

}

function listCatalogue($storeID){
     // Initialisation
     $Catalogue = [];
     $pQuantite = "%";
     $pNom = "%";
     $pUrlDescription = "%";
     $pPrixReference = "%";
     $pActif = "1"; // par défaut on selection uniquement les produits actifs

     $reqSelect = "SELECT PROPOSER.ID_Catalogue, PROPOSER.Quantite, catalogue.NomCatalogue, catalogue.URLCatalogue, catalogue.PrixReference, catalogue.Actif
     FROM PROPOSER, catalogue WHERE PROPOSER.ID_Catalogue = catalogue.ID_Catalogue AND
     PROPOSER.ID_Magasin LIKE ? AND PROPOSER.Quantite LIKE ? AND catalogue.NomCatalogue LIKE ? AND
     catalogue.URLCatalogue LIKE ? AND catalogue.PrixReference LIKE ? AND catalogue.Actif = ?";

     if (isset($_POST['FiltreCatalogueNom'])) {
          $pNom = "%" . $_POST['FiltreCatalogueNom'] . "%";
     }
     if (isset($_POST['FiltreCatalogueURL'])) {
          $pUrlDescription = "%" . $_POST['FiltreCatalogueURL'] . "%";
     }
     if (isset($_POST['FiltreCataloguePrixReference'])) {
          $pPrixReference = "%" . $_POST['FiltreCataloguePrixReference'] . "%";
     }
     if (isset($_POST['FiltreCatalogueQuantite'])) {
          $pQuantite = "%" . $_POST['FiltreCatalogueQuantite'] . "%";
     }
     if (isset($_POST['FiltreCatalogueEtat'])) {
          $pActif = $_POST['FiltreCatalogueEtat'];
     }
          
     $parameters = array($storeID,$pQuantite,$pNom,$pUrlDescription,$pPrixReference,$pActif);

     $resultReq = Model::ExecuteQuery($reqSelect,$parameters);

     foreach ($resultReq as $Produit) {

          $produit = new catalogue();

          $produit->__set("Nom",$Produit['NomCatalogue']);
          $produit->__set("UrlDescription",$Produit['URLCatalogue']);
          $produit->__set("PrixReference",$Produit['PrixReference']);
          $produit->__set("Quantite",$Produit['Quantite']);
          $produit->__set("Actif",$Produit['Actif']);
          $produit->__set("ID_Catalogue",$Produit['ID_Catalogue']);

          array_push($Catalogue,$produit);         
     }

     return $Catalogue;
}