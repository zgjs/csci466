/*
 * CSCI 466 - Section 2
 * Assignment 6 - SQL-DML - Single Table
 * zgjs <zgjs@zgjs.dev>
 */

-- 1. Select the BabyName database.
USE BabyName;

-- 2. Show all of the tables in the database.
SHOW TABLES;

-- 3. Show all of the columns and their types for each table in the database.
DESCRIBE BabyName;

-- 4. Show all of the years (once only) where your first name appears.
--    Some people's names may not be present in the databse.
--    If your name is one of those, then use 'Chad' if you are male, or 'Stacy' if you are female.
--    If you don't feel you fit inot one of those, feel free to use 'Pat'.
\! echo "Years that 'zgjs' appears in BabyName':"
SELECT DISTINCT year FROM BabyName WHERE name = 'zgjs';

-- 5. Show all of the names from your birth year. (Showing each name only once.)
\! echo "Names from 1990:"
SELECT DISTINCT name FROM BabyName WHERE year = 1990;

-- 6. Display the most popular male and female names from the year of your birth.
\! echo "Most popular male and female names in 1990:"
(
    SELECT gender,name
    FROM BabyName
    WHERE gender = 'M' AND year = 1990
    ORDER BY count DESC
	LIMIT 1
) UNION (
    SELECT gender,name
	FROM BabyName
	WHERE gender = 'F' AND year = 1990
	ORDER BY count DESC
	LIMIT 1
);

-- 7. Show all the information available about names similar to your name (or the one you adopted from above),
--    sorted in alphabetical order by name, then within that, by count, and finally, by the year.
\! echo "All info about names similar to 'zgjs':"
SELECT * FROM BabyName WHERE name LIKE '%zgjs%' ORDER BY name,count,year;

-- 8. Show how many rows there are in the table.
\! echo "How many rows in the BabyName table:"
SELECT COUNT(*) FROM BabyName;

-- 9. Show how many names there were in the year 1972.
\! echo "How many names there were in the year 1972:"
SELECT COUNT(DISTINCT name) FROM BabyName WHERE year = 1972;

-- 10. Show how many names are in the table for each sex from the year 1953.
\! echo "How many names for each sex in 1953:"
SELECT gender,COUNT(DISTINCT name) FROM BabyName WHERE year = 1953 GROUP BY gender;

-- 11. List how many different names there are for each place.
\! echo "How many different names for each place:"
SELECT place,COUNT(DISTINCT name) FROM BabyName GROUP BY place;

