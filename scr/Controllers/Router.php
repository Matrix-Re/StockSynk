<?php

class Router{

     public function routeReq(){
          
          $url = [''];
          if (isset($_GET['url'])) {
               $url = explode('/', $_GET['url']);
          }

          // On récupère le paramètre url saisir après le nom de la page
          if(!empty($url[1])){
               $GLOBALS['paramterUrl'] = $url[1];
          }          

          $controller = "Controller".ucfirst(strtolower($url[0]));

          if(!empty($url[0])){
               $controllerRepertory = "Controllers/" . $controller . ".php";
               if(file_exists($controllerRepertory)) 
               {
                    require($controllerRepertory);
                    $MonController = new $controller();
               }
               else{ 
                    echo "Page introuvable"; 
               }
          }else{
               require("Controllers/ControllerConnexion.php");
               $controller = new ControllerConnexion();
          }

     }

}