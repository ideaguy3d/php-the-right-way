
USE AdventureWorks2014
GO

CREATE PROCEDURE spSalesTerritory
AS
BEGIN
	SELECT
		 [TerritoryId]
		,[Name]
		,[CountryRegionCode]
		,[Group]
		,[SalesYTD]
		,[SalesLastYear]
		,[CostYTD]
		,[CostLastYear]
		,[rowguid]
		,[ModifiedDate]
	FROM [AdventureWorks2014].[Sales].[SalesTerritory]
END;


ALTER PROCEDURE [dbo].[spSalesTerritory]
AS
BEGIN
	SELECT
		 [TerritoryId]
		,[Name]
		,[CountryRegionCode]
		,[Group]
		,[SalesYTD]
		,[SalesLastYear]
		,[CostYTD]
		,[CostLastYear]
		--,[rowguid]
		--,[ModifiedDate]
	FROM [AdventureWorks2014].[Sales].[SalesTerritory]
END

DROP PROCEDURE [dbo].[spSalesTerritory]
GO

USE TutorialDB
GO

CREATE PROC spUseParameter
@BookAuthor varchar(20)
AS
BEGIN
	SELECT
		 [BooksID]
		,[BookTitle]
		,[BookAuthor]
		,[BookQuantity]
		,[SoldDate]
	FROM [TutorialDB].[dbo].[Books]
	WHERE BookAuthor = @BookAuthor
END;

CREATE PROC spMultipleParams
@foo varchar(20),
@bar varchar(20)
AS
BEGIN
	SELECT
		BooksID
		,BookTitle
		,BookAuthor
		,BookQuantity
		,SoldDate
	FROM [TutorialDB].[dbo].[Books]
	WHERE BookAuthor = @foo AND BookTitle = @bar
END

EXEC spUseParameter 'Charlotte Bronte';

spMultipleParams
-- 'Margaret Mitchell', 'Gone With The Wind'
'Charlotte Bronte','Jane Eyre'

DROP PROC spMultipleParams;












-- end of sql file