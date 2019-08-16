SELECT *
FROM customers
JOIN orders
  ON customers.customer_id = orders.customer_id;

SELECT orders.order_id,
  customer.customer_name
FROM orders
JOIN customers
  ON orders.customer_id = customers.customer_id;

SELECT *
FROM orders
JOIN subscriptions
  ON orders.subscription_id = subscriptions.subscription_id
WHERE subscriptions.description = 'Fashion Magazine';

/****************/
/* commit point */
/****************/

SELECT COUNT(*) AS 'total newspaper subscribers'
FROM newspaper_subscribers_table;

SELECT COUNT(*) AS 'total online Fashion Magazine subscribers'
FROM online_suscribers_table
WHERE subscription_type = 'Fashion Magazine';

SELECT COUNT(*) AS 'total online & newspaper subscribers'
FROM online_subscribers_table
JOIN newspaper_subscribers_table
  ON online_subscribers_table.customer_id = newpaper_subscribers_table.customer_id;

SELECT *
FROM newspaper
LEFT JOIN online
  ON newspaper.id = online.id;

SELECT *
FROM newspaper
LEFT JOIN online
 ON newspaper.id = online.id
WHERE online.id IS NULL;

/* primary key to foreign key */
SELECT *
FROM classes
JOIN students
  ON classes.id = students.class_id;


/* 7. Cross Join */
/* This will get all the users from the month of march
 The 'WHERE' clause uses counter intuitive syntax to
 get only month '3' e.g. < > */
SELECT COUNT(*) as 'Total subscribers in March'
FROM newspaper
WHERE month_start < 3 AND month_end > 3;

SELECT *
FROM newspaper
CROSS JOIN months;

SELECT *
FROM newspaper
CROSS JOIN months
WHERE months.month > newspaper.start_month
  AND months.month < newspaper.end_month;

/* find how many subscribers there were per month */
SELECT
  `month`,
  COUNT(*) as subscribers
FROM newspaper_table
CROSS JOIN months_table
WHERE months_table.month > newspaper_table.start_month
  AND months_table.month < newspaper_table.end_month
GROUP BY `month`;

/* 8. UNION */
SELECT *
FROM newspaper_subscriptions_table
UNION
SELECT *
FROM online_subscriptions_table;

/* 9. WITH */
WITH subscription_totals AS (
  SELECT
    customer_id,
    COUNT(subscription_id) as subscriptions
  FROM orders
  GROUP BY customer_id
)
SELECT
  customers_table.customer_name,
  subscription_totals.subscriptions
FROM subscription_totals
JOIN customers_table
  ON subscription_totals.customer_id = customers_table.customer_id;

/* other random SQL prac */
select *,
  case product_name
  	when 'kale-smoothie'    then 'smoothie'
    when 'banana-smoothie'  then 'smoothie'
    when 'orange-juice'     then 'drink'
    when 'soda'             then 'drink'
    when 'blt'              then 'sandwich'
    when 'grilled-cheese'   then 'sandwich'
    when 'tikka-masala'     then 'dinner'
    when 'chicken-parm'     then 'dinner'
    else 'other'
  end as category
from order_items
order by id
limit 100;











/* end of this SQL file */
