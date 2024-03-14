<?php

require_once './Models/ModelProduit.php';

class vente extends Model{

     // Attribut
     private $ID_Vente = "";
     private $DateVente = "";
     private $ID_Magasin = "";
     private $NbArticle = "";
     private $PrixTotal = "";
     
     private $Panier = [];  
    
     // Constructeur
     public function __construct($id_magasin, $id_vente = 0){
          $this->ID_Magasin = $id_magasin;
          if ($id_vente != 0) {
               $this->ID_Vente = $id_vente;
               $this->getInformation();
          }
     }

     // Accesseur
     public function __get($name) { return $this->$name;  }
     public function __set($name, $value) { $this->$name = $value; }

     // Methode
     private function getInformation(){
          $reqSelect = "SELECT * FROM vente WHERE ID_Vente = ". $this->ID_Vente;

          $resultatReq = self::ExecuteQuery($reqSelect);

          if ($resultatReq != NULL) {
               $this->ID_Magasin   = $resultatReq[0]['ID_Magasin'];
               $this->DateVente    = $resultatReq[0]['DateVente'];
               $this->ID_Vente     = $resultatReq[0]['ID_Vente'];
          }

     }

     public function AddVente(){

          $reqInsert = "INSERT INTO vente(DateVente, ID_Magasin) VALUES ('".date("Ymd")."', $this->ID_Magasin)";
          // On execute la requête
          self::ExecuteQuery($reqInsert);
          // On récupère l'id de la vente crée précedement
          $this->ID_Vente = self::GetID();

          foreach ($this->Panier as $Produit) {
               $reqInsert = "INSERT INTO REPRESENTE(ID_Vente, ID_Catalogue, PrixUnitaire, Quantite) 
               VALUES ($this->ID_Vente," . $Produit->__get('ID_Catalogue') . "," . $Produit->__get('PU') . "," .
               $Produit->__get('Quantite') .")";
               
               self::ExecuteQuery($reqInsert);
               $reqUpdate = "UPDATE proposer SET Quantite = Quantite - " . $Produit->__get('Quantite') .
               " WHERE ID_Catalogue = " . $Produit->__get('ID_Catalogue') ." AND 
               ID_Magasin = " . $_SESSION['Connexion']->__get("ID_Store");
               self::ExecuteQuery($reqUpdate);
          }          

          $_SESSION['newVente'] = null;          
          Controller::Message("Information","La vente à été enregistré");
     }

     public function AddPanier($id_catalogue, $prixunitaire, $quantite){
          
          require_once './Models/ModelCatalogue.php';
          $Catalogue = new catalogue($id_catalogue);
          if ($Catalogue->__get("Quantite") - $this->QuantiteProduit($id_catalogue) - $quantite >= 0){
               array_push($this->Panier,new produit(
                    $id_catalogue,
                    $prixunitaire,
                    $quantite)
               );
               Controller::Message("Information","Le produit " . $Catalogue->__get("Nom") . " a été ajouté au panier");
          }else {
               Controller::Message("Erreur","Le produit " . $Catalogue->__get("Nom") . " n'a pas été ajouté au panier car le stock est insuffisant.<br>
                    Le stock est actuellement de " . $Catalogue->__get("Quantite"));
          }
          
     }

     public function DeletePanier($indice){
          unset($this->Panier[$indice]);
          $this->Panier = array_values($this->Panier);
          echo "Article supprimé du panier";
     }

     private function QuantiteProduit($ID_Catalogue){
          $resultat = 0;
          foreach ($this->Panier as $Produit) {
               if ($Produit->__get('ID_Catalogue') == $ID_Catalogue) {
                    $resultat += $Produit->__get('Quantite');
               }
          }
          return $resultat;
     }

     public function DetailVente(){

          $reqSelect = "SELECT represente.ID_Catalogue, represente.PrixUnitaire, represente.Quantite
          FROM vente, represente
          WHERE vente.ID_Vente = represente.ID_Vente
          AND vente.ID_Vente = " . $this->ID_Vente;

          $resultReq = self::ExecuteQuery($reqSelect);

          foreach ($resultReq as $article) {
               array_push($this->Panier,new produit(
                    $article['ID_Catalogue'],
                    $article['PrixUnitaire'],
                    $article['Quantite'])
               );
          }
     }
}

//-----------------------//
//  Fonction hors class  //
//-----------------------//

function listVente($storeID){
     // Initialisation
     global $database;
     $listVente = [];
     $FiltreWhere = "";
     $FiltreHaving = "";

     if (!empty($_POST['FiltreVenteID'])) {
          $FiltreWhere .= " AND vente.ID_Vente LIKE '%" . $_POST['FiltreVenteID'] . "%'";
     }
     if (!empty($_POST['FiltreVenteDateMin'])) {
          $FiltreWhere .= " AND vente.DateVente >= '" . $_POST['FiltreVenteDateMin'] . "'";
     }
     if (!empty($_POST['FiltreVenteDateMax'])) {
          $FiltreWhere .= " AND vente.DateVente <= '" . $_POST['FiltreVenteDateMax'] . "'";
     }
     if (!empty($_POST['FiltreVenteNbProduitMin'])) {
          if($FiltreHaving != "") $FiltreHaving .= " AND ";
          $FiltreHaving .= " COUNT(represente.Quantite) >= " . $_POST['FiltreVenteNbProduitMin'];
     }
     if (!empty($_POST['FiltreVenteNbProduitMax'])) {
          if($FiltreHaving != "") $FiltreHaving .= " AND ";
          $FiltreHaving .= " COUNT(represente.Quantite) <= " . $_POST['FiltreVenteNbProduitMax'];
     }
     if (!empty($_POST['FiltreVentePrixTotalMin'])) {
          if($FiltreHaving != "") $FiltreHaving .= " AND ";
          $FiltreHaving .= " SUM(represente.PrixUnitaire * represente.Quantite) >= " . $_POST['FiltreVentePrixTotalMin'];
     }
     if (!empty($_POST['FiltreVentePrixTotalMax'])) {
          if($FiltreHaving != "") $FiltreHaving .= " AND ";
          $FiltreHaving .= " SUM(represente.PrixUnitaire * represente.Quantite) <= " . $_POST['FiltreVentePrixTotalMax'];
     }

     if($FiltreHaving != "") $FiltreHaving = " HAVING " . $FiltreHaving;

     $reqSelect = "SELECT vente.ID_Vente, vente.ID_Magasin, vente.DateVente,
               COUNT(represente.Quantite) as 'NbArticle',
               SUM(represente.PrixUnitaire * represente.Quantite) as 'Total'
          FROM
	          vente, represente
          WHERE
	          vente.ID_Vente = represente.ID_Vente
          AND
               vente.ID_Magasin = ? ".$FiltreWhere."
          GROUP BY
	          vente.ID_Vente, vente.ID_Magasin, vente.DateVente " . $FiltreHaving ;

          $parameters = array($storeID);

     $resultReq = Model::ExecuteQuery($reqSelect,$parameters);

     foreach ($resultReq as $vente) {

          $Vente = new vente($storeID);

          $Vente->__set("ID_Vente",$vente['ID_Vente']);
          $Vente->__set("DateVente",$vente['DateVente']);
          $Vente->__set("NbArticle",$vente['NbArticle']);
          $Vente->__set("PrixTotal",$vente['Total']);

          array_push($listVente,$Vente);
     }

     return $listVente;
}

?>