
/****** Script for SelectTopNRows command from SSMS  ******/
--SELECT TOP (1000) *
--FROM [TutorialDB].[dbo].[mailing_list2]

--CREATE TABLE Names (
--	fname varchar(30),
--	lname varchar(30),
--	city varchar(30),
--	state char(3)
--);

USE TutorialDB
GO

--ALTER TABLE Names ADD salary int; 

--ALTER TABLE Names 
--ALTER COLUMN salary money; 

INSERT INTO Names VALUES ('Michelle', 'Tran', 'Sacramento', 'ca', 59000);

UPDATE Names SET fname = 'Josh' WHERE fname = 'JULIUS';

SELECT * FROM [TutorialDB].[dbo].[Names];

--"Make a copy of the db"
--SELECT * INTO [TutorialDB].[dbo].[Mailing_List1_Copy]
--FROM [TutorialDB].[dbo].[Mailing_list1]

--ALTER TABLE [TutorialDB].[dbo].[mailing_list2] ADD quantity int

--"Use an expression in the UPDATE table SET field WHERE condition operator"
--UPDATE [TutorialDB].[dbo].[mailing_list2] SET [quantity] = [quantity]+4 WHERE [city] = 'Coppell'

--SELECT * INTO [TutorialDB].[dbo].[mailing_list2_copy]
--FROM [TutorialDB].[dbo].[mailing_list2]

DELETE FROM [TutorialDB].[dbo].[mailing_list2_copy]
WHERE city = 'The Woodlands'

--"subquery prac" in t-sql
DELETE FROM [TutorialDB].[dbo].[mailing_list2_copy]
WHERE st IN (
	SELECT st FROM [TutorialDB].[dbo].[mailing_list2_copy]
	WHERE st = 'TX' OR st = 'AZ'
)

--ALTER TABLE [TutorialDB].[dbo].[mailing_list2_copy]
--ALTER COLUMN plus4 int

SELECT year, first, address, city, st, quantity, plus4 FROM [TutorialDB].[dbo].[mailing_list2_copy]
ORDER BY [plus4] DESC

--"IN operator practice"
SELECT year, first, address, city, st, quantity, plus4 FROM [TutorialDB].[dbo].[mailing_list2_copy]
WHERE first IN ('John', 'Ralph', 'Alan')

--"BETWEEN prac"
SELECT year, first, address, city, st, quantity, plus4 FROM [TutorialDB].[dbo].[mailing_list2_copy]
WHERE first IN ('John', 'Ralph', 'Alan')

--"BETWEEN prac"
SELECT year, first, address, city, st, quantity, plus4 FROM [TutorialDB].[dbo].[mailing_list2_copy]
WHERE year BETWEEN 2010 AND 2014


--"NOT BETWEEN prac"
SELECT year, first, address, city, st, quantity, plus4 FROM [TutorialDB].[dbo].[mailing_list2_copy]
WHERE year NOT BETWEEN 2010 AND 2014

--"AND, BETWEEN, IN, NOT"
SELECT year, first, address, city, st, quantity, plus4 FROM [TutorialDB].[dbo].[mailing_list2_copy]
WHERE [year] NOT BETWEEN 2009 AND 2014
AND [st] NOT IN ('NJ', 'CO', 'GA')

--"using BETWEEN for words/text"
SELECT year, first, address, city, st, quantity, plus4 FROM [TutorialDB].[dbo].[mailing_list2_copy]
WHERE first BETWEEN 'H' AND 'J'

SELECT year, first, address, city, st, quantity, plus4 FROM [TutorialDB].[dbo].[mailing_list2_copy]
WHERE first NOT BETWEEN 'H' AND 'K'

--"BETWEEN prac for date ranges"
SELECT year, first, address, city, st, quantity, plus4 FROM [TutorialDB].[dbo].[mailing_list2_copy]
WHERE deadline BETWEEN '2018-06-01 00:00:00.000' AND '2018-07-01 00:00:00.000'


SELECT * FROM [TutorialDB].[dbo].[mailing_list2_copy]













































-- end of sql file --