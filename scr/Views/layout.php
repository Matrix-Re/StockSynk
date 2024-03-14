<html>

<head>
     <meta charset="utf-8" />
     <title><?= $title ?></title>
     <!-- BIBLIOTHEQUES -->
     <link href="<?= UrlSite ?>Views/CSS/bootstrap/bootstrap.css" rel="stylesheet">     
     <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
     <!-- RESSOURCES DU SITE -->
     <link href="<?= UrlSite ?>Views/CSS/style.css" rel="stylesheet">
     <script src="<?= UrlSite ?>Views/JS/outils.js"></script>
</head>

<body class="d-flex flex-column min-vh-100">

     <?= $content ?>

</body>

</html>