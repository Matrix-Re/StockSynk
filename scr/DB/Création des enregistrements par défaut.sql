INSERT INTO `salarie` (`Identifiant`, `Password`, `Status`) VALUES ('Jean', '$2y$10$PwFSaOzeN3nvCIdaQzIXsupzdRNOXKCtk9xq5uBi3ingRdEmt8RO.', 'Administrateur');

INSERT INTO `magasin` (`NomMagasin`, `CodePostal`, `Ville`, `Actif`) VALUES ('ODYSSEUM', '34000', 'Montpellier', True);

INSERT INTO `travailler` (`ID_Magasin`, `ID_Salarie`) VALUES ('1', '1')