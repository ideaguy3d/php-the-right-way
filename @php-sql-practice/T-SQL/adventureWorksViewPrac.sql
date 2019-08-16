SELECT [BooksID]
, [BookTitle]
, [BookAuthor]
, [BookQuantity]
, [SoldDate]
FROM [TutorialDB].[dbo].[Books]


-- "CREATE VIEW vName AS" prac
CREATE VIEW vBooks2
AS
SELECT [Books].[BooksID],
	[Books].[BookTitle],
	[Books].[BookAuthor]
FROM [TutorialDB].[dbo].[Books];

SELECT * FROM vBooks2;

CREATE VIEW vNameDepartment4
AS
SELECT
	[Names3].[Fname],
	[Department].[Department]
FROM [TutorialDB].[dbo].[Department] JOIN [TutorialDB].[dbo].[Names3]
ON [Department].[NameID] = [Names3].[NameID];

SELECT * FROM vNameDepartment4;



























-- end of t-sql file