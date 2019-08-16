USE [TutorialDB]
GO

/* Create a table */

CREATE TABLE Names3
( NameID int NOT NULL IDENTITY (1,1) PRIMARY KEY, --<< Note: This is the Primary Key for this table.
Fname varchar (20) NULL,
Lname varchar (20) NULL,
Gender char (1) NULL)

--Insert data into table names

INSERT INTO Names3
VALUES
('Tom','Jones','M'), --1
('Bob','Smith','M'), --2
('Henry','Book','M'), --3
('Mary','Lamp','F'), --4
('Susan','Keys','F'),--5
('Danella','Hortom','F')--6

SELECT * FROM Names3

/* Create Department table & insert data */

CREATE TABLE Department
(DepartmentID int NOT NULL IDENTITY (1,1) PRIMARY KEY, --<<Note: This is the Primary Key for this table
Department varchar (20),
BuildingCode varchar (20),
NameID int FOREIGN KEY REFERENCES Names3 (NameID) NULL) --<<Note: This is the Foreign Key in this table that has a relationship with Names table

--Insert data into table Department

INSERT INTO Department
VALUES
('Finance','abc',1),
('Business','xyz',4),
('Accouting','123',2),
('Taxes','xyz',NULL),
('Taxes','xyz',NULL),
('Taxes','xyz',NULL)

SELECT * FROM Department;

SELECT 
	Department.NameID,
	Department.Department, 
	Names3.Fname 
FROM Department JOIN Names3 
ON Department.NameID = Names3.NameID;




/*********************************/
/* UNION, EXCEPT, INTERCEPT prac */
/*********************************/

CREATE TABLE dbo.Buyer
(BuyID int IDENTITY (1,1) NOT NULL PRIMARY KEY,--<<Primary Key
BuyFname varchar (20) NULL,
BuyLname varchar (20) NULL,
Age char (3) NULL)
 
--INSERT DATA

INSERT INTO dbo.Buyer
VALUES
('BOB', 'SMITH','35'),
('TOM', 'JONES','25'),
('MIKE', 'COBAL','15'),
('DREW', 'SHAFFER','75'),
('HENRY', 'WILLIS','37'),
('RAF', 'ASGHAR','35')--<< This row will exists in both tables for our demo

Select * from dbo.Buyer
 
--CREATE 2nd TABLE

CREATE TABLE dbo.Supplier
(SuppID int IDENTITY (1,1) NOT NULL PRIMARY KEY, --<<Primary Key
SuppFname varchar (20) NULL,
SuppLname varchar (20) NULL,
Age char (3) NULL)
 
--INSERT DATA

INSERT INTO dbo.Supplier
VALUES
('Jack', 'Supplies','35'),
('Mike', 'Supplies','25'),
('Jessy', 'Supplies','15'),
('DREW', 'Supplies','75'),
('John', 'Supplies','37'),
('Mary', 'Supplies','35'),
('RAF', 'ASGHAR','35') --<<Duplicate value shared by both tables

Select * from dbo.Supplier;

-- "UNION will only select distinct data"
SELECT
	[TutorialDB].[dbo].[Supplier].[SuppFname],
	[TutorialDB].[dbo].[Supplier].[SuppLname]
FROM [TutorialDB].[dbo].[Supplier]
UNION
SELECT
	[TutorialDB].[dbo].[Buyer].[BuyFname],
	[TutorialDB].[dbo].[Buyer].[BuyLname]
FROM [TutorialDB].[dbo].[Buyer];

-- "UNION ALL prac"
SELECT
	[TutorialDB].[dbo].[Supplier].[SuppFname],
	[TutorialDB].[dbo].[Supplier].[SuppLname]
FROM [TutorialDB].[dbo].[Supplier]
UNION ALL
SELECT
	[TutorialDB].[dbo].[Buyer].[BuyFname],
	[TutorialDB].[dbo].[Buyer].[BuyLname]
FROM [TutorialDB].[dbo].[Buyer];

-- "EXCEPT prac, if data exists in both tables, exclude it"
SELECT
	[TutorialDB].[dbo].[Supplier].[SuppFname],
	[TutorialDB].[dbo].[Supplier].[SuppLname]
FROM [TutorialDB].[dbo].[Supplier]
EXCEPT
SELECT
	[TutorialDB].[dbo].[Buyer].[BuyFname],
	[TutorialDB].[dbo].[Buyer].[BuyLname]
FROM [TutorialDB].[dbo].[Buyer];


-- "INTERSECT prac, only include data that is in both tables"
SELECT
	[TutorialDB].[dbo].[Supplier].[SuppFname],
	[TutorialDB].[dbo].[Supplier].[SuppLname]
FROM [TutorialDB].[dbo].[Supplier]
INTERSECT
SELECT
	[TutorialDB].[dbo].[Buyer].[BuyFname],
	[TutorialDB].[dbo].[Buyer].[BuyLname]
FROM [TutorialDB].[dbo].[Buyer];




























-- end of t-sql file 