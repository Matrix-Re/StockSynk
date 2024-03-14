<?php

class vente{

     // Attribut
     private $ID_Vente = "";
     private $DateVente = "";
     private $ID_Magasin = "";
     private $NbArticle = "";
     private $PrixTotal = "";
     
     private $Panier = [];  
    
     // Constructeur
     public function __construct($id_magasin,$datevente = null,$nbarticle = 0,$prixtotal = 0,$id_vente = 0){
          $this->ID_Vente = $id_vente;
          $this->DateVente = $datevente;
          $this->ID_Magasin = $id_magasin;
          $this->NbArticle = $nbarticle;
          $this->PrixTotal = $prixtotal;
     }

     // Accesseur
     public function __get($name) { return $this->$name;  }
     public function __set($name, $value) { $this->$name = $value; }

     // Methode
     public function AddVente(){
          global $database;
               
          $reqInsert = "INSERT INTO vente(DateVente, ID_Magasin) VALUES ('".date("Ymd")."', $this->ID_Magasin)";
          // On execute la requête
          $database->ExecuteQuery($reqInsert);
          // On récupère l'id de la vente crée précedement
          $this->ID_Vente = $database->GetID();

          foreach ($this->Panier as $Produit) {
               $reqInsert = "INSERT INTO REPRESENTE(ID_Vente, ID_Catalogue, PrixUnitaire, Quantite) 
               VALUES ($this->ID_Vente," . $Produit->__get('ID_Catalogue') . "," . $Produit->__get('PU') . "," .
               $Produit->__get('Quantite') .")";
               
               $database->ExecuteQuery($reqInsert);
               $reqUpdate = "UPDATE proposer SET Quantite = Quantite - " . $Produit->__get('Quantite') .
               " WHERE ID_Catalogue = " . $Produit->__get('ID_Catalogue') ." AND 
               ID_Magasin = " . $_SESSION['connexion']->__get("ID_Magasin");
               $database->ExecuteQuery($reqUpdate);
          }          

          $_SESSION['newVente'] = null;          
          echo "Vente enregistrer";
     }

     public function AddPanier($id_catalogue, $prixunitaire, $quantite){
          
          //getQuantiteProduit - quantité panier - quantite à ajouter > 0
          if (getQuantiteCatalogue($id_catalogue) - $this->QuantiteProduit($id_catalogue) - $quantite >= 0){
               array_push($this->Panier,new produit(
                    $id_catalogue,
                    $prixunitaire,
                    $quantite)
               );
               echo "Le produit " . getNameCatalogue($_POST['ID_Catalogue']) . " a été ajouté au panier";
          }else {
               echo "Le produit " . getNameCatalogue($_POST['ID_Catalogue']) . " n'a pas été ajouté au panier, stock insuffisant";
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
          global $database;

          $reqSelect = "SELECT represente.ID_Catalogue, represente.PrixUnitaire, represente.Quantite
          FROM vente, represente
          WHERE vente.ID_Vente = represente.ID_Vente
          AND vente.ID_Vente = " . $this->ID_Vente;

          $resultReq = $database->ExecuteQuery($reqSelect);

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

     if (!empty($_POST['FiltreIDVente'])) {
          $FiltreWhere .= " AND vente.ID_Vente LIKE '%" . $_POST['FiltreIDVente'] . "%'";
     }
     if (!empty($_POST['FiltreDateMinVente'])) {
          $FiltreWhere .= " AND vente.DateVente >= '" . $_POST['FiltreDateMinVente'] . "'";
     }
     if (!empty($_POST['FiltreDateMaxVente'])) {
          $FiltreWhere .= " AND vente.DateVente <= '" . $_POST['FiltreDateMaxVente'] . "'";
     }
     if (!empty($_POST['FiltreNombreMinProduit'])) {
          if($FiltreHaving != "") $FiltreHaving .= " AND ";
          $FiltreHaving .= " COUNT(represente.Quantite) >= " . $_POST['FiltreNombreMinProduit'];
     }
     if (!empty($_POST['FiltreNombreMaxProduit'])) {
          if($FiltreHaving != "") $FiltreHaving .= " AND ";
          $FiltreHaving .= " COUNT(represente.Quantite) <= " . $_POST['FiltreNombreMaxProduit'];
     }
     if (!empty($_POST['FiltrePrixMinTotal'])) {
          if($FiltreHaving != "") $FiltreHaving .= " AND ";
          $FiltreHaving .= " SUM(represente.PrixUnitaire * represente.Quantite) >= " . $_POST['FiltrePrixMinTotal'];
     }
     if (!empty($_POST['FiltrePrixMaxTotal'])) {
          if($FiltreHaving != "") $FiltreHaving .= " AND ";
          $FiltreHaving .= " SUM(represente.PrixUnitaire * represente.Quantite) <= " . $_POST['FiltrePrixMaxTotal'];
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

     $resultReq = $database->ExecuteQuery($reqSelect,$parameters);

     foreach ($resultReq as $vente) {

          array_push($listVente,
               new vente($storeID,$vente['DateVente'],$vente['NbArticle'],$vente['Total'],$vente['ID_Vente'])
          );
     }

     return $listVente;
}

function RechargeListVente(){
     global $listVente;
     $listVente = listVente($_SESSION['connexion']->__get("ID_Magasin"));
}

function getIndiceVente($ID_Vente){
     // Initialisation
     global $listVente;
     $indice = 0;

     foreach ($listVente as $Vente) {
          if ($Vente->__get("ID_Vente") == $ID_Vente) { break; }
          $indice++;
     }

     if (count($listVente)-1 < $indice) {
          die("impossible de trouver l'indice <br>");
     }

     return $indice;
}

?>