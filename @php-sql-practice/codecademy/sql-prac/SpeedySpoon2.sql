/* query orders by date */
SELECT
	DATE(ordered_at) AS the_date,
	COUNT(1) AS orders
FROM orders
GROUP BY 1
ORDER BY 1
LIMIT 10;

SELECT *
FROM orders
LIMIT 10;

/* get daily revenue of kale smoothie product */
SELECT
	DATE(ordered_at) AS the_day,
	ROUND(SUM(amount_paid), 2) AS revenue_for_day
FROM orders
	INNER JOIN order_items
  ON orders.id = order_items.order_id
WHERE name = 'kale-smoothie'
GROUP BY 1
ORDER BY 1
LIMIT 10;

/* look at data real quick */
SELECT * FROM orders
LIMIT 10;
SELECT * FROM order_items
LIMIT 10;


/*
  commit point
*/


/* total revenue per product name */
SELECT
	name,
  ROUND(SUM(amount_paid), 2) AS total_revenue
FROM order_items
GROUP BY name
ORDER BY 2 DESC;

/* percent of revenue for each product */
SELECT
  `name` AS name_of_product,
  ROUND(SUM(amount_paid) /
  (SELECT SUM(amount_paid) FROM order_items) * 100, 2) AS pct
FROM order_items
GROUP BY 1,
ORDER BY pct DESC;

/* This query aggregates names to a category via a
  case statement and group by */
SELECT
	case `name`
  	when 'kale-smoothie'    then 'smoothie'
    when 'banana-smoothie'  then 'smoothie'
    when 'orange-juice'     then 'drink'
    when 'soda'             then 'drink'
    when 'blt'              then 'sandwich'
    when 'grilled-cheese'   then 'sandwich'
    when 'tikka-masala'     then 'dinner'
    when 'chicken-parm'     then 'dinner'
    else 'other'
  end AS category,
  ROUND(SUM(amount_paid) /
        (SELECT SUM(amount_paid) FROM order_items) * 100, 2) as pct
FROM order_items
GROUP BY 1
ORDER BY 2 DESC
LIMIT 100;

/*
  commit point
 */

/* count the distinct orders relative to name */
SELECT
	`name`,
  COUNT(DISTINCT order_id) AS total_orders
FROM order_items
GROUP BY 1
ORDER BY 1;

/* get the number of people making the order
  for the re order rate per person, I think */
SELECT
	`name` AS product_name,
  ROUND(1.0 * COUNT(DISTINCT order_id) /
        COUNT(DISTINCT delivered_to), 2) AS reorder_rate
FROM order_items
	JOIN orders ON orders.id = order_items.order_id
GROUP BY 1
ORDER BY 2 DESC;