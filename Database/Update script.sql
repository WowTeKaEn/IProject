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

UPDATE Voorwerp
SET beschrijving = dbo.FN_StripHTML(dbo.FN_StripStyle(dbo.FN_StripScript(Items.Beschrijving)))
FROM Items
WHERE Voorwerp.voorwerpnummer = Items.ID

SELECT beschrijving FROM Voorwerp WHERE voorwerpnummer = 110366862071

SELECT TOP 100 voorwerpnummer,beschrijving FROM Voorwerp WHERE voorwerpnummer = 110366862071

DELETE FROM Users