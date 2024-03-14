<?php

class produit{

     // Attribut
     private $ID_Catalogue = 0;
     private $PU = 0;
     private $Quantite = 0;

     public function __construct($id_catalogue, $pu, $quantite){
          $this->ID_Catalogue = $id_catalogue;
          $this->PU = $pu;
          $this->Quantite = $quantite;
     }

     // Accesseur
     public function __get($name) { return $this->$name;  }
     public function __set($name, $value) { $this->$name = $value; }

}

//-----------------------//
//  Fonction hors class  //
//-----------------------//



?>