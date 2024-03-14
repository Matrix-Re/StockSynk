<?php
// On définit le lien du site
define('UrlSite', "http://" . $_SERVER['SERVER_NAME'] . "/");

require './Controllers/Router.php';

$routeur = new Router();
$routeur->routeReq();