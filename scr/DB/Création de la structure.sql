CREATE TABLE Magasin(
    ID_Magasin INT NOT NULL AUTO_INCREMENT,
    NomMagasin VARCHAR(50),
    CodePostal VARCHAR(2),
    Ville VARCHAR(50),
    Actif BINARY DEFAULT 1,
    PRIMARY KEY(ID_Magasin)
);

CREATE TABLE Salarie(
    ID_Salarie INT NOT NULL AUTO_INCREMENT,
    Identifiant VARCHAR(50) NOT NULL,
    Password VARCHAR(100),
    Status VARCHAR(14),
    Actif BINARY DEFAULT 1,
    PRIMARY KEY(ID_Salarie),
    UNIQUE(Identifiant)
);

CREATE TABLE Catalogue(
    ID_Catalogue INT NOT NULL AUTO_INCREMENT,
    NomCatalogue VARCHAR(50),
    URLCatalogue VARCHAR(2083),
    PrixReference DOUBLE,
    Actif BINARY DEFAULT 1,
    PRIMARY KEY(ID_Catalogue)
);

CREATE TABLE Vente(
    ID_Vente INT NOT NULL AUTO_INCREMENT,
    DateVente DATE,
    ID_Magasin INT NOT NULL,
    PRIMARY KEY(ID_Vente),
    FOREIGN KEY(ID_Magasin) REFERENCES Magasin(ID_Magasin)
);

CREATE TABLE QRCode(
    ID_QRCode INT NOT NULL AUTO_INCREMENT,
    NomQRCode VARCHAR(50),
    Actif BINARY,
    ID_Catalogue INT NOT NULL,
    ID_Magasin INT,
    PRIMARY KEY(ID_QRCode),
    FOREIGN KEY(ID_Catalogue) REFERENCES Catalogue(ID_Catalogue),
    FOREIGN KEY(ID_Magasin) REFERENCES Magasin(ID_Magasin)
);

CREATE TABLE Scan(
    ID_Scan INT NOT NULL AUTO_INCREMENT,
    DateScan DATE,
    NombreScan INT,
    ID_Catalogue INT,
    ID_QRCode INT,
    PRIMARY KEY(ID_Scan),
    FOREIGN KEY(ID_Catalogue) REFERENCES Catalogue(ID_Catalogue),
    FOREIGN KEY(ID_QRCode) REFERENCES QRCode(ID_QRCode)
);

CREATE TABLE PROPOSER(
    ID_Magasin INT NOT NULL AUTO_INCREMENT,
    ID_Catalogue INT,
    Quantite INT,
    PRIMARY KEY(ID_Magasin, ID_Catalogue),
    FOREIGN KEY(ID_Magasin) REFERENCES Magasin(ID_Magasin),
    FOREIGN KEY(ID_Catalogue) REFERENCES Catalogue(ID_Catalogue)
);

CREATE TABLE TRAVAILLER(
    ID_Magasin INT NOT NULL AUTO_INCREMENT,
    ID_Salarie INT,
    PRIMARY KEY(ID_Magasin, ID_Salarie),
    FOREIGN KEY(ID_Magasin) REFERENCES Magasin(ID_Magasin),
    FOREIGN KEY(ID_Salarie) REFERENCES Salarie(ID_Salarie)
);

CREATE TABLE REPRESENTE(
    ID_Catalogue INT NOT NULL AUTO_INCREMENT,
    ID_Vente INT,
    PrixUnitaire DOUBLE,
    Quantite INT,
    PRIMARY KEY(ID_Catalogue, ID_Vente),
    FOREIGN KEY(ID_Catalogue) REFERENCES Catalogue(ID_Catalogue),
    FOREIGN KEY(ID_Vente) REFERENCES Vente(ID_Vente)
);
