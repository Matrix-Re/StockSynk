<?php

if (!empty($qrcode)) {
     $qrcode->IncrementScan();
     $Link = $qrcode->GetURLCatalogue();
     echo "redirection vers  : <b>" . $Link . "</b><br>";
     echo "<script>
               setTimeout(function () { 
                    window.location.href= '" . $Link . "'; 
               },5000);
          </script>";
}

?>

<?php require './Views/Champs/Footer.php'; ?>