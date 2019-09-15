/* Create script voor tabellen uit LRS */
USE iproject26
GO


/* Tabel Gebruiker, Gebruikertelefoon & Rubriek gemaakt door Danny */
CREATE TABLE Gebruiker
(
	gebruikersnaam NVARCHAR(20) NOT NULL,
	voornaam NVARCHAR(255) NOT NULL,
	achternaam NVARCHAR(255) NOT NULL,
	/* In appendix D stond een max aantal voor de voornaam van 5, maar dat is natuurlijk veel te kort.
	Na googlen kwam ik op deze link https://www.w3.org/International/questions/qa-personal-names en hier staat dat in veel verschillende culturen lange namen zijn dus hebben wij gekozen voor een hoog maximum aantal voor de voor en achternaam.*/
	adresregel1 NVARCHAR(255) NOT NULL,
	adresregel2 NVARCHAR(255) NULL,
	/* In appendix D stond een max aantal voor de adresregels van 15, maar dat is natuurlijk te kort.
	Online hebben wij geen duidelijk maximum aantal kunnen vinden dus kiezen wij maar weer de veilige optie en dat is een heel hoog maximum aantal.*/
	postcode NVARCHAR(18) NOT NULL,
	/* In appendix D stond een maximum aantal van 7 maar het is in Chili mogelijk om een postcode te hebben van 18 characters lang dus kiezen wij voor 18.
	bron: https://kitefaster.com/2017/05/03/maximum-string-length-popular-database-fields/ */
	plaatsnaam NVARCHAR(189) NOT NULL,
	/* In appendix D stond een maximum lengte van 9 maar er is een stad in Bankok met een lengte van 189 characters lang dus moeten we hier rekening mee houden.
	Bron: https://kitefaster.com/2017/05/03/maximum-string-length-popular-database-fields/ */
	landnaam NVARCHAR(90) NOT NULL,
	/* In appendix D stond een maximum lengte van 12 maar de oude Arabische naam van Libie is 90 characters lang dus moeten we daar rekening mee houden.
	Bron: https://kitefaster.com/2017/05/03/maximum-string-length-popular-database-fields/ */
	geboortedatum DATE NOT NULL,
	emailadres NVARCHAR(254) NOT NULL,
	/* In appendix D stond een maximum lengte van 18 maar dat is natuurlijk te kort, wij hebben voor 254 characters gekozen aangezien dat de afgsproken maximum lengte is van een e-mailadres
	Bron: https://7php.com/the-maximum-length-limit-of-an-email-address-is-254-not-320/ */
	wachtwoord NVARCHAR(60) NOT NULL,
	/* In appendix D stond een maximum lengte van 9 maar daar gaan gehashte wachtwoorden nooit inpassen.
	Wij hebben gekozen voor een lengte van 60 characters lang omdat wachtwoorden die gehasht zijn met bcrypt 60 characters lang zijn.*/
	vraagnummer INT NOT NULL,
	antwoordtekst NVARCHAR(255) NOT NULL,
	/* In appendix D stond een maximum lengte van 6 maar je weet nooit zeker dat er antwoord word gegeven dat maar zo kort is dus hebben wij besloten om een heel hoog aantal characters lang mogelijk te maken. */
	verkoper NVARCHAR(4) NOT NULL
    CONSTRAINT PK_Gebruiker PRIMARY KEY(gebruikersnaam)
)
GO

CREATE TABLE Gebruikerstelefoon
(
	volgnr INT NOT NULL,
	gebruikersnaam NVARCHAR(20) NOT NULL,
	telefoonnummer NVARCHAR(15) NOT NULL
	/* In appendix D stond een maximum lengte van 11 maar de officiele afgesproken maximum lengte van een telefoonnummer is 15 characters lang dus hanteren we dat.
	Bron: https://en.wikipedia.org/wiki/Telephone_numbering_plan */
	CONSTRAINT PK_Gebruikerstelefoon PRIMARY KEY(volgnr,gebruikersnaam)
	CONSTRAINT FK_Gebruiker_Gebruikerstelefoon FOREIGN KEY(gebruikersnaam) REFERENCES gebruiker(gebruikersnaam)
)
GO

CREATE TABLE Rubriek
(
	rubrieknummer INT IDENTITY(1,1),
	rubrieknaam NVARCHAR(24) NOT NULL,
	parent_rubriek INT NULL
	/* Volgnr weggelaten aangezien dat overbodig is */
	CONSTRAINT PK_Rubriek PRIMARY KEY(rubrieknummer)
	CONSTRAINT FK_Rubriek_Parent_Rubriek FOREIGN KEY(parent_rubriek) REFERENCES Rubriek(rubrieknummer)
)
GO

