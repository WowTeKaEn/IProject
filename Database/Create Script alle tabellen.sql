/* 
Create script voor tabellen uit LRS
- Tabellen Bestand, Bod & Feedback gemaakt door Redwan
- Tabellen Gebruiker, Gebruikertelefoon & Rubriek gemaakt door Danny 
- Tabellen Verkoper, Voorwerp, VoorwerpInRubriek & Vraag gemaakt door Niels
*/
DROP TABLE IF EXISTS Feedback
DROP TABLE IF EXISTS Bod
DROP TABLE IF EXISTS Bestand
DROP TABLE IF EXISTS Vraag
DROP TABLE IF EXISTS VoorwerpInRubriek
DROP TABLE IF EXISTS Voorwerp
DROP TABLE IF EXISTS Verkoper
DROP TABLE IF EXISTS Rubriek
DROP TABLE IF EXISTS Gebruikerstelefoon
DROP TABLE IF EXISTS Gebruiker
DROP TABLE IF EXISTS Meldingen
GO

CREATE TABLE Gebruiker
(
	gebruikersnaam NVARCHAR(200) NOT NULL,
	voornaam NVARCHAR(100) NULL,
	achternaam NVARCHAR(100) NULL,
	/* In appendix D stond een max aantal voor de voornaam van 5, maar dat is natuurlijk veel te kort.
	Wij hebben voor een lengte van 100 gekozen. */
	adresregel1 NVARCHAR(255) NULL,
	adresregel2 NVARCHAR(255) NULL,
	/* In appendix D stond een max aantal voor de adresregels van 15, maar dat is natuurlijk te kort.
	Online hebben wij geen duidelijk maximum aantal kunnen vinden dus kiezen wij maar weer de veilige optie en dat is een heel hoog maximum aantal.*/
	postcode NVARCHAR(18) NOT NULL,
	/* In appendix D stond een maximum aantal van 7 maar het is in Chili mogelijk om een postcode te hebben van 18 characters lang dus kiezen wij voor 18.
	bron: https://kitefaster.com/2017/05/03/maximum-string-length-popular-database-fields/ */
	plaatsnaam NVARCHAR(189) NULL,
	/* In appendix D stond een maximum lengte van 9 maar er is een stad in Bankok met een lengte van 189 characters lang dus moeten we hier rekening mee houden.
	Bron: https://kitefaster.com/2017/05/03/maximum-string-length-popular-database-fields/ */
	landnaam NVARCHAR(90) NOT NULL,
	/* In appendix D stond een maximum lengte van 12 maar de oude Arabische naam van Libie is 90 characters lang dus moeten we daar rekening mee houden.
	Bron: https://kitefaster.com/2017/05/03/maximum-string-length-popular-database-fields/ */
	geboortedatum DATE NULL,
	emailadres NVARCHAR(254) NULL,
	/* In appendix D stond een maximum lengte van 18 maar dat is natuurlijk te kort, wij hebben voor 254 characters gekozen aangezien dat de afgsproken maximum lengte is van een e-mailadres
	Bron: https://7php.com/the-maximum-length-limit-of-an-email-address-is-254-not-320/ */
	wachtwoord NVARCHAR(60) NULL,
	/* In appendix D stond een maximum lengte van 9 maar daar gaan gehashte wachtwoorden nooit inpassen.
	Wij hebben gekozen voor een lengte van 60 characters lang omdat wachtwoorden die gehasht zijn met bcrypt 60 characters lang zijn.*/
	vraagnummer INT NULL,
	antwoordtekst NVARCHAR(255) NULL,
	/* In appendix D stond een maximum lengte van 6 maar je weet nooit zeker dat er antwoord word gegeven dat maar zo kort is dus hebben wij besloten om een heel hoog aantal characters lang mogelijk te maken. */
	verkoper BIT NOT NULL,
	/* Als de source 1 (true) is dan is het uit een data-batch, als het 0 (false) is dan heeft de geburiker zich geregistreerd op de website zelf */
	source BIT NOT NULL,
	verificatie BIT NOT NULL,
	geblokkeerd BIT NULL
    CONSTRAINT PK_Gebruiker PRIMARY KEY(gebruikersnaam)
)
GO

CREATE TABLE Gebruikerstelefoon
(
	volgnr INT IDENTITY(1,1) NOT NULL,
	gebruikersnaam NVARCHAR(200) NOT NULL,
	telefoonnummer NVARCHAR(15) NOT NULL
	/* In appendix D stond een maximum lengte van 11 maar de officiele afgesproken maximum lengte van een telefoonnummer is 15 characters lang dus hanteren we dat.
	Bron: https://en.wikipedia.org/wiki/Telephone_numbering_plan */
	CONSTRAINT PK_Gebruikerstelefoon PRIMARY KEY(volgnr,gebruikersnaam),
	CONSTRAINT FK_Gebruiker_Gebruikerstelefoon FOREIGN KEY(gebruikersnaam) 
		REFERENCES gebruiker(gebruikersnaam)
)
GO

CREATE TABLE Rubriek
(
	rubrieknummer INT IDENTITY(1,1),
	rubrieknaam NVARCHAR(100) NOT NULL,
	parent_rubriek INT NULL,
	source BIT NOT NULL

	/* Volgnr weggelaten aangezien dat overbodig is */
	CONSTRAINT PK_Rubriek PRIMARY KEY(rubrieknummer),
	CONSTRAINT FK_Rubriek_Parent_Rubriek FOREIGN KEY(parent_rubriek) 
		REFERENCES Rubriek(rubrieknummer)
)
GO

CREATE TABLE Verkoper (
	gebruikersnaam NVARCHAR(200) NOT NULL,
	banknaam NVARCHAR(20) NULL,
	rekeningnummer NVARCHAR(34) NULL,
	controle_optie_naam NVARCHAR(20) NULL,
	creditcardnummer NVARCHAR(19) NULL,
	source BIT NOT NULL

	CONSTRAINT PK_Verkoper PRIMARY KEY (gebruikersnaam),
	CONSTRAINT FK_Gebruiker_Verkoper FOREIGN KEY (gebruikersnaam)
		REFERENCES Gebruiker(gebruikersnaam),
	CONSTRAINT CHK_ControleOptie
		CHECK (controle_optie_naam = 'Creditcard' OR controle_optie_naam = 'Post')
)
GO

CREATE TABLE Voorwerp (
	voorwerpnummer BIGINT IDENTITY(1,1),
	titel NVARCHAR(100) NOT NULL,
	beschrijving NVARCHAR(MAX) NOT NULL,
	startprijs NUMERIC(10,2) NULL,
	betalingswijze_naam NVARCHAR(10) NULL,
	betalingsinstructie NVARCHAR(500) NULL,
	plaatsnaam NVARCHAR(189) NULL,
	/* In appendix D stond een maximum lengte van 9 maar er i,s een stad in Bankok met een lengte van 189 characters lang dus moeten we hier rekening mee houden.
	Bron: https://kitefaster.com/2017/05/03/maximum-string-length-popular-database-fields/ */
	landnaam NVARCHAR(90) NOT NULL,
	/* In appendix D stond een maximum lengte van 12 maar de oude Arabische naam van Libie is 90 characters lang dus moeten we daar rekening mee houden.
	Bron: https://kitefaster.com/2017/05/03/maximum-string-length-popular-database-fields/ */
	looptijd TINYINT NULL DEFAULT 7,
	looptijd_start_datum DATE NULL,
	looptijd_start_tijd TIME NULL,
	verzendkosten NUMERIC (10,2) NULL,
	verzendinstructies NVARCHAR(250) NULL,
	verkoper_gebruikersnaam NVARCHAR(200) NOT NULL,
	koper_gebruikersnaam NVARCHAR(200) NULL,
	looptijd_einde_datum DATE NULL,
	looptijd_einde_tijd TIME NULL,
	veiling_gesloten BIT NOT NULL,
	verkoopprijs NUMERIC(10,2) NULL,
	thumbnail NVARCHAR(100) NULL,
	/* Als het voorwerp 1 (true) is dan is het uit een data-batch, als het 0 (false) is dan heeft een geburiker zelf het voorwerp aangeboden door een veiling te maken */
	source BIT NOT NULL,
	conditie NVARCHAR(75) NULL


	CONSTRAINT PK_Voorwerp PRIMARY KEY (voorwerpnummer),
	CONSTRAINT FK_Verkoper_Voorwerp FOREIGN KEY (verkoper_gebruikersnaam)
		REFERENCES Verkoper(gebruikersnaam),
	CONSTRAINT FK_Koper_Voorwerp FOREIGN KEY (koper_gebruikersnaam)
		REFERENCES Gebruiker (gebruikersnaam)
)
GO

CREATE TABLE VoorwerpInRubriek (
	voorwerpnummer BIGINT NOT NULL,
	rubrieknummer INT NOT NULL,
	source BIT NOT NULL

	CONSTRAINT PK_VoorwerpInRubriek PRIMARY KEY (voorwerpnummer, rubrieknummer),
	CONSTRAINT FK_Voorwerp_VoorwerpInRubriek FOREIGN KEY (voorwerpnummer) 
		REFERENCES Voorwerp(voorwerpnummer),
	CONSTRAINT FK_Rubriek_VoorwerpInRubriek FOREIGN KEY (rubrieknummer) 
		REFERENCES Rubriek(rubrieknummer)
)
GO

CREATE TABLE Vraag (
	vraagnummer INT IDENTITY(1,1) NOT NULL,
	tekst_vraag NVARCHAR(150) NOT NULL,

	CONSTRAINT PK_Vraag PRIMARY KEY (vraagnummer)
)
GO

--Create Bestand
CREATE TABLE Bestand (
	filenaam NVARCHAR(100) NOT NULL,
	voorwerpnummer BIGINT NOT NULL,
	source BIT NOT NULL

	CONSTRAINT PK_Bestand PRIMARY KEY(filenaam),
	CONSTRAINT FK_Voorwerp_Bestand FOREIGN KEY(voorwerpnummer)
		REFERENCES Voorwerp(voorwerpnummer)
)
GO

-- Create Bod
CREATE TABLE Bod (
	voorwerpnummer BIGINT NOT NULL,
	bod_bedrag NUMERIC(10,2) NOT NULL,
	gebruikersnaam NVARCHAR(200) NOT NULL,
	datum DATE NOT NULL,
	tijd TIME NOT NULL,
	CONSTRAINT PK_Bod PRIMARY KEY (voorwerpnummer,bod_bedrag),
	CONSTRAINT FK_Voorwerp_Bod FOREIGN KEY(voorwerpnummer)
		REFERENCES Voorwerp(voorwerpnummer),
	CONSTRAINT FK_Gebruiker_Bod FOREIGN KEY(gebruikersnaam)
		REFERENCES Gebruiker(gebruikersnaam)

)
GO

--Create Feedback
CREATE TABLE Feedback (
	voorwerpnummer BIGINT NOT NULL,
	door_koper BIT NOT NULL,
	feedback_soort_naam NVARCHAR(8) NOT NULL,
	datum DATE NOT NULL,
	tijd TIME NOT NULL,
	commentaar NVARCHAR(500) NULL,
	rating TINYINT NULL,

	CONSTRAINT PK_Feedback PRIMARY KEY (voorwerpnummer,door_koper),
	CONSTRAINT FK_Voorwerp_Feedback FOREIGN KEY (voorwerpnummer)
	REFERENCES Voorwerp(voorwerpnummer),
	CONSTRAINT CHK_SoortNaam CHECK (feedback_soort_naam IN('negatief','positief','neutraal'))
)
GO
GO

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[FN_GeefPrijs]')
AND type in (N'FN', N'IF',N'TF', N'FS', N'FT'))
DROP FUNCTION [dbo].[FN_GeefPrijs]
GO
CREATE FUNCTION FN_GeefPrijs(@voorwerpnummer BIGINT)
RETURNS NUMERIC(10,2)
BEGIN
	DECLARE @min_prijs NUMERIC(10,2),
	@max_bod NUMERIC(10,2)
	SELECT @max_bod = MAX(bod_bedrag)
	FROM Bod
	WHERE voorwerpnummer = @voorwerpnummer;
	SELECT @min_prijs = startprijs FROM Voorwerp
	WHERE voorwerpnummer = @voorwerpnummer;
	RETURN CASE WHEN @max_bod IS NOT NULL THEN @max_bod
	ELSE @min_prijs END
END
GO

--Create Meldingen
CREATE TABLE dbo.Meldingen (
	gebruikersnaam nvarchar(100) not null,
    titel nvarchar(50) not null,
    datum datetime not null,
    tekst nvarchar(450) not null,
);
GO