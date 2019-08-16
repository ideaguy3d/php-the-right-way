/* fictional 'Restaurant' data table query Prac */
select * from nomnom;

SELECT DISTINCT neighborhood FROM nomnom;

SELECT DISTINCT cuisine FROM nomnom;

SELECT * FROM nomnom
WHERE cuisine = 'Chinese';

SELECT * FROM nomnom
WHERE review > 4;

SELECT * FROM nomnom
WHERE cuisine = 'Italian' AND price = '$$$';

SELECT * FROM nomnom
WHERE `name` LIKE '%meatball%';

SELECT * FROM nomnom
WHERE neighborhood = 'Midtown'
  OR neighborhood = 'Chinatown'
  OR neighborhood = 'Downtown';

SELECT * FROM nomnom
WHERE health IS NULL;

SELECT * FROM nomnom
WHERE review IS NOT NULL
ORDER BY review DESC
LIMIT 10;

SELECT `name`, `cuisine`,
  CASE
    WHEN review > 4.5 THEN 'Extraordinary'
    WHEN review > 4 THEN 'Excellent'
    WHEN review > 3 THEN 'Good'
    WHEN review > 2 THEN 'Fair'
    ELSE 'Poor'
  END AS 'rate'
FROM nomnom;