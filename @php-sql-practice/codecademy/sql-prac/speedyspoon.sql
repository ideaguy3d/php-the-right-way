SELECT *
FROM orders
ORDER BY id
LIMIT 100;

SELECT *
FROM order_items
ORDER BY id
LIMIT 100;

SELECT date(ordered_at) as dateOrdered
FROM orders_table
ORDER BY 1
LIMIT 100;

/* group rows by 'order_at' column
  and count how many orders there were
  for each date */
SELECT DATE(ordered_at), 1,
FROM orders_table
GROUP BY 1
ORDER BY 1 ASC
limit 100;

select
	date(ordered_at) as dateOrdered,
  count(1) as totalOrders
from orders
group by 1
order by 1 asc
limit 100;

/* sum the daily order amount
 by INNER JOIN ing two tables */
SELECT
  date(ordered_at) as 'order date',
  round(sum(amount_paid), 2) as 'total sales'
FROM orders
JOIN order_items
  ON orders.id = order_items.order_id
GROUP BY 1
ORDER BY 1;

select date(timestamp) as 'the date',
	merchant_order_id as 'Merchant Order ID'
from majide_fba_sales_v1
limit 20;

/* find the total sales by day */
select
	date(timestamp) as 'the date',
	round(sum(item_price),2) as 'total sales'
from majide_fba_sales_v1
group by 1
order by 1 desc;

/* 3. daily count 2 */
/* group the date column by the same
  date and count how many orders there
  were on that date */
select date(ordered_at), count(1)
from orders_table
group by 1
order by 1;

/* same query as above, but with
  a where clause */
select
  date(ordered_at),
  round(sum(amount_paid), 2)
from orders
join order_items
	on orders.id = order_items.order_id
where name = 'kale-smoothie'
group by 1
order by 1;

SELECT
  order_items.name as Name,
  date(orders.ordered_at) as OrderDate,
  round(sum(order_items.amount_paid), 2) as Sales
FROM orders
INNER JOIN order_items
  ON orders.id = order_items.order_id
WHERE order_items.name = 'kale-smoothie'
GROUP BY OrderDate
ORDER BY OrderDate;

/* basically how many orders each product had */
select
	name,
  count(*) as TableTotal
from order_items
group by name;

select count(*) as 'Total Kale Smoothies sold'
from order_items
where name = 'kale-smoothie';

/* Advanced Aggregate  */
/* Sub Query prac */
SELECT `name`,
  round(sum(amount_paid) /
  (SELECT sum(amount_paid) FROM order_items) * 100.0, 2) as pct
FROM order_items
GROUP BY 1
ORDER BY pct DESC;

SELECT
  product_name,
  round(sum(product_price) /
    (select sum(product_price) from item_orders) * 100.0, 2) as pct
FROM order_items
GROUP BY product_name
ORDER BY pct DESC;

/* case prac */
select *,
  case name
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

select
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
  end as category,
  round(1.0 * sum(amount_paid) /
    (select sum(amount_paid) from order_items) * 100.0, 2) as pct
from order_items
group by category
order by pct desc;

/* creating a metric called 'reorder rate'
  'reorder rate' is a ratio */
select name,
  round(1.0 * COUNT(DISTINCT order_id) /
    COUNT(DISTINCT delivered_to), 2) as reorder_rate
from orders
  inner join order_items on orders.id = order_items.order_id
group by name
order by reorder_rate DESC;









/* end of this SQL file */