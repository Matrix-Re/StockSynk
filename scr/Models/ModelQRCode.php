<?php

require "phpqrcode/qrlib.php";

/**
 * Sets the value of a property.
 *
 * @param string $name The property name.
 * @param mixed $value The property value.
 */
class qrcodeimage extends Model{

     // Attribut
     private $ID_QRCode = 0;
     private $Nom = "";
     private $Actif = true;
     private $ID_Catalogue = 0;

    /**
     * qrcodeimage constructor.
     *
     * Initializes a qrcodeimage object. If an ID is provided, it retrieves the QR code information.
     *
     * @param int $id_qrcode The QR code ID.
     */
     public function __construct($id_qrcode = 0){
          if ($id_qrcode != 0) {
               $this->ID_QRCode = $id_qrcode;
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
     * Retrieves the QR code information.
     */
     private function getInformation(){
          $reqSelect = "SELECT qrcode.ID_QRCode, qrcode.NomQRCode, qrcode.Actif, qrcode.ID_Catalogue, catalogue.NomCatalogue
               FROM qrcode, catalogue
          WHERE qrcode.ID_Catalogue = catalogue.ID_Catalogue AND qrcode.ID_QRCode = " . $this->ID_QRCode;

          $resultatReq = self::ExecuteQuery($reqSelect);

          if ($resultatReq != NULL) {
               $this->ID_QRCode    = $resultatReq[0]['ID_QRCode'];
               $this->Nom          = $resultatReq[0]['NomQRCode'];
               $this->Actif        = $resultatReq[0]['Actif'];
               $this->ID_Catalogue = $resultatReq[0]['ID_Catalogue'];
          }

     }

    /**
     * Saves the QR code.
     *
     * If the QR code ID is 0, it adds the QR code. Otherwise, it updates the QR code.
     */
     public function Save(){
          if ($this->ID_QRCode == 0) {
               $this->Insert();
          }else {
               $this->Update();
          }
     }

    /**
     * Inserts a new QR code.
     */
     private function Insert(){    
          // On enregistre l'utilisateur
          $reqInsert = "INSERT INTO qrcode(NomQRCode, Actif, ID_Magasin, ID_Catalogue) VALUES ('" . $this->Nom . "','" . (int)$this->Actif . "','" . $_SESSION['Connexion']->__get('ID_Store') . "','" . $this->ID_Catalogue . "')"; 
          
          self::ExecuteQuery($reqInsert);
          
          Controller::Message("Information","QRCode " . $this->Nom . " enregistrer");
     }

    /**
     * Updates an existing QR code.
     */
     private function Update(){
     
          // On enregistre l'utilisateur
          $reqInsert = "UPDATE qrcode SET NomQRCode = '" . $this->Nom . "',
                                             Actif = " . (int)$this->Actif . ",
                                             ID_Catalogue = " . $this->ID_Catalogue . 
                                             "  WHERE ID_QRCode = " . $this->ID_QRCode; 
          
          self::ExecuteQuery($reqInsert);
          $this->SupprimerImageQRCode();
          
          Controller::Message("Information","QRCode " . $this->Nom . " enregistrer");
     }

    /**
     * Changes the state of the QR code.
     */
     public function ChangeEtat(){
          $NouvelleEtat = 0;
          $NomEtat = "inactif";
          if (!$this->Actif) {
               $NouvelleEtat = 1;
               $NomEtat = "actif";
          }
          // On change l'etat du catalogue
          $reqUpdate = "UPDATE qrcode SET Actif = " . $NouvelleEtat . 
                    " WHERE ID_QRCode = " . $this->ID_QRCode;
          
          self::ExecuteQuery($reqUpdate);
          $this->SupprimerImageQRCode();
          
          Controller::Message("Erreur","Le QRCode " . $this->Nom. " est maintenant " . $NomEtat);
     }

    /**
     * Gets the URL of the catalogue.
     *
     * @return string The URL of the catalogue.
     */
     public function GetURLCatalogue(){
          $URLCatalogue = "";
          $reqSelect = "SELECT URLCatalogue FROM Catalogue WHERE ID_Catalogue = " . $this->ID_Catalogue;

          $resultReq = self::ExecuteQuery($reqSelect);

          if(!empty($resultReq[0]['URLCatalogue'])){
               $URLCatalogue = $resultReq[0]['URLCatalogue'];
               if(strpos(strtolower($URLCatalogue), "http") === false)
                    $URLCatalogue = "http://" . $URLCatalogue;
          }
               

          return $URLCatalogue;
     }

    /**
     * Generates a QR code.
     *
     * @return string The directory link of the generated QR code.
     */
     public function GenererQRCode(){
          $linkWeb = "http://Gestion-Stock/qrcode/".$this->ID_QRCode;
          $linkDirectory = "";
          $filename = "qrcode" . $this->ID_QRCode . ".png";
          $Directory = "phpqrcode/qrcodeimage";
          $PNG_WEB_DIR = "Models/" . $Directory . "/";

          if (file_exists($PNG_WEB_DIR . $filename)) {
               $linkDirectory = $PNG_WEB_DIR . $filename;
          }else {
               $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.$Directory.DIRECTORY_SEPARATOR;
    
               //ofcourse we need rights to create temp dir
               if (!file_exists($PNG_TEMP_DIR))
                    mkdir($PNG_TEMP_DIR);
          
               $fileDestination = $PNG_TEMP_DIR . $filename;
          
               QRcode::png($linkWeb, $fileDestination, "M", 10, 2);    
               
               $linkDirectory = $PNG_WEB_DIR . $filename;
          }
          return $linkDirectory;
     }

    /**
     * Deletes the QR code image.
     */
     public function SupprimerImageQRCode(){
          $filename = "qrcode" . $this->ID_QRCode . ".png";
          $Directory = "phpqrcode/qrcodeimage";
          $PNG_WEB_DIR = "Model/" . $Directory . "/";

          if (file_exists($PNG_WEB_DIR . $filename))
               unlink($PNG_WEB_DIR . $filename);
     }

    /**
     * Increments the scan count.
     */
     public function IncrementScan(){

          $reqSelect = "SELECT Scan.ID_Scan,
          Scan.NombreScan 
          FROM Scan
          WHERE Scan.DateScan = '" .date("Ymd") . "' AND
          Scan.ID_QRCode = $this->ID_QRCode AND
          Scan.ID_Catalogue = $this->ID_Catalogue";

          $resultReq = self::ExecuteQuery($reqSelect);
          
          if (!empty($resultReq)) {
               $reqUpdate = "UPDATE Scan SET NombreScan = " . ($resultReq[0]['NombreScan']+1) . " WHERE Scan.ID_Scan = " . $resultReq[0]['ID_Scan'];
               
               $resultReq = self::ExecuteQuery($reqUpdate);
          }else {
               $reqInsert = "INSERT INTO Scan (DateScan, NombreScan, ID_Catalogue, ID_QRCode) 
               VALUES ('" . date("Ymd") . "',1,$this->ID_Catalogue,$this->ID_QRCode)";

               $resultReq = self::ExecuteQuery($reqInsert);
          }

     }

}

/**
 * Lists the QR codes.
 *
 * @param int $storeID The store ID.
 * @return array The list of QR codes.
 */
function listQRCode($storeID){
     // Initialisation
     global $database;
     $listQRCode = [];
     $pIDQRCode = "%";
     $pNomQRCode = "%";
     $pNomCatalogue = "%";
     $pActif = "1"; // par défaut on selection uniquement les qrcode actifs

     $reqSelect = "SELECT qrcode.ID_QRCode, qrcode.NomQRCode, qrcode.Actif, qrcode.ID_Catalogue,
          catalogue.NomCatalogue FROM qrcode, catalogue
          WHERE qrcode.ID_Catalogue = catalogue.ID_Catalogue AND
          qrcode.ID_Magasin = ? AND ID_QRCode LIKE ? AND NomQRCode LIKE ? AND
          NomCatalogue LIKE ? AND qrcode.Actif = ?"; 

     if (!empty($_POST['FiltreQRCodeID'])) {
          $pIDQRCode = "%" . $_POST['FiltreQRCodeID'] . "%";
     }
     if (!empty($_POST['FiltreQRCodeNom'])) {
          $pNomQRCode = "%" . $_POST['FiltreQRCodeNom'] . "%";
     }
     if (!empty($_POST['FiltreQRCodeNomCatalogue'])) {
          $pNomCatalogue = "%" . $_POST['FiltreQRCodeNomCatalogue'] . "%";
     }
     if (isset($_POST['FiltreQRCodeEtat'])) {
          $pActif = $_POST['FiltreQRCodeEtat'];
     }

     $parameters = array($storeID,$pIDQRCode,$pNomQRCode,$pNomCatalogue,$pActif);

     $resultReq = Model::ExecuteQuery($reqSelect,$parameters);

     foreach ($resultReq as $qrcode) {

          $QRCode = new qrcodeimage();

          $QRCode->__set("ID_QRCode",$qrcode['ID_QRCode']);
          $QRCode->__set("Nom",$qrcode['NomQRCode']);
          $QRCode->__set("Actif",$qrcode['Actif']);
          $QRCode->__set("ID_Catalogue",$qrcode['ID_Catalogue']);

          array_push($listQRCode,$QRCode);

     }

     return $listQRCode;

}

?>