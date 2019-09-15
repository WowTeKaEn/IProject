/*

Beperkingsregels

*/



/* 1 */
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[FN_CheckVerkoper]')
AND type in (N'FN', N'IF',N'TF', N'FS', N'FT'))
DROP FUNCTION [dbo].[FN_CheckVerkoper]
GO
CREATE FUNCTION FN_CheckVerkoper(@gebruikersnaam NVARCHAR(20))
RETURNS BIT
BEGIN
	DECLARE @antwoord BIT
	SELECT @antwoord = verkoper
	FROM Gebruiker
	WHERE @gebruikersnaam = gebruikersnaam;
	RETURN @antwoord
END;
GO

ALTER TABLE Verkoper
ADD CONSTRAINT CHK_Wel_Verkoper CHECK(dbo.FN_CheckVerkoper(gebruikersnaam) = 1)
GO



/* 2 */




/* 3 */
ALTER TABLE Verkoper
ADD CONSTRAINT CHK_Rekeningnummer_Creditcardnummer CHECK(rekeningnummer != NULL OR creditcardnummer != NULL)
GO



/* 4 */
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[FN_CheckFileCount]')
AND type in (N'FN', N'IF',N'TF', N'FS', N'FT'))
DROP FUNCTION [dbo].[FN_CheckFileCount]
GO
CREATE FUNCTION FN_CheckFileCount(@voorwerpnummer BIGINT)
RETURNS INT
BEGIN
	DECLARE @count INT;
	SELECT @count = COUNT(*)
	FROM Bestand
	WHERE Bestand.voorwerpnummer = @voorwerpnummer
	RETURN @count;
END;
GO

ALTER TABLE Bestand
ADD CONSTRAINT CHK_FileCount CHECK(dbo.FN_CheckFileCount(voorwerpnummer) < 4 OR source = 1)
GO



/* 5 */
ALTER TABLE Bod
DROP CONSTRAINT CHK_Bod_Voldoende
GO
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[FN_CheckBod]')
AND type in (N'FN', N'IF',N'TF', N'FS', N'FT'))
DROP FUNCTION [dbo].[FN_CheckBod]
GO
CREATE FUNCTION FN_CheckBod(@voorwerpnummer BIGINT,@nieuwe_bod NUMERIC(10,2))
RETURNS BIT
BEGIN
	DECLARE @hoogste_bod NUMERIC(10,2)
	DECLARE @verschil NUMERIC(10,2)
	DECLARE @bod_mogelijk BIT
	SELECT @hoogste_bod = MAX(bod_bedrag)
	FROM Bod
	WHERE voorwerpnummer = @voorwerpnummer;
	SET @bod_mogelijk = CASE 
		WHEN @hoogste_bod IS NULL THEN 1
		WHEN @hoogste_bod < 50 AND @nieuwe_bod - @hoogste_bod > 0.5 THEN 1
		WHEN @hoogste_bod < 500 AND @nieuwe_bod - @hoogste_bod > 1 THEN 1
		WHEN @hoogste_bod < 1000 AND @nieuwe_bod - @hoogste_bod > 5 THEN 1
		WHEN @hoogste_bod < 5000 AND @nieuwe_bod - @hoogste_bod > 10 THEN 1
		WHEN @hoogste_bod >= 5000 AND @nieuwe_bod - @hoogste_bod > 50 THEN 1
		ELSE 0
	END;
	RETURN @bod_mogelijk
END;
GO

ALTER TABLE Bod
ADD CONSTRAINT CHK_Bod_Voldoende CHECK(dbo.FN_CheckBod(voorwerpnummer,bod_bedrag) = 1)
GO



/* 6 */
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[FN_CheckBodGebruiker]')
AND type in (N'FN', N'IF',N'TF', N'FS', N'FT'))
DROP FUNCTION [dbo].[FN_CheckBodGebruiker]
GO
CREATE FUNCTION FN_CheckBodGebruiker(@gebruikersnaam NVARCHAR(20),@voorwerpnummer BIGINT)
RETURNS BIT
BEGIN
	DECLARE @verkoper_gebruikersnaam NVARCHAR(20)
	SELECT @verkoper_gebruikersnaam = verkoper_gebruikersnaam
	FROM Voorwerp
	WHERE voorwerpnummer = @voorwerpnummer;
	RETURN CASE WHEN @gebruikersnaam = @verkoper_gebruikersnaam THEN 1 ELSE 0 END
END
GO

ALTER TABLE Bod
ADD CONSTRAINT CHK_Bod_Gebruikersnaam CHECK(dbo.FN_CheckBodGebruiker(gebruikersnaam,voorwerpnummer) = 0)
GO