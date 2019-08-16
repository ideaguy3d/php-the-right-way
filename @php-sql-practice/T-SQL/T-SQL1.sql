-- Create a database 'TutorialDB'
USE master -- master database
GO
IF NOT EXISTS (
  SELECT name
  FROM sys.databases
  WHERE name = N'TutorialDB'
)
GO

ALTER DATABASE [TutorialDB] SET QUERY_STORE=ON
GO

-- Create a table called 'dbo.customers',
-- drop it if it already exists
IF OBJECT_ID('dbo.Customers', 'U') IS NOT NULL
DROP TABLE dbo.Customers
GO

-- Create the dbo.Customers table
CREATE TABLE dbo.Customers
(
	CustomerId		INT				NOT NULL	PRIMARY KEY,
	Name		[NVARCHAR](50)		NOT NULL,
	Location	[NVARCHAR](50)		NOT NULL,
	Email		[NVARCHAR](50)		NOT NULL
);
GO

-- insert data into the table
INSERT INTO dbo.Customers
	([CustomerId],[Name],[Location],[Email])
VALUES
	(1, N'Orlando', N'Australia', N''),
	(2, N'Keith', N'India', N'keith@adventure-works.com'),
	(3, N'Donna', N'Germany', N'donna@micro.com'),
	(4, N'Janet', N'United States', N'jan@states.com')
GO
