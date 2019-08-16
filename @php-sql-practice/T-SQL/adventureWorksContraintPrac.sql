USE TutorialDB
GO

-- "DEFAULT constraint"
CREATE TABLE Student (
	StudentId int identity(1,1) NOT NULL PRIMARY KEY,
	First varchar(50),
	Last varchar(50),
	Score int DEFAULT 70
);

INSERT INTO Student (Last, First) 
VALUES
	('Hernandez', 'Jose'), 
	('Vu', 'Vince')
GO

SELECT * FROM Student;

-- "UNIQUE constraint"
CREATE TABLE Customer (
	CustomerId int UNIQUE,
	First varchar(30),
	Last varchar(50)
);


INSERT INTO Student (CustomerId, Last, First) VALUES
(1,'Rodriguez', 'Maria'), (2,'Jones', 'Danielle');

SELECT * FROM Customer;

-- "FOREIGN KEY constraint"
CREATE TABLE Bridge2 (
	BridgeID INT UNIQUE,
	First VARCHAR(30),
	Last VARCHAR(50)
);

CREATE TABLE BridgeBuilders (
	BridgeBuilderID INT NOT NULL,
	BridgeBuildersName varchar (50) NULL,
	BridgeID int,
	PRIMARY KEY (BridgeBuilderID),
	FOREIGN KEY (BridgeID) REFERENCES Bridge2(BridgeID)
);

-- "CHECK contraint"
ALTER TABLE Customer
ADD Check (CustomerId > 0);

INSERT INTO Customer VALUES (-4, 'Nathan', 'Chellous');
INSERT INTO Customer VALUES (5, 'Natalie', 'Shaffer');

-- DELETE FROM Customer WHERE CustomerId < 0;

SELECT * FROM Customer;

-- "more CHECK practice"
ALTER TABLE Student
ADD CHECK (Score > 0 AND Score < 100);

--INSERT INTO Student VALUES ('Jessica', 'Garcia', 110);

































-- end of t-sql file
