DROP TABLE IF EXISTS visits, dogs;
CREATE TABLE dogs (
   `dog id` INT AUTO_INCREMENT PRIMARY KEY,
   `breed` VARCHAR(255),
   `name` VARCHAR(30)
);
INSERT INTO dogs (breed, name) VALUES
('Afador', 'Alfie'),
('Bull Mastif', 'Betty'),
('Chihuahua', 'Charlie'),
('Dane', 'Douglas'),
('English Setter','Edward');
CREATE TABLE visits (
   `visit id` INT AUTO_INCREMENT PRIMARY KEY,
   `dog id` INT,
   `date` DATE,
   FOREIGN KEY (`dog id`) REFERENCES dogs (`dog id`)
);
INSERT INTO visits (`dog id`, `date`) VALUES
(1, '2020-09-01'),
(2, '2020-09-02'),
(3, '2020-09-03'),
(4, '2020-09-04'),
(5, '2020-09-05'),
(1, '2020-09-06'),
(2, '2020-09-07'),
(3, '2020-09-08');
ALTER TABLE visits
ADD `time` TIME;
UPDATE visits
SET `time` = '09:30:00'
WHERE `date` BETWEEN DATE('2020-09-02') AND DATE('2020-09-05');