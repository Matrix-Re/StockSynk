<?php

$pNomCatalogue = "%";
$pNomQRCode = "%";
$Filtre = "";

if (!empty($_POST['FiltreNomCatalogueScan'])) {
     $pNomCatalogue = "%" . $_POST['FiltreNomCatalogueScan'] . "%";
}
if (!empty($_POST['FiltreNomQRCodeScan'])) {
     $pNomQRCode = "%" . $_POST['FiltreNomQRCodeScan'] . "%";
}
if (!empty($_POST['FiltreDateMinScan'])) {
     $Filtre .= " AND DateScan >= '" . $_POST['FiltreDateMinScan'] . "'";
}
if (!empty($_POST['FiltreDateMaxScan'])) {
     $Filtre .= " AND DateScan <= '" . $_POST['FiltreDateMaxScan'] . "'";
}
if (!empty($_POST['FiltreNombreMinScan'])) {
     $Filtre .= " AND NombreScan >= " . $_POST['FiltreNombreMinScan'];
}
if (!empty($_POST['FiltreNombreMaxScan'])) {
     $Filtre .= " AND NombreScan <= " . $_POST['FiltreNombreMaxScan'];
}

$parameters = array($_SESSION['Connexion']->__get("ID_Store"),$pNomCatalogue,$pNomQRCode);

$reqSelect = "SELECT DISTINCT DateScan, NombreScan, NomCatalogue, NomQRCode
               FROM scan, catalogue, qrcode, proposer
               WHERE
               scan.ID_Catalogue = catalogue.ID_Catalogue
               AND               
               catalogue.ID_Catalogue = proposer.ID_Catalogue
               AND
               scan.ID_QRCode = qrcode.ID_QRCode
               AND
               QRCode.ID_Magasin = ?
               AND
               NomCatalogue LIKE ?
               AND
               NomQRCode LIKE ? $Filtre"

?>

<table class="text-center table">
     <thead>
          <tr>
               <th>Date</th>
               <th>Nombre de scan</th>
               <th>Nom Catalogue</th>
               <th>Nom QRCode</th>
          </tr>
     </thead>
     <tbody>
          <?php foreach (Model::ExecuteQuery($reqSelect,$parameters) as $Scan) { ?>

                    <tr>
                         <th><?= $Scan['DateScan'] ?></th>
                         <th><?= $Scan['NombreScan'] ?></th>
                         <th><?= $Scan['NomCatalogue'] ?></th>
                         <th><?= $Scan['NomQRCode'] ?></th>
                    </tr>

          <?php } ?>
     </tbody>
</table>