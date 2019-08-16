SELECT COUNT (*)
FROM fake_apps;

SELECT COUNT (*) FROM fake_apps
WHERE price = 0.00;

SELECT SUM (downloads) FROM fake_apps;

SELECT MAX (downloads) FROM  fake_apps;

SELECT MIN (downloads) FROM fake_apps;

SELECT MAX (price) FROM fake_apps;

SELECT AVG (downloads) FROM fake_apps;

SELECT AVG (price) FROM fake_apps;

SELECT `name`, ROUND (price, 0) FROM fake_apps;

SELECT ROUND (AVG(price), 2) FROM fake_apps;

/*******************/
/* 'GROUP BY' prac */
/*******************/
SELECT price, COUNT (*)
FROM fake_apps
GROUP BY price;

SELECT price, COUNT (*)
FROM fake_apps
WHERE downloads > 20000
GROUP BY price;

SELECT category, SUM(downloads)
FROM fake_apps
GROUP BY category;

/* ----------------------
   ---- commit point ----
   ---------------------- */

SELECT category, price, AVG(downloads)
FROM fake_apps
GROUP BY category, price;

SELECT category, price,
  ROUND(AVG(downloads), 2) AS 'avg_downloads'
FROM fake_apps
GROUP BY 1, 2;

SELECT price,
  ROUND(AVG(downloads)) AS 'average_downloads'
FROM fakes_apps
GROUP BY 2
HAVING COUNT(*) > 9;

SELECT COUNT(*) AS 'total_rows_in_data_table'
FROM fake_apps;

SELECT category, COUNT(category) AS 'total number of apps in category'
FROM fake_apps
GROUP BY category
HAVING COUNT(*) > 10;