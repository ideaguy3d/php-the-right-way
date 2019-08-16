SELECT *
FROM flights
LIMIT 10;

/* non-correlated query
 with airports w/elevation < 2000 */
SELECT *
FROM flights
WHERE origin IN (
	SELECT code
  FROM airports
  WHERE elevation < 2000
);

/* non-correlated query
 where faa_region = 'ASO' */
SELECT *
FROM flights
WHERE origin IN (
	SELECT code
  FROM airports
  WHERE faa_region = 'ASO'
)
LIMIT 10;

/* find average distance of flights by `day of week`
  and `month` using a subquery on same table */
SELECT a.dep_month,
	a.dep_day_of_week,
  AVG(a.flight_distance) AS average_distance
FROM (
    SELECT dep_month,
      dep_day_of_week,
      dep_date,
      SUM(distance) AS flight_distance
    FROM flights
    GROUP BY 1,2,3
	) a
GROUP BY 1,2
ORDER BY 1
LIMIT 20;

/* find average distance of flights by `day of week`
  and `month` using a subquery on same table */
SELECT
  a.dep_month,
  a.dep_day_of_week,
  AVG(a.flight_distance) AS average_distance
FROM (
  SELECT
    dep_month,
    dep_day_of_week,
    dep_date,
    SUM(distance) AS flight_distance
  FROM flights
  GROUP BY 1,2,3
) a
GROUP BY 1,2
ORDER BY 1,2
LIMIT 10;

/* find list of flights that are above average
 for carrier distance */
SELECT id, distance
FROM flights AS f
WHERE distance > (
  SELECT AVG(distance)
  FROM flights
  WHERE carrier = f.carrier
);

/* find list of flights with less
  average distance from their carrier  */
SELECT id, distance
FROM flights AS f
WHERE distance < (
  SELECT AVG(distance)
  FROM flights
  WHERE carrier = f.carrier
);

/* same query as above without distance column */
SELECT id
FROM flights AS f
WHERE distance < (
	SELECT AVG(distance)
  FROM flights
  WHERE carrier = f.carrier
);

/* view flights by carrier, flight id,
 and sequence number */
SELECT
  carrier,
  id,
  (
    SELECT COUNT(*)
    FROM flights AS f
    WHERE f.id < flights.id
      AND f.carrier = flights.carrier
  ) + 1 AS flight_sequence_number
FROM flights
LIMIT 20;

/* view flights by origin, flight id,
 and sequence number */
SELECT id,
  origin,
  (
    SELECT COUNT(*)
    FROM flights AS f
    WHERE f.id < flights.id
      AND f.origin = flights.origin
  ) + 1 AS flight_sequence_number
FROM flights;
/* wrote same query as above
  to try to understand it better */
SELECT
  origin,
  id,
  (
    SELECT COUNT(*)
    FROM flights AS f
    WHERE f.id < flights.id
      AND f.origin = flights.origin
  ) + 1 AS flight_sequence_number
FROM flights;
/* wrote same query as above
  to try to understand it better */
SELECT id,
	origin,
  (
  	SELECT COUNT(*)
    FROM flights AS f2
    WHERE f2.id < f1.id
    	AND f2.origin = f1.origin
  ) + 1 AS flight_sequence_number
FROM flights AS f1;