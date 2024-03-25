<?php

require_once './Models/ModelProduit.php';

/**
 * Lists the employees.
 *
 * @return array The list of employees.
 */
class vente extends Model{

     private $ID_Vente = "";
     private $DateVente = "";
     private $ID_Magasin = "";
     private $NbArticle = "";
     private $PrixTotal = "";
     
     private $Panier = [];

    /**
     * vente constructor.
     *
     * Initializes a vente object with the provided store ID. If a sale ID is provided, it retrieves the sale information.
     *
     * @param int $id_magasin The store ID.
     * @param int $id_vente The sale ID.
     */
     public function __construct($id_magasin, $id_vente = 0){
          $this->ID_Magasin = $id_magasin;
          if ($id_vente != 0) {
               $this->ID_Vente = $id_vente;
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
     * Retrieves the sale information.
     */
     private function getInformation(){
          $reqSelect = "SELECT * FROM vente WHERE ID_Vente = ". $this->ID_Vente;

          $resultatReq = self::ExecuteQuery($reqSelect);

          if ($resultatReq != NULL) {
               $this->ID_Magasin   = $resultatReq[0]['ID_Magasin'];
               $this->DateVente    = $resultatReq[0]['DateVente'];
               $this->ID_Vente     = $resultatReq[0]['ID_Vente'];
          }

     }

    /**
     * Adds a sale.
     *
     * It adds the sale to the database and displays a message.
     */
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

    /**
     * Adds a product to the cart.
     *
     * @param int $id_catalogue The catalogue ID.
     * @param float $prixunitaire The unit price.
     * @param int $quantite The quantity.
     */
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

    /**
     * Removes a product from the cart.
     *
     * @param int $indice The index of the product in the cart.
     */
     public function DeletePanier($indice){
          unset($this->Panier[$indice]);
          $this->Panier = array_values($this->Panier);
          echo "Article supprimé du panier";
     }

    /**
     * Gets the quantity of a product.
     *
     * @param int $ID_Catalogue The catalogue ID.
     * @return int The quantity of the product.
     */
     private function QuantiteProduit($ID_Catalogue){
          $resultat = 0;
          foreach ($this->Panier as $Produit) {
               if ($Produit->__get('ID_Catalogue') == $ID_Catalogue) {
                    $resultat += $Produit->__get('Quantite');
               }
          }
          return $resultat;
     }

    /**
     * Gets the details of a sale.
     */
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

/**
 * Lists the sales.
 *
 * @param int $storeID The store ID.
 * @return array The list of sales.
 */
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