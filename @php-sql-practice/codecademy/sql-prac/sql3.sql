select *
from gameplays
order by user_id
limit 50;


select
  date(g1.created_at) as dt,
  round(100 * count(distinct g2.user_id)  /
        count(distinct g1.user_id), 2) as retention
  from gameplays as g1
  left join gameplays as g2
  on g1.user_id = g2.user_id
  and date(g1.created_at) = date(datetime(g2.created_at, '-1 day'))
group by 1
order by 1 ASC
limit 50;