<?php

require_once './Controllers/Controller.php';
require_once './Models/ModelConnexion.php';

/**
 * Class ControllerConnexion
 *
 * This class extends the Controller class and provides methods for managing user connections.
 */
class ControllerConnexion extends Controller{

    /**
     * ControllerConnexion constructor.
     *
     * Starts the session, creates a new ModelConnexion object, and handles actions.
     */
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

    /**
     * Handles AJAX actions.
     *
     * If login and password are set, it attempts to log in the user.
     */
     private static function ActionJQuery(){
          // Action en Ajax
          if (isset($_POST['Login']) && isset($_POST['Password'])) {
               $_SESSION['Connexion']->Login($_POST['Login'],$_POST['Password']);
          }
     }

    /**
     * Handles non-AJAX actions.
     */
     // Fait une action et recharge la page
     private static function Action(){
          // Action      
     }

}