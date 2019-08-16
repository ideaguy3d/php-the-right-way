/* "AS" prac */
SELECT imdb_rating AS 'rate'
FROM movies
WHERE rate > 8;

/* "DISTINCT" prac */
SELECT DISTINCT genre
FROM movies;


/* "LIKE" prac */
SELECT * FROM movies
WHERE movie_name LIKE 'Se_en';

SELECT * FROM movies
WHERE movie_name LIKE '%man%';


/* "IS NULL" prac */
SELECT `name` FROM movies
WHERE imdb_rating IS NULL;


/* ----------------------
   ---- commit point ----
   ---------------------- */

/* 'BETWEEN' and 'AND' prac */
SELECT * FROM movies
WHERE `name` BETWEEN 'D' AND 'G';

SELECT * FROM movies
WHERE year BETWEEN 1970 AND 1979;

SELECT * FROM movies
WHERE `year` BETWEEN 1970 AND 1979
  AND imdb_rating > 8;

SELECT * FROM movies
WHERE `year` < 1985 AND imdb_rating > 8
  AND genre = 'horror';

/* 'OR' prac */
SELECT * FROM movies
WHERE `year` > 2014 OR genre = 'action';

SELECT * FROM  movies
WHERE genre = 'comedy' OR genre = 'romance';

SELECT * FROM  movies
WHERE genre = 'comedy' OR genre = 'romance'
ORDER BY -imdb_rating;

/* ----------------------
   ---- commit point ----
   ---------------------- */

/* 'ORDER BY' prac */
SELECT `name`, `year` FROM movies
ORDER BY `name` ASC;

SELECT `name`, `year`, imdb_rating FROM movies
ORDER BY `year` DESC;

/* 'LIMIT' prac */
SELECT * FROM movies
ORDER BY imdb_rating LIMIT 3;

/* 'CASE' prac */
SELECT `name`,
	CASE
  	WHEN imdb_rating > 8 THEN "Spetacular movie!"
    WHEN imdb_rating > 6 THEN "Not that good."
    ELSE "A pretty awful movie"
  END AS 'zREVIEW'
FROM movies;

SELECT `name`,
  CASE
    WHEN `genre` = 'romance' THEN 'chill'
    WHEN `genre` = 'comedy' THEN 'chill'
    ELSE 'intense'
  END AS 'Mood'
FROM movies;

SELECT
	`amazon_order_id`,
  `product_name`,
	CASE
    	WHEN `item_price` > 10 THEN 'Expensive'
      WHEN `item_price` > 1 THEN 'Affordable'
      ELSE 'unknown'
  END AS 'economics'
FROM `majide_test1`;

UPDATE celebs_table
SET twitter_handle = '@realDonaldTrump'
WHERE id = 9825;

DELETE FROM celebs_table
WHERE age > 30;

SELECT * FROM movies_table
WHERE movie_title BETWEEN 'A' AND 'J';

SELECT * FROM orders
JOIN customers
ON orders.customer_id = customers.customer_id;


/*
  mine blocks project random prac
*/

/* get the daily revenue & daily players
  from 2 different tables */
with daily_revenue as (
	select
  	date(created_at) as dt,
    round(sum(price), 2) as rev
  from purchases
  where refunded_at is null
  group by 1
),
daily_players as (
	select
  	date(created_at) as dt,
  	count(distinct user_id) as players
  from gameplays
  group by 1
)
select * from daily_players order by dt;

/* TODO: rewrite entire query a few times
  to fully grok it */

/* extract the 'daily revenue per player' metric
  each with clause is querying a different table */
with daily_revenue as (
	select
  	date(created_at) as dt,
    round(sum(price), 2) as rev
  from purchases
  where refunded_at is null
  group by 1
),
daily_players as (
	select
  	date(created_at) as dt,
  	count(distinct user_id) as players
  from gameplays
  group by 1
)
select
	daily_revenue.dt,
  daily_revenue.rev / daily_players.players
from daily_revenue
	join daily_players using (dt);

/* inner join prac */
select
  date(g1.created_at) as dt,
  count(distinct g1.user_id) as z_total_users,
  count(distinct g2.user_id) as z_retained_users
from gameplays as g1
  left join gameplays as g2
  on g1.user_id = g2.user_id
  and date(g1.created_at) = date(datetime(g2.created_at), '-1 day')
group by 1
order by 1 ASC
limit 100;










/* end of this SQL file */