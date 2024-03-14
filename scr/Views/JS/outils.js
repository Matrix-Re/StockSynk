// On attend que le document soit charger
$(document).ready(function () {

  /////////////////////////////////////
  //       VARIABLE DE GLOBAL        //
  /////////////////////////////////////

  let AjaxDisponible = true;
  let expanded = false;

  /////////////////////////////////////
  //       FONCTION DE GENERAL       //
  /////////////////////////////////////

  // Permet de dérouler la ComboBox checkbox
  $(document).on("click", ".selectBox", function () {
    var checkboxes = document.getElementById("checkboxes");
    if (!expanded) {
      checkboxes.style.display = "block";
      expanded = true;
    } else {
      checkboxes.style.display = "none";
      expanded = false;
    }
  });

  function IsAjaxAvailable() {
    if (AjaxDisponible) {
      AjaxDisponible = false;
      return true
    }
    return false;
  }

  // Permet de supprimer l'élément modal du document
  $(document).on("click", "#ClosePopup", function () {
    AjaxDisponible = true;
    $("#modal").remove()
  });

  /////////////////////////////////////
  //      FONCTION DE CONNEXION      //
  /////////////////////////////////////

  // Lorsque l'utilisateur se connecte
  $("#ButtonConnexion").click(function () {
    if (IsAjaxAvailable()) {
      $.ajax({
        url: "/Connexion",
        method: "POST",
        data: { ActionAjax: "", Login: $("#Login").val(), Password: $("#Password").val() }

      }).done(function (data) {
        try {
          const Data = JSON.parse(data); // Try to parse it as JSON
          document.location.href = Data.Link;
        } catch (err) {
          $("body").append(data)
        }

      });
    }
  });

  /////////////////////////////////////
  //   FONCTION AJAX DU CATALOGUE    //
  /////////////////////////////////////

  // On affiche le formulaire du catalogue
  $(document).on("click", ".ButtonAfficherCatalogue", function () {
    $.ajax({
      url: "/Home",
      method: "POST",
      data: { ActionAjax: "AfficherCatalogue", ID_Catalogue: $(this).attr('value') }

    }).done(function (data) {
      $("body").append(data)
    });
  });

  // On envoie les informations saisie pour les enregistrés
  $(document).on("click", "#ButtonValiderCatalogue", function () {
    if (IsAjaxAvailable()) {
      $.ajax({
        url: "/Home",
        method: "POST",
        data: {
          ActionAjax: "ValiderCatalogue",
          ID_Catalogue: $(this).val(),
          Nom: $("#Nom").val(),
          UrlDescription: $("#UrlDescription").val(),
          PrixReference: $("#PrixReference").val(),
          Quantite: $("#Quantite").val()
        }

      }).done(function (data) {
        MiseAJourTableauCatalogue();
        $("#ClosePopup").click();
        $("body").append(data)
      });
    }
  });

  // On met à jour le tableau catalogue
  function MiseAJourTableauCatalogue() {
    $.ajax({
      url: "/Home",
      method: "POST",
      data: {
        ActionAjax: "MiseAJourTableauCatalogue",
        FiltreCatalogueNom: $("#FiltreCatalogueNom").val(),
        FiltreCatalogueURL: $("#FiltreCatalogueURL").val(),
        FiltreCataloguePrixReference: $("#FiltreCataloguePrixReference").val(),
        FiltreCatalogueQuantite: $("#FiltreCatalogueQuantite").val(),
        FiltreCatalogueEtat: $("#FiltreCatalogueEtat").val()
      }

    }).done(function (data) {
      $("#TableauCatalogue").empty();
      $("#TableauCatalogue").append(data)
    });
  }

  // On filtre le tableau catalogue
  $('.FiltreCatalogue').on('change', function (e) {
    MiseAJourTableauCatalogue();
  });

  // On change l'etat du catalogue
  $(document).on("click", ".ButtonEtatCatalogue", function () {
    if (IsAjaxAvailable()) {
      $.ajax({
        url: "/Home",
        method: "POST",
        data: {
          ActionAjax: "ChangeEtatCatalogue",
          ID_Catalogue: $(this).val()
        }

      }).done(function (data) {
        MiseAJourTableauCatalogue();
        $("body").append(data)
      });
    }
  });

  /////////////////////////////////////
  //     FONCTION AJAX DU QRCODE     //
  /////////////////////////////////////

  // On affiche le formulaire du qrcode  
  $(document).on("click", ".ButtonAfficherQRCode", function () {
    $.ajax({
      url: "/Home",
      method: "POST",
      data: { ActionAjax: "AfficherQRCode", ID_QRCode: $(this).attr('value') }

    }).done(function (data) {
      $("body").append(data)
    });
  });

  // On envoie les informations saisie pour les enregistrés
  $(document).on("click", "#ButtonValiderQRCode", function () {
    if (IsAjaxAvailable()) {
      $.ajax({
        url: "/Home",
        method: "POST",
        data: {
          ActionAjax: "ValiderQRCode",
          ID_QRCode: $(this).val(),
          Nom: $("#Nom").val(),
          Actif: $("#Actif").prop('checked'),
          ID_Catalogue: $("#ID_Catalogue").val()
        }

      }).done(function (data) {
        MiseAJourTableauQRCode();
        $("#ClosePopup").click();
        $("body").append(data)
      });
    }
  });

  // On met à jour le tableau qrcode
  function MiseAJourTableauQRCode() {
    $.ajax({
      url: "/Home",
      method: "POST",
      data: {
        ActionAjax: "MiseAJourTableauQRCode",
        FiltreQRCodeID: $("#FiltreQRCodeID").val(),
        FiltreQRCodeNom: $("#FiltreQRCodeNom").val(),
        FiltreQRCodeNomCatalogue: $("#FiltreQRCodeNomCatalogue").val(),
        FiltreQRCodeEtat: $("#FiltreQRCodeEtat").val()
      }

    }).done(function (data) {
      $("#TableauQRCode").empty();
      $("#TableauQRCode").append(data)
    });
  }

  // On filtre le tableau QRCode
  $('.FiltreQRCode').on('change', function (e) {
    MiseAJourTableauQRCode();
  });

  // On change l'etat du QRCode
  $(document).on("click", ".ButtonEtatQRCode", function () {
    if (IsAjaxAvailable()) {
      $.ajax({
        url: "/Home",
        method: "POST",
        data: {
          ActionAjax: "ChangeEtatQRCode",
          ID_QRCode: $(this).val()
        }

      }).done(function (data) {
        MiseAJourTableauQRCode();
        $("body").append(data)
      });
    }
  });

  /////////////////////////////////////
  //       FONCTION AJAX SCAN        //
  /////////////////////////////////////

  // On met à jour le tableau Scan
  function MiseAJourTableauScan() {
    $.ajax({
      url: "/Home",
      method: "POST",
      data: {
        ActionAjax: "MiseAJourTableauScan",
        FiltreScanDateMin: $("#FiltreScanDateMin").val(),
        FiltreScanDateMax: $("#FiltreScanDateMax").val(),
        FiltreScanNombreMin: $("#FiltreScanNombreMin").val(),
        FiltreScanNombreMax: $("#FiltreScanNombreMax").val(),
        FiltreScanNomCatalogue: $("#FiltreScanNomCatalogue").val(),
        FiltreScanNomQRCode: $("#FiltreScanNomQRCode").val()
      }

    }).done(function (data) {
      $("#TableauScan").empty();
      $("#TableauScan").append(data)
    });
  }

  // On filtre le tableau Scan
  $('.FiltreScan').on('change', function (e) {
    MiseAJourTableauScan();
  });

  /////////////////////////////////////
  //       FONCTION AJAX Vente       //
  /////////////////////////////////////

  $(document).on("click", ".AfficherDétailsVente", function () {
    $.ajax({
      url: "/Home",
      method: "POST",
      data: {
        ActionAjax: "DetailsVente",
        ID_Vente: $(this).data('value')
      }

    }).done(function (data) {
      $("body").append(data)
    });
  });

  // On met à jour le tableau Scan
  function MiseAJourTableauVente() {
    $.ajax({
      url: "/Home",
      method: "POST",
      data: {
        ActionAjax: "MiseAJourTableauVente",
        FiltreVenteID: $("#FiltreVenteID").val(),
        FiltreVenteDateMin: $("#FiltreVenteDateMin").val(),
        FiltreVenteDateMax: $("#FiltreVenteDateMax").val(),
        FiltreVenteNbProduitMin: $("#FiltreVenteNbProduitMin").val(),
        FiltreVenteNbProduitMax: $("#FiltreVenteNbProduitMax").val(),
        FiltreVentePrixTotalMin: $("#FiltreVentePrixTotalMin").val(),
        FiltreVentePrixTotalMax: $("#FiltreVentePrixTotalMax").val()
      }

    }).done(function (data) {
      $("#TableauVente").empty();
      $("#TableauVente").append(data)
    });
  }

  // On filtre le tableau Scan
  $('.FiltreVente').on('change', function (e) {
    MiseAJourTableauVente();
  });

  /////////////////////////////////////
  //      FONCTION AJAX MAGASIN      //
  /////////////////////////////////////

  // On affiche le formulaire du magasin  
  $(document).on("click", ".ButtonAfficherMagasin", function () {
    $.ajax({
      url: "/Admin",
      method: "POST",
      data: { ActionAjax: "AfficherMagasin", ID_Magasin: $(this).attr('value') }

    }).done(function (data) {
      $("body").append(data)
    });
  });

  // On envoie les informations saisie pour les enregistrés
  $(document).on("click", "#ButtonValiderMagasin", function () {
    if (IsAjaxAvailable()) {
      $.ajax({
        url: "/Admin",
        method: "POST",
        data: {
          ActionAjax: "ValiderMagasin",
          ID_Magasin: $(this).val(),
          Nom: $("#Nom").val(),
          CP: $("#CP").val(),
          Ville: $("#Ville").val()
        }

      }).done(function (data) {
        MiseAJourTableauMagasin();
        $("#ClosePopup").click();
        $("body").append(data)
      });
    }
  });

  function MiseAJourTableauMagasin() {
    $.ajax({
      url: "/Admin",
      method: "POST",
      data: {
        ActionAjax: "MiseAJourTableauMagasin",
        FiltreMagasinNom: $("#FiltreMagasinNom").val(),
        FiltreMagasinCP: $("#FiltreMagasinCP").val(),
        FiltreMagasinVille: $("#FiltreMagasinVille").val(),
        FiltreMagasinEtat: $("#FiltreMagasinEtat").val()
      }

    }).done(function (data) {
      $("#TableauMagasin").empty();
      $("#TableauMagasin").append(data)
    });
  }

  // On filtre le tableau magasin
  $('.FiltreMagasin').on('change', function (e) {
    MiseAJourTableauMagasin();
  });

  // On change l'etat du magasin
  $(document).on("click", ".ButtonEtatMagasin", function () {
    if (IsAjaxAvailable()) {
      $.ajax({
        url: "/Admin",
        method: "POST",
        data: {
          ActionAjax: "ChangeEtatMagasin",
          ID_Magasin: $(this).val()
        }

      }).done(function (data) {
        MiseAJourTableauMagasin();
        $("body").append(data)
      });
    }
  });

  /////////////////////////////////////
  //    FONCTION AJAX UTILISATEUR    //
  /////////////////////////////////////

  // On affiche le formulaire du magasin  
  $(document).on("click", ".ButtonAfficherSalarie", function () {
    $.ajax({
      url: "/Admin",
      method: "POST",
      data: { ActionAjax: "AfficherSalarie", ID_Salarie: $(this).attr('value') }

    }).done(function (data) {
      $("body").append(data)
    });
  });

  // On envoie les informations saisie pour les enregistrés
  $(document).on("click", "#ButtonValiderSalarie", function () {
    if (IsAjaxAvailable()) {
      $.ajax({
        url: "/Admin",
        method: "POST",
        data: {
          ActionAjax: "ValiderSalarie",
          ID_Salarie: $(this).val(),
          Identifiant: $("#Identifiant").val(),
          Password: $("#Password").val(),
          Status: $("#Status").val(),
          Actif: $("#Actif").val()
        }

      }).done(function (data) {
        MiseAJourTableauSalarie();
        $("#ClosePopup").click();
        $("body").append(data)
      });
    }
  });

  function MiseAJourTableauSalarie() {
    $.ajax({
      url: "/Admin",
      method: "POST",
      data: {
        ActionAjax: "MiseAJourTableauSalarie",
        FiltreSalarieID: $("#FiltreSalarieID").val(),
        FiltreSalarieNom: $("#FiltreSalarieNom").val(),
        FiltreSalarieStatus: $("#FiltreSalarieStatus").val(),
        FiltreSalarieEtat: $("#FiltreSalarieEtat").val()
      }

    }).done(function (data) {
      $("#TableauSalarie").empty();
      $("#TableauSalarie").append(data)
    });
  }

  // On filtre le tableau magasin
  $('.FiltreSalarie').on('change', function (e) {
    MiseAJourTableauSalarie();
  });

  // On change l'etat du magasin
  $(document).on("click", ".ButtonEtatSalarie", function () {
    if (IsAjaxAvailable()) {
      $.ajax({
        url: "/Admin",
        method: "POST",
        data: {
          ActionAjax: "ChangeEtatSalarie",
          ID_Salarie: $(this).val()
        }

      }).done(function (data) {
        MiseAJourTableauSalarie();
        $("body").append(data)
      });
    }
  });

  /////////////////////////////////////
  //    FONCTION AJAX PAGE VENTE     //
  /////////////////////////////////////

  // On ajoute le produit au panier
  $('#ButtonValiderProduit').on('click', function (e) {
    $.ajax({
      url: "/Qrcode",
      method: "POST",
      data: {
        ActionAjax: "AjouterProduitPanier",
        ID_Catalogue: $("#SelectionCatalogue").val(),
        Quantite: $("#Quantite").val(),
        Prix: $("#Prix").val()
      }

    }).done(function (data) {
      $("body").append(data)
      MiseAJourPanier()
    });
  });

  // On met à jour le contenue du panier
  function MiseAJourPanier() {
    $.ajax({
      url: "/Qrcode",
      method: "POST",
      data: {
        ActionAjax: "AfficherPanier"
      }

    }).done(function (data) {
      $("#ContenuePanier").empty();
      $("#ContenuePanier").append(data)
    });

  }

  // On supprime le produit du panier
  $(document).on("click", ".ButtonSupprimerProduitPanier", function () {
    $.ajax({
      url: "/Qrcode",
      method: "POST",
      data: {
        ActionAjax: "SupprimerProduitPanier",
        IndicePanier: $(this).val()
      }

    }).done(function (data) {
      MiseAJourPanier();
    });
  });

  // On modifie le produit du panier
  $(document).on("click", ".ButtonModifierProduitPanier", function () {
    IndiceProduit = $(this).val()
    $.ajax({
      url: "/Qrcode",
      method: "POST",
      data: {
        ActionAjax: "ModifierProduitPanier",
        IndicePanier: IndiceProduit
      }

    }).done(function (data) {
      try {
        const Data = JSON.parse(data); // Try to parse it as JSON
        // On remplit le formulaire avec les valeurs retourné
        $("#Quantite").val(Data.Quantite)
        $("#Prix").val(Data.PU)
        $("#SelectionCatalogue").val(Data.ID_Catalogue)
        $("#PrixTotal").text("Prix total : " + Data.Quantite * Data.PU + " €")

        // On cherche le bonton supprimer associer au produit et on click dessus
        var monBouton = $('.ButtonSupprimerProduitPanier').filter(function () {
          return $(this).val() === IndiceProduit;
        });
        monBouton.click()


      } catch (err) {
        $("body").append(data)
      }
    });
  });

  $('#SelectionCatalogue').on('change', function (e) {
    $.ajax({
      url: "/Qrcode",
      method: "POST",
      data: {
        ActionAjax: "GetPrixProduit",
        ID_Catalogue: $(this).val()
      }

    }).done(function (data) {
      try {
        const Data = JSON.parse(data); // Try to parse it as JSON
        $("#Prix").val(Data.PU)
      }
      catch (err) {
        $("body").append(data)
      }
    });
  });

});