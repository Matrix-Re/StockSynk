<?php

require_once './Controllers/Controller.php';
require_once './Models/ModelConnexion.php';
class ControllerConnexion extends Controller{

     public function __construct()
     {
          session_start();
          $_SESSION['Connexion'] = new ModelConnexion();
          
          if (!isset($_POST['ActionAjax'])) {
               $this->Action();
               $this->Render('Connexion','Connexion');
          }else {
               $this->ActionJQuery();
          }
     }

     // Fait une action et retoune une réponse
     private static function ActionJQuery(){
          // Action en Ajax
          if (isset($_POST['Login']) && isset($_POST['Password'])) {
               $_SESSION['Connexion']->Login($_POST['Login'],$_POST['Password']);
          }
     }

     // Fait une action et recharge la page
     private static function Action(){
          // Action      
     }

}