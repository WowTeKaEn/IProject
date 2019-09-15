CREATE TABLE Verkoper (
Gebruiker NVARCHAR(20) NOT NULL,
Bank NVARCHAR(20),
Bankrekening CHAR(34),
ControleOptie NVARCHAR(20) NOT NULL, 
--Naam aangepast 

Creditcard CHAR(19),

CONSTRAINT PK_Gebruiker PRIMARY KEY (Gebruiker),
CONSTRAINT FK_VerkoperNaam FOREIGN KEY (Gebruiker)
	REFERENCES Gebruiker (gebruikersnaam),
CONSTRAINT CHK_ControleOptie
	CHECK (ControleOptie = 'Creditcard' OR ControleOptie = 'Post')
)

CREATE TABLE Voorwerp (
voorwerpnummer INT IDENTITY(1,1) NOT NULL,
titel NVARCHAR(50) NOT NULL,
beschrijving NVARCHAR(MAX) NOT NULL,
Startprijs NUMERIC(6,2) NOT NULL,
Betalingswijze NVARCHAR(10) NOT NULL,
betalingsinstructie NVARCHAR(MAX),
plaatsnaam NVARCHAR(30) NOT NULL,
Land NVARCHAR(20) NOT NULL,
Looptijd TINYINT NOT NULL DEFAULT 7,
LooptijdbeginDag DATE NOT NULL,
LooptijdbeginTijdstip TIME NOT NULL,
Verzendkosten NUMERIC (5,2),
verzendinstructies NVARCHAR(MAX),
Verkoper NVARCHAR(20) NOT NULL,
Koper NVARCHAR(20),
LooptijdeindeDag DATE NOT NULL,
LooptijdeindeTijdstip TIME NOT NULL,
Veilinggesloten BIT NOT NULL,
--Naam aangepast

Verkoopprijs NUMERIC(6,2),

CONSTRAINT PK_voorwerpnummer PRIMARY KEY (voorwerpnummer),
CONSTRAINT FK_verkoper FOREIGN KEY (Verkoper)
	REFERENCES Verkoper (Gebruiker),
CONSTRAINT FK_voorwerpKoper FOREIGN KEY (Koper)
	REFERENCES Gebruiker (gebruikersnaam)
)

CREATE TABLE VoorwerpInRubriek (
--Naam aangepast
Voorwerp INT IDENTITY(1,1) NOT NULL,
RubriekOpLaagsteNiveau INT NOT NULL,
--Naam aangepast

CONSTRAINT PK_voorwerpnummer PRIMARY KEY (Voorwerp, RubriekOpLaagsteNiveau),
CONSTRAINT FK_voorwerpNummer FOREIGN KEY (Voorwerp) 
	REFERENCES Voorwerp(voorwerpnummer),
CONSTRAINT FK_rubriekNummer FOREIGN KEY (RubriekOpLaagsteNiveau) 
	REFERENCES Rubriek(rubrieknummer)
)

CREATE TABLE Vraag (
vraagnummer INT IDENTITY(1,1) NOT NULL,
tekstVraag NVARCHAR(50) NOT NULL,
--Naam aangepast

CONSTRAINT PK_voorwerpnummer PRIMARY KEY (vraagnummer)
)