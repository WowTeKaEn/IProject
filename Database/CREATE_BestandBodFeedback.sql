--Create Bestand
CREATE TABLE Bestand (

filenaam NVARCHAR(50) NOT NULL PRIMARY KEY,
voorwerpnummer NUMERIC(10) NOT NULL,
	CONSTRAINT FK_VoorwerpNummer FOREIGN KEY Bestand.voorwerpnummer
    REFERENCES Voorwerp.voorwerpnummer
)

-- Create Bod
CREATE TABLE Bod (

voorwerpnummer NUMERIC(10) NOT NULL,
bodBedrag FLOAT(5) NOT NULL,
CONSTRAINT PK_BodBedrag PRIMARY KEY (voorwerpnummer,bodbedrag),
gebruikersnaam NVARCHAR(20) NOT NULL,
bodDag DATE NOT NULL,
bodtijdstip TIME NOT NULL,
	CONSTRAINT FK_VoorwerpNummer FOREIGN KEY Bod.voorwerpnummer
    REFERENCES Voorwerp.voorwerpnummer
	CONSTRAINT FK_Gebruikersnaam FOREIGN KEY Bod.gebruikersnaam
    REFERENCES Gebruiker.gebruikersnaam

)

--Create Feedback
CREATE TABLE Feedback (

commentaar NVARCHAR(200),
datum DATE NOT NULL,
feedbackSoortNaam NVARCHAR(8) NOT NULL,
koperVerkoper NVARCHAR(8) NOT NULL,
tijdAanduiding TIME NOT NULL,
voorwerpnummer NUMERIC(10) NOT NULL,
CONSTRAINT PK_VoorwerpGebruiker PRIMARY KEY (voorwerpnummer,koperVerkoper),
CONSTRAINT FK_Voorwerpnummer FOREIGN KEY Feedback.voorwerpnummer
REFERENCES Voorwerp.voorwerpnummer,
CONSTRAINT CHK_SoortNaam CHECK (feedbackSoortNaam='negatief' OR 'positief' OR 'neutraal');
CONSTRAINT CHK_SoortKoperVerkoper CHECK (koperVerkoper='koper' OR 'verkoper');

)