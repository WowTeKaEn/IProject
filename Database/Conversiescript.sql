-- Tabellen leeggooien start
DELETE FROM Bestand
WHERE source = 1
GO
DELETE FROM VoorwerpInRubriek
WHERE source = 1
GO
DELETE FROM Rubriek
WHERE source = 1
GO
DELETE FROM Voorwerp
WHERE source = 1
GO
DELETE FROM Verkoper
WHERE source = 1
GO
DELETE FROM Gebruiker
WHERE source = 1
GO
-- Tabellen leegooien einde



-- Gebruikers insert start
INSERT INTO Gebruiker
(gebruikersnaam,postcode,landnaam,verkoper,source)
SELECT DISTINCT Username,
Postalcode,
SUBSTRING(Location,CHARINDEX(',',Location),90 - CHARINDEX(',',Location)),
CASE WHEN (SELECT COUNT(*) FROM Items WHERE Items.Verkoper = Username) > 0 THEN 1 ELSE 0 END,
1
FROM Users
GO 
-- Gebruikers insert einde



-- Verkoper insert start
INSERT INTO Verkoper
(gebruikersnaam,source)
SELECT DISTINCT G.gebruikersnaam, 1
FROM Gebruiker G
WHERE G.verkoper = 1 AND G.source = 1
GO
-- Verkoper insert einde


-- Voorwerp insert start
-- Functie om de HTML uit de beschrijving te halen
IF OBJECT_ID('FN_StripScript') IS NOT NULL
DROP FUNCTION [FN_StripScript]
GO
CREATE FUNCTION [dbo].[FN_StripScript] (@HTMLText NVARCHAR(MAX))
RETURNS NVARCHAR(MAX) AS
BEGIN
    DECLARE @Start INT
    DECLARE @End INT
    DECLARE @Length INT
	SET @Start = CHARINDEX('<script',@HTMLText)
	SET @End = CHARINDEX('/script>',@HTMLText,@Start)
	SET @Length = (@End - @Start) + 8
    WHILE @Start > 0 AND @End > 0 AND @Length > 0
    BEGIN
        SET @HTMLText = STUFF(@HTMLText,@Start,@Length,'')
		SET @Start = CHARINDEX('<script',@HTMLText)
		SET @End = CHARINDEX('/script>',@HTMLText,@Start)
		SET @Length = (@End - @Start) + 8
	END
    RETURN LTRIM(RTRIM(@HTMLText))
END
GO

IF OBJECT_ID('FN_StripStyle') IS NOT NULL
DROP FUNCTION [FN_StripStyle]
GO
CREATE FUNCTION [dbo].[FN_StripStyle] (@HTMLText NVARCHAR(MAX))
RETURNS NVARCHAR(MAX) AS
BEGIN
    DECLARE @Start INT
    DECLARE @End INT
    DECLARE @Length INT
	SET @Start = CHARINDEX('<style',@HTMLText)
	SET @End = CHARINDEX('/style>',@HTMLText,@Start)
	SET @Length = (@End - @Start) + 7
    WHILE @Start > 0 AND @End > 0 AND @Length > 0
    BEGIN
        SET @HTMLText = STUFF(@HTMLText,@Start,@Length,'')
		SET @Start = CHARINDEX('<style',@HTMLText)
		SET @End = CHARINDEX('/style>',@HTMLText,@Start)
		SET @Length = (@End - @Start) + 7
    END
    RETURN LTRIM(RTRIM(@HTMLText))
END
GO

IF OBJECT_ID('FN_StripHTML') IS NOT NULL
DROP FUNCTION [FN_StripHTML]
GO
CREATE FUNCTION [dbo].[FN_StripHTML] (@HTMLText NVARCHAR(MAX))
RETURNS NVARCHAR(MAX) AS
BEGIN
    DECLARE @Start INT
    DECLARE @End INT
    DECLARE @Length INT
	SET @Start = CHARINDEX('<',@HTMLText)
	SET @End = CHARINDEX('>',@HTMLText,@Start)
	SET @Length = (@End - @Start) + 1
    WHILE @Start > 0 AND @End > 0 AND @Length > 0
    BEGIN
        SET @HTMLText = STUFF(@HTMLText,@Start,@Length,'')
		SET @Start = CHARINDEX('<',@HTMLText)
		SET @End = CHARINDEX('>',@HTMLText,@Start)
		SET @Length = (@End - @Start) + 1
    END
    RETURN LTRIM(RTRIM(@HTMLText))
END
GO

-- Functie om de letters uit een string te halen
IF OBJECT_ID('FN_RemoveChars') IS NOT NULL
DROP FUNCTION [FN_RemoveChars]
GO
CREATE FUNCTION [dbo].[FN_RemoveChars](@Str varchar(1000))
RETURNS VARCHAR(1000)
BEGIN
	DECLARE @NewStr varchar(1000),
	@i int
	SET @i = 1
	SET @NewStr = ''

	WHILE @i <= len(@str)
		BEGIN
		--grab digits or (| in regex) decimal
		IF SUBSTRING(@str,@i,1) LIKE '%[0-9|.]%'
			BEGIN
			SET @NewStr = @NewStr + SUBSTRING(@str,@i,1)
			END
		ELSE
			BEGIN
			SET @NewStr = @NewStr
			END
		SET @i = @i + 1
		END
	RETURN Rtrim(Ltrim(@NewStr))
END
GO

GO
SET IDENTITY_INSERT Voorwerp ON
GO
INSERT INTO Voorwerp
(voorwerpnummer,titel,beschrijving,landnaam,verkoper_gebruikersnaam,veiling_gesloten,verkoopprijs,thumbnail,source,conditie)
SELECT DISTINCT ID,
Titel,
dbo.FN_StripHTML(dbo.FN_StripStyle(dbo.FN_StripScript(Items.Beschrijving))),
SUBSTRING(Locatie,CHARINDEX(',',Locatie),90 - CHARINDEX(',',Locatie)),
Verkoper,
1,
DBO.FN_RemoveChars(Prijs),
Thumbnail,
1,
Conditie
FROM Items
GO
SET IDENTITY_INSERT Voorwerp OFF
GO
-- Voorwerp insert einde



-- Rubrieken insert start
SET IDENTITY_INSERT Rubriek ON
GO
INSERT INTO Rubriek (rubrieknummer,rubrieknaam,parent_rubriek,source)
SELECT DISTINCT ID,
Name,
Parent,
1
FROM Categorieen
GO
SET IDENTITY_INSERT Rubriek OFF
GO
-- Rubrieken insert einde



-- Voorwerpen in Rubrieken insert start
INSERT INTO VoorwerpInRubriek (voorwerpnummer,rubrieknummer,source)
SELECT DISTINCT ID,Categorie,1
FROM Items
GO
-- Voorwerpen in Rubrieken insert einde



-- Bestand insert start
INSERT INTO Bestand (voorwerpnummer,filenaam,source)
SELECT DISTINCT I.ItemID,I.IllustratieFile,1
FROM Illustraties I
WHERE ItemID IN (SELECT TOP 4 II.ItemID FROM Illustraties II WHERE I.ItemID = II.ItemID)
GO
--- Bestanden insert einde