-- CSCI 466 Databases, section 2
-- zgjs <zgjs@zgjs.dev>
-- Assignment 5, due Wednesday, October 7, 2020

-- 1. Run a statement that will remove all of the tables that will be created below. This will allow the script to be run again without any errors caused by existing tables.

DROP TABLE IF EXISTS visits, dogs;

-- 2. Create a table called dogs with a dog id, a breed, and a name. The id of the dog should be the primary key, and should be automatically assigned to the next available value when inserting a new row into the table.

CREATE TABLE dogs (
   `dog id` INT AUTO_INCREMENT PRIMARY KEY,
   `breed` VARCHAR(255),
   `name` VARCHAR(30)
);

-- 3. Put five rows into the dog table with example data.

INSERT INTO dogs (breed, name) VALUES
('Afador', 'Alfie'),
('Bull Mastif', 'Betty'),
('Chihuahua', 'Charlie'),
('Dane', 'Douglas'),
('English Setter','Edward');

-- 4. Run the command DESCRIBE dogs;

DESCRIBE dogs;

-- 5. Run the command SELECT * FROM dogs;

SELECT * FROM dogs;

-- 6. Createa table called visits that contains a visit id as primary key. It should also have a foreign key that references a row in the dog table, and an attribute to hold the date that the visit took place.

CREATE TABLE visits (
   `visit id` INT AUTO_INCREMENT PRIMARY KEY,
   `dog id` INT,
   `date` DATE,
   FOREIGN KEY (`dog id`) REFERENCES dogs (`dog id`)
);

-- 7. Put at least eight new rows into the visits table. Since there are only five dogs, this means that some dogs will have multiple visits.

INSERT INTO visits (`dog id`, `date`) VALUES
(1, '2020-09-01'),
(2, '2020-09-02'),
(3, '2020-09-03'),
(4, '2020-09-04'),
(5, '2020-09-05'),
(1, '2020-09-06'),
(2, '2020-09-07'),
(3, '2020-09-08');

-- 8. Run the command DESCRIBE visits;

DESCRIBE visits;

-- 9. Run the command SELECT * FROM visits;

SELECT * FROM visits;

-- 10. Add a column to the visits table to hold the time of the visit.

ALTER TABLE visits
ADD `time` TIME;

-- 11. Change the value for the newly-added `time` attribute in several of the existing rows.

UPDATE visits
SET `time` = '09:30:00'
WHERE `date` BETWEEN DATE('2020-09-02') AND DATE('2020-09-05');

-- 12. Run the command SELECT * FROM visits; again.

SELECT * FROM visits;

-- That's all folks!

