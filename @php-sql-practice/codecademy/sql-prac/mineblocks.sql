select *
from purchases
order by id
limit 10;

select *
from gameplays
order by id
limit 10;

/* the daily revenue */
select
  date(created_at) as the_day,
  sum(price) as sales_for_day
from purchases
group by 1
order by 1;

/* same query as above but excluding refunds */
select
	date(created_at) as day,
	round(sum(price), 2) as daily_rev
from purchases
where refunded_at is not null
group by 1
order by 1;

select
  date(created_at) as day_played,
  count(distinct user_id) as dau
from gameplays
group by 1
order by 1;

select
  date(created_at) as date_played,
  platform,
  count(distinct user_id) as dau
from the_table
group by 1, 2
order by 1, 2

/* get the 'average revenue per purchasing user */
select
  date(created_at) as the_date,
  round(sum(price) / count(distinct user_id), 2) as the_arppu
from purchases
where refunded_at is null
group by 1
order by 1;

with daily_revenue as (
  select
    date(created_at) as dt,
    round(sum(price), 2) as rev
  from purchases
  where refunded_at is null
  group by 1
)
select * daily_revenue order by dt;

/* query for average daily revenue of all players */
with daily_revenue as (
  select
    date(created_at) as zdate,
    round(sum(price), 2) as rev
  from purchases
  where refunded_at is null
  group by 1
),
daily_players as (
  select
    date(created_at) as zdate,
    count(distinct user_id) as players
  from gameplays
  group by 1
)
select
  daily_revenue.zdate as z_date_played,
  round(daily_revenue.rev / daily_players.players, 2) as z_arpu
from daily_revenue
  inner join daily_players using (zdate);

/*
  ~ self join practice to find '1 Day Retention' metric
*/

select
  date(g1.created_at) as dt,
  round(100 * count(distinct g2.user_id) /
    count(distinct g1.user_id), 2) as retention
from gameplays as g1
  left join gameplays as g2
  on g1.user_id = g2.user_id
  and date(g1.created_at) = date(datetime(g2.created_at, '-1 day'))
group by 1
order by 1 asc
limit 100;



















/* end of this SQL file */