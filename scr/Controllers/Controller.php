<?php
/**
 * Class Controller
 *
 * This class provides methods for rendering views and displaying popups.
 */
abstract class Controller{

    /**
     * Render a view.
     *
     * @param string $View The name of the view to render.
     * @param string $NomPage The name of the page.
     * @param array $Data (optional) Data to be passed to the view.
     */
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

    /**
     * Display a message in a popup.
     *
     * @param string $PopupTitle The title of the popup.
     * @param string $PopupMessage The message to display in the popup.
     */
    public static function Message($PopupTitle,$PopupMessage){
        ob_start();
        $typePopup = "popupInformation";
        require 'Views/Popup/popupInformation.php';
        $PopupContent = ob_get_clean();
        require 'Views/Popup/ModalPopup.php';
    }

    /**
     * Display a popup.
     *
     * @param string $popupName The name of the popup to display.
     * @param array $Data (optional) Data to be passed to the popup.
     */
    public static function DisplayPopup($popupName,$Data = [""]){
        extract($Data);
        ob_start();
        $typePopup = "popupEdition";        
        require "Views/Popup/$popupName.php";
        $PopupContent = ob_get_clean();
        require 'Views/Popup/ModalPopup.php';
    }
    
}
