/* revenue per day */
SELECT
	DATE(created_at) AS date_played,
	ROUND(SUM(price), 2) AS revenue
FROM purchases
GROUP BY 1
ORDER BY 1;

/* daily revenue not counting refunds */
SELECT
	DATE(created_at) AS date_played,
	ROUND(SUM(price), 2) AS daily_revenue
FROM purchases
WHERE refunded_at IS NOT NULL
GROUP BY 1
ORDER BY 1
LIMIT 10;

/* daily active users per day */
SELECT
	DATE(created_at) AS date_played,
	COUNT(DISTINCT user_id) AS dau
FROM gameplays
GROUP BY 1
ORDER BY 1;

/* grouping by 2 columns to get daily
  active users per date & platform */
SELECT
	DATE(created_at) AS date_played,
  platform,
	COUNT(DISTINCT user_id) AS dau
FROM gameplays
GROUP BY 1, 2
ORDER BY 1, 2;

/* with clause practice */
WITH daily_revenue AS (
  SELECT
    DATE(created_at) AS dt,
    ROUND(SUM(price), 2) AS rev
  FROM purchases
  WHERE refunded_at IS NULL
  GROUP BY 1
)
SELECT *
FROM daily_revenue
ORDER BY dt
LIMIT 10;

/* using a with clause to get daily active user
  notice how the keyword 'WITH' is used only once */
WITH daily_revenue AS (
	SELECT
  	DATE(created_at) AS dt,
  	ROUND(SUM(price), 2) AS rev
  FROM purchases
  WHERE refunded_at IS NULL
  GROUP BY 1
),
daily_players AS (
	SELECT
  	DATE(created_at) AS dt,
  	COUNT(DISTINCT user_id) AS players
  FROM gameplays
  GROUP BY 1
)
SELECT *
FROM daily_players
ORDER BY dt;

/* joining to with clauses using
  the USING (dt) shorthand notation
  since both CTEs have dt in common */
WITH daily_revenue AS (
	SELECT
  	DATE(created_at) AS dt,
  	ROUND(SUM(price), 2) AS rev
  FROM purchases
  WHERE refunded_at IS NULL
  GROUP BY 1
),
daily_players AS (
	SELECT
  	DATE(created_at) AS dt,
  	COUNT(DISTINCT user_id) AS players
  FROM gameplays
  GROUP BY 1
)
SELECT
	daily_revenue.dt AS day_played,
  ROUND(daily_revenue.rev / daily_players.players, 2) AS arpu
FROM daily_revenue
	JOIN daily_players USING (dt)
WHERE arpu > 0.5;

/*
    commit point
*/

/* doing a self join to figure out
  the 1 day retention metric */
SELECT
	DATE(g1.created_at) AS dt,
  g1.user_id
FROM gameplays AS g1
	JOIN gameplays AS g2
  ON g1.user_id = g2.user_id
ORDER BY dt
LIMIT 100;

/* this query is using an 'AND'
  in the self join, a continuation of the last query
  to find 1 day retention */
SELECT
	DATE(g1.created_at) AS dt,
  g1.user_id,
  g2.user_id
FROM gameplays AS g1
	JOIN gameplays AS g2
  ON g1.user_id = g2.user_id
  AND DATE(g1.created_at) = DATE(DATETIME(g2.created_at, '-1 day'))
ORDER BY dt
LIMIT 100;

/* a more useful version of the above query */
SELECT
	DATE(g1.created_at) AS dt,
  COUNT(DISTINCT g1.user_id) AS total_users,
  COUNT(DISTINCT g2.user_id) AS retained_users
FROM gameplays AS g1
	LEFT JOIN gameplays AS g2
  ON g1.user_id = g2.user_id
  AND DATE(g1.created_at) = DATE(DATETIME(g2.created_at, '-1 day'))
GROUP BY 1
ORDER BY 1
LIMIT 100;

/* now calculating retention */
SELECT
	DATE(g1.created_at) AS dt,
  ROUND(100 * COUNT(DISTINCT g2.user_id) /
  COUNT(DISTINCT g1.user_id), 2) AS retention
FROM gameplays AS g1
	LEFT JOIN gameplays AS g2
  ON g1.user_id = g2.user_id
  AND DATE(g1.created_at) = DATE(DATETIME(g2.created_at, '-1 day'))
GROUP BY 1
ORDER BY 1
LIMIT 100;