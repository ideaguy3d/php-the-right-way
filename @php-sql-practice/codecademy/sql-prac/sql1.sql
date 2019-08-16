CREATE TABLE friends (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT DEFAULT "unknown name",
  birthdate DATE,
  cur_age INTERGER DEFAULT 99,
  gender TEXT
);

INSERT INTO friends (name, birthdate, cur_age, gender)
VALUES ('Jane Doe', 'May 30th 1990', 28, 'female');

INSERT INTO friends (name, birthdate, cur_age, gender)
VALUES ('Julius Hernandez Alvarado', 'Feb 5th 1989', 29, 'male');

INSERT INTO friends (name, birthdate, cur_age, gender)
VALUES ('Jazmine Gijon Palacio', 'Apr 28th 1995', 22, 'female');

UPDATE friends
SET name = 'Jane Smith'
WHERE id = 1;

ALTER TABLE friends
ADD COLUMN email;

UPDATE friends
SET email = 'jane@codecademy.com'
WHERE id = 1;

UPDATE friends
SET email = 'javascript.uiux@gmail.com'
WHERE id = 2;

UPDATE friends
SET email = 'jazmingigonpalacio@gmail.com'
WHERE id = 3;

DELETE FROM friends
WHERE id = 1;

SELECT * FROM friends;

UPDATE celebs_table
SET age = 29
WHERE id = 9;

DELETE FROM celebs_table
WHERE twitter_handle IS NULL;

SELECT * FROM movies_table
WHERE `year` BETWEEN 1990 AND 1999;


/* ----------------------
   ---- commit point ----
   ---------------------- */

SELECT * FROM orders
JOIN subscriptions
  ON orders.subscriptions_id = subscriptions.subscription_id;

SELECT * FROM orders
JOIN subscriptions
  ON orders.subscription_id = subscriptions.subscription_id
WHERE subscriptions.description = 'Fashion Magazine';

SELECT COUNT(*) AS 'total_newspaper_describers'
FROM newspaper;

SELECT COUNT(*) AS 'total_online_subscribers'
FROM online;

SELECT COUNT(*) AS 'combined newspaper&online subscribers'
FROM newspaper
JOIN online
  ON newspaper.id = online.id;

/* more sql self-join prac */
select
  date(g1.created_at) as dt,
  g1.user_id as user_day1,
  g2.user_id as user_day2
from gameplays as g1
  join gameplays as g2
  on g1.user_id = g2.user_id
  and date(g1.created_at) = date(datetime(g2.created_at), '-1 day')
order by 1
limit 100;









/* end of this SQL file */