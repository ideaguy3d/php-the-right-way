CREATE TABLE #Temp1 (
	id int identity(1,1) NOT NULL PRIMARY KEY,
	first varchar(100) NOT NULL,
	last varchar(100) NULL
);

CREATE INDEX Ind_Temp1_first ON #Temp1 (first); -- ALT+F1 to view table info

ALTER TABLE #Temp1
ADD CONSTRAINT Const_pk PRIMARY KEY (first);

-- "temp table prac"
CREATE TABLE #Temp1 (
	id int identity(1,1) NOT NULL PRIMARY KEY,
	first varchar(100) NOT NULL,
	last varchar(100) NULL
);

CREATE INDEX Ind_Temp1_first ON #Temp1 (first); -- ALT+F1 to view table info

ALTER TABLE #Temp1
ADD CONSTRAINT Const_pk PRIMARY KEY (first);

INSERT INTO #Temp1 VALUES
('Bobby', 'Smithson'),
('Julius', 'Hernandez');

SELECT year, make, model INTO #tempCarData
FROM [TutorialDB].[dbo].[mailing_list2]

SELECT * FROM #tempCarData;


-- "Global temp table prac"
CREATE TABLE ##GlobalTempTable(
	ID int identity(1,1) NOT NULL PRIMARY KEY,
	First varchar(50) NULL,
	Last varchar(50) NULL
);

INSERT INTO ##GlobalTempTable
VALUES ('Julius', 'Hernandez'),
('Jocelyn', 'Alvarez'),
('Julissa', 'Avila');

SELECT * FROM ##GlobalTempTable;

-- "Stored Procedure prac"
USE tempdb
GO

CREATE PROCEDURE tempGlobalModelsThatStartWithJ
AS
BEGIN
  SELECT year, make, model INTO ##tempGlobalCarInfo2
	FROM [TutorialDB].[dbo].mailing_list2
	WHERE model LIKE 'j%';

	SELECT * FROM ##tempGlobalCarInfo2;
END

EXEC tempGlobalModelsThatStartWithJ;














-- end of sql file