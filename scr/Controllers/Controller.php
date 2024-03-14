<?php

abstract class Controller{

    public static function Render($View, $NomPage, $Data = [""]){  
        extract($Data);      
        if(file_exists("Views/$View.php"))
        {
            ob_start();
            $title = $NomPage;
            require "Views/$View.php";
            $content = ob_get_clean();
            require 'Views/layout.php';
        }else {
            require 'Views/Error.php';
        }                
    }

    public static function Message($PopupTitle,$PopupMessage){
        ob_start();
        $typePopup = "popupInformation";
        require 'Views/Popup/popupInformation.php';
        $PopupContent = ob_get_clean();
        require 'Views/Popup/ModalPopup.php';
    }

    public static function DisplayPopup($popupName,$Data = [""]){
        extract($Data);
        ob_start();
        $typePopup = "popupEdition";        
        require "Views/Popup/$popupName.php";
        $PopupContent = ob_get_clean();
        require 'Views/Popup/ModalPopup.php';
    }
    
}
