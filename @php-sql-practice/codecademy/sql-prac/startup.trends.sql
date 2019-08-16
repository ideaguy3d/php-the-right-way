SELECT COUNT(*) AS 'total companies',
	SUM(valuation) AS 'total value',
  MAX(raised) AS 'most raised'
FROM startups;

/* simple query to get most raised
  by seed stage start up */
SELECT MAX(raised)
FROM startups
WHERE stage = 'seed';

SELECT stage,
  COUNT(*) AS 'total companies',
  SUM(raised) AS 'total raised',
  MAX(raised) AS 'max raised',
FROM startups
GROUP BY stage;

SELECT stage,
	COUNT(*) AS 'total companies',
	SUM(valuation) AS 'total value',
  MAX(raised) AS 'most raised'
FROM startups
GROUP BY stage
HAVING stage NOT NULL;

/* find the oldest startup */
SELECT `name`,
  MIN(founded) AS 'year founded',
FROM startups
GROUP BY `name`
ORDER BY founded ASC
LIMIT 3; /* get the 3 oldest */

/* simpler way */
SELECT *, MIN(founded)
FROM startups;

SELECT ROUND(AVG(valuation)) AS 'average of all startups'
FROM startups;

SELECT category,
  ROUND(AVG(valuation), 2)
FROM startups
GROUP BY category
HAVING category NOT NULL
ORDER BY 2 DESC;

SELECT category
  COUNT(*) AS 'total',
FROM startups
GROUP BY category
HAVING COUNT(*) > 3
ORDER BY COUNT(*) DESC
LIMIT 30;

SELECT category,
	COUNT(*) AS 'total'
FROM startups
GROUP BY category
HAVING COUNT(*) > 3
ORDER BY COUNT(*) DESC;

SELECT location,
  ROUND(AVG(employees)) AS 'average size'
FROM startups
GROUP BY location
HAVING AVG(employees) > 500
ORDER BY AVG(employees) DESC;







/* end of this SQL file */