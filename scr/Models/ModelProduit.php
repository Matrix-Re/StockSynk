<?php

/**
 * Lists the stores.
 *
 * @return array The list of stores.
 */
class produit{

     private $ID_Catalogue = 0;
     private $PU = 0;
     private $Quantite = 0;

    /**
     * produit constructor.
     *
     * Initializes a produit object with the provided catalogue ID, unit price, and quantity.
     *
     * @param int $id_catalogue The catalogue ID.
     * @param float $pu The unit price.
     * @param int $quantite The quantity.
     */
     public function __construct($id_catalogue, $pu, $quantite){
          $this->ID_Catalogue = $id_catalogue;
          $this->PU = $pu;
          $this->Quantite = $quantite;
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

}