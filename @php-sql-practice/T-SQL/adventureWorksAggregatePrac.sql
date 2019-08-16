USE [TutorialDB]
GO

Create Table Books(
 BooksID int Identity (1,1) Not Null Primary Key,
 BookTitle varchar (50) Null,
 BookAuthor varchar (50) Null,
 BookQuantity int Null,
 SoldDate Datetime Null
);

Insert Into Books
Values
('The Great Gatsby 2','F Scott Fitzgerald',32325,'02-10/11'),
('Pride and Prejudice','Jane Austen',32,'03-10/15'),
('The Lord of the Rings','JRR Tolkien',555,'03-10/15'),
('Jane Eyre','Charlotte Bronte',3454,'03-10/15'),
('Harry Potter series','JK Rowling',5434,'02-10/15'),
('To Kill a Mockingbird','Harper Lee',866,'02-10/15'),
('Wuthering Heights','Emily Bronte',45646,'02-10/15'),
('Nineteen Eighty Four','George Orwell',34523,'01-10/15'),
('His Dark Materials','Philip Pullman',45453,'01-10/15'),
('Great Expectations','Charles Dickens',23432,'01-10/15'),
('Little Women','Louisa M Alcott',34234,'03-10/14'),
('Tess of the D’,Urbervilles', 'Thomas Hardy',234234,'03-10/14'),
('Catch 22','Joseph Heller',2343,'03-10/13'),
('Rebecca','Daphne Du Maurier',24342,'03-10/13'),
('The Hobbit','JRR Tolkien',342343,'03-10/13'),
('Birdsong','Sebastian Faulk',23432,'03-10/13'),
('Catcher in the Rye','JD Salinger',5756,'02-10/12'),
('The Time Traveler’s Wife','Audrey Niffenegger',7564,'02-10/11'),
('Middlemarch','George Eliot',909090,'02-10/11'),
('Gone With The Wind','Margaret Mitchell',8844,'02-10/11'),
('The Great Gatsby','F Scott Fitzgerald',90494,'02-10/11')

SELECT * FROM Books

SELECT 
	SUM(Books.BookQuantity),
	Books.SoldDate
FROM Books
WHERE SoldDate = '2011-02-10 00:00:00.000'
GROUP BY SoldDate; -- Any column that is NOT being aggregated must be grouped by

SELECT
	max(BookQuantity) AS MaxQuantity,
	SoldDate
FROM Books
WHERE SoldDate = '2011-02-10 00:00:00.000'
GROUP BY SoldDate;


SELECT
	BookAuthor,
	SUM(BookQuantity) AS TotalSales
FROM Books
WHERE BookAuthor LIKE 'j%'
GROUP BY BookAuthor;


CREATE TABLE EmpSalary(
	EmpSalaryID int Identity (1,1) Not Null Primary Key,
	Fname varchar (20) Null,
	Lname varchar (20) Null,
	Salary Money Null,
	Sales Money Null,
	Commission varchar (10) Null
);

Insert Into EmpSalary
values
('Tom','Smith','35000',453000,'2'),
('Stan','Brimes','34055',7686,'10'),
('Roger','Fuller','23045',34834,'3'),
('Ralph','Knowes','76034',96675,'7'),
('Andy','Mattews','86076',21193,'10')


-- What is the bonus of each rep? (using 2 columns in SUM())
SELECT Fname, Lname, Sales, Commission,
	SUM(Sales * Commission) AS TotalBonus
FROM EmpSalary
GROUP BY Fname, Lname, Sales, Commission;















































-- end of t-sql file 