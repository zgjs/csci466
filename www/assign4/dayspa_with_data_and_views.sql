/* CSCI 466 - Section 2
 * Assignment 4 - Serenity Springs Day Spa
 * SQL Model
 * zgjs <zgjs@zgjs.dev>
 *
 * This is my implementation of the Day Spa assignment, including all the views.
 * Implementing the views in the database was not requried in the asssignment, but
 * it helped me make sure that my entities/relationships could support all of the
 * requried views from the assignment.
 *
 * Relations Schema in third normal form (3NF):
 *    Entities:
 *      • Services(_Service Name_, Price)
 *      • Persons(_ID_, Name, Phone Number, Physical Address, Email)
 *      • Employees(_Employee ID†_, Job Title, Service Name†, Pay Rate)
 *      • Clients(_Client ID†_)
 *    More-than-binary Relationships:
 *      • PreferredFor(_Client ID†_, _Employee ID†_, _Service Name†_)
 *      • Appointments(_Client ID†_, _Employee ID†_, _Service Name†_, Date) 
 *
 * Tree structure:
 *
 * ┬┬ TABLES
 * ││
 * │├┬ Entities
 * │││
 * ││├┬ Person
 * │││├─ _ID_
 * │││├─ Name
 * │││├─ Phone Number
 * │││├─ Physical Address
 * │││└─ Email
 * │││
 * ││├┬ Employee (Person)
 * │││├─ _ID†_
 * │││├─ Job Title
 * │││├─ Specialty†
 * │││└─ Pay Rate
 * │││
 * ││├┬ Client (Person)
 * │││└─ _ID†_
 * │││
 * ││└┬ Service
 * ││ ├─ _Name_
 * ││ └─ Price
 * ││
 * │└┬ More-than-binary Relationsips
 * │ │
 * │ ├┬ PreferredFor
 * │ │├─ _Client ID†_
 * │ │├─ _Employee ID†_
 * │ │└─ _Service†_
 * │ │
 * │ └┬ Appointment
 * │  ├─ _Employee ID†_
 * │  ├─ _Client ID†_
 * │  ├─ _Employee ID†_
 * │  ├─ _Service†_
 * │  └─ _Date_
 * │
 * └┬ VIEWS 
 *  ├─ Client Bills
 *  ├─ Upcoming client appointments
 *  ├─ List of clients
 *  ├─ List of employees
 *  ├─ List of services and their prices
 *  ├─ Employees' work schedules
 *  ├─ List clients scheduled for a particular day
 *  └─ Client preferences for services
 */

-- Clean database --
\! echo "Dropping views and tables...";
DROP VIEW IF EXISTS
   `Appointment Reminders`,
    Billing,
   `Client Appointments By Day`,
   `Client List`,
   `Client Preferences Matrix`,
   `Clients Scheduled Today`, 
   `Employee List`,
   `Employee Schedules By Day`,
   `Services List`;
DROP TABLE IF EXISTS Appointment,PreferredFor,Employee,Client,Person,Service;

\! echo "";
\! echo "╔══════ Tables list ════════════╗";
SHOW TABLES;
\! echo "";


-- Create tables --
\! echo "";
\! echo "╔══════════════════════════════════════╗";
\! echo "║                                      ║";
\! echo "║        Creating Entity tables        ║";
\! echo "║                                      ║";
\! echo "╚══════════════════════════════════════╝";
\! echo ""
\! echo "Creating table: Service";
CREATE TABLE Service(
   Name VARCHAR(20) PRIMARY KEY,
   Price DECIMAL(5,2) NOT NULL
);
\! echo "Creating table: Person";
CREATE TABLE Person(
   ID INT AUTO_INCREMENT PRIMARY KEY,
   Name VARCHAR(255) NOT NULL,
  `Phone Number` VARCHAR(25), -- Phone numbers are normally in format "(000) 000-0000" but might be as long as "+000 00 0000 0000, 000000"
  `Physical Address` VARCHAR(255),
  `Email` VARCHAR(255)
);
\! echo "Creating table: Client";
CREATE TABLE Client(
   ID INT PRIMARY KEY,
   FOREIGN KEY (ID) REFERENCES Person(ID)
);
\! echo "Creating table: Employee";
CREATE TABLE Employee(
   ID INT PRIMARY KEY,
  `Job Title` VARCHAR(255) NOT NULL,
   Specialty VARCHAR(20) NOT NULL,
  `Pay Rate` DECIMAL(5,2) NOT NULL,
   FOREIGN KEY (ID) REFERENCES Person(ID),
   FOREIGN KEY (Specialty) REFERENCES Service(Name)
);
\! echo "";
\! echo "╔══════════════════════════════════════╗";
\! echo "║                                      ║";
\! echo "║     Creating Relationship tables     ║";
\! echo "║                                      ║";
\! echo "╚══════════════════════════════════════╝";
\! echo ""
\! echo "Creating table: PreferredFor";
Create TABLE PreferredFor(
  `Client ID` INT NOT NULL,
  `Employee ID` INT NOT NULL,
  `Service Name` VARCHAR(20) NOT NULL,
  Primary KEY (`Client ID`, `Employee ID`, `Service Name`),
  FOREIGN KEY (`Client ID`) REFERENCES Client(ID),
  FOREIGN KEY (`Employee ID`) REFERENCES Employee(ID),
  FOREIGN KEY (`Service Name`) REFERENCES Service(Name)
);
\! echo "Creating table: Appointment";
CREATE TABLE Appointment(
   `Service Name` VARCHAR(20) NOT NULL,
   `Client ID` INT NOT NULL,
   `Employee ID` INT NOT NULL,
   `Date` DATETIME,
   PRIMARY KEY (`Service Name`, `Client ID`, `Employee ID`, `Date`),
   FOREIGN KEY (`Service Name`) REFERENCES Service(Name),
   FOREIGN KEY (`Client ID`) REFERENCES Client(ID),
   FOREIGN KEY (`Employee ID`) REFERENCES Employee(ID)
);
\! echo "Done creating tables!";
-- Create views --
\! echo "";
\! echo "╔══════════════════════════════════════╗";
\! echo "║                                      ║";
\! echo "║            Creating views            ║";
\! echo "║                                      ║";
\! echo "╚══════════════════════════════════════╝";
\! echo "";

\! echo "Creating view: Billing";
CREATE VIEW `Billing` AS
SELECT DISTINCT `Client ID`,Person.`Name`,SUM(`Price`) AS 'Total Billed'
FROM `Appointment` INNER JOIN `Service` ON Service.`Name` = Appointment.`Service Name`
INNER JOIN `Person` ON Person.`ID` = Appointment.`Client ID`
GROUP BY `Client ID`;

\! echo "Creating view: Client List";
CREATE VIEW `Client List` AS
SELECT `Name`, `Phone Number`, `Physical Address`, `Email`
FROM `Client` INNER JOIN `Person` USING(`ID`);

\! echo "Creating view: Client Preferences Matrix";
CREATE VIEW `Client Preferences Matrix` AS
SELECT Client.`Name` AS 'Client',
  MAX(case WHEN `Service Name` = 'Hair' THEN Employee.`Name` ELSE '' END) AS 'Hair',
  MAX(case WHEN `Service Name` = 'Makeup' THEN Employee.`Name` ELSE '' END) AS 'Makeup',
  MAX(case WHEN `Service Name` = 'Manicure' THEN Employee.`Name` ELSE '' END) AS 'Manicure',
  MAX(case WHEN `Service Name` = 'Massage' THEN Employee.`Name` ELSE '' END) AS 'Massage',
  MAX(case WHEN `Service Name` = 'Wax' THEN Employee.`Name` ELSE '' END) AS 'Wax'
FROM PreferredFor
INNER JOIN Person AS Client ON Client.`ID` = PreferredFor.`Client ID`
INNER JOIN Person AS Employee ON Employee.`ID` = PreferredFor.`Employee ID`
GROUP BY Client.`Name`;

\! echo "Creating view: Employee List";
CREATE VIEW `Employee List` AS
SELECT `Name`, `Job Title`, `Specialty`, `Pay Rate`, `Phone Number`, `Physical Address`, `Email`
FROM `Employee` INNER JOIN `Person` USING(`ID`);

\! echo "Creating view: Services List";
CREATE VIEW `Services List` AS
SELECT `Name` AS 'Service',CONCAT('$',Price) AS Price FROM Service;

\! echo "Creating view: Clients Scheduled Today";
CREATE VIEW `Clients Scheduled Today` AS
SELECT DATE_FORMAT(`Date`, "%h %i %p") AS 'Time',Client.`Name` AS 'Client', Appointment.`Service Name` AS 'Service', Employee.`Name` AS 'Employee'
FROM Appointment
INNER JOIN Person AS Client ON Client.`ID` =  Appointment.`Client ID`
INNER JOIN Person AS Employee ON Employee.`ID` =  Appointment.`Employee ID`
WHERE DATE(`Date`) = CURDATE()
GROUP BY TIME(`Date`),Client.`Name`;

\! echo "Creating view: Appointment Reminders";
CREATE VIEW `Appointment Reminders` AS
SELECT Client.`Phone Number`, CONCAT(Client.`Name`, ' has a ', `Service Name`, ' appointment with ', Employee.`Name`, DATE_FORMAT(`Date`, " on %b %d, at %h:%i %p.")) AS 'Message'
FROM Appointment
INNER JOIN Person AS Client ON Client.`ID` = Appointment.`Client ID`
INNER JOIN Person AS Employee ON Employee.`ID` = Appointment.`Employee ID`
WHERE `Date` BETWEEN DATE_ADD(NOW(),INTERVAL 1 DAY) AND DATE_ADD(NOW(),INTERVAL 3 DAY)
ORDER BY `Date` ASC LIMIT 20;

\! echo "Creating view: Client Appointments By Day";
CREATE VIEW `Client Appointments By Day` AS
SELECT DISTINCT DATE_FORMAT(`Date`, "%h %i %p") AS 'Time',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 6,CONCAT(Client.`Name`, '-', Appointment.`Service Name`),NULL) SEPARATOR ', '),'[Available]') AS 'Sunday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 0,CONCAT(Client.`Name`, '-', Appointment.`Service Name`),NULL) SEPARATOR ', '),'[Available]') AS 'Monday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 1,CONCAT(Client.`Name`, '-', Appointment.`Service Name`),NULL) SEPARATOR ', '),'[Available]') AS 'Tuesday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 2,CONCAT(Client.`Name`, '-', Appointment.`Service Name`),NULL) SEPARATOR ', '),'[Available]') AS 'Wednesday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 3,CONCAT(Client.`Name`, '-', Appointment.`Service Name`),NULL) SEPARATOR ', '),'[Available]') AS 'Thursday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 4,CONCAT(Client.`Name`, '-', Appointment.`Service Name`),NULL) SEPARATOR ', '),'[Available]') AS 'Friday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 5,CONCAT(Client.`Name`, '-', Appointment.`Service Name`),NULL) SEPARATOR ', '),'[Available]') AS 'Saturday'
FROM Appointment
INNER JOIN Person AS Client ON Client.`ID` =  Appointment.`Client ID`
INNER JOIN Person AS Employee ON Employee.`ID` =  Appointment.`Employee ID`
WHERE `Date` BETWEEN DATE_ADD(NOW(),INTERVAL -1 DAY) AND DATE_ADD(NOW(),INTERVAL 6 DAY)
GROUP BY TIME(`Date`);
\! echo "Creating view: Employee Schedules By Day";
CREATE VIEW `Employee Schedules By Day` AS
SELECT DISTINCT DATE_FORMAT(`Date`, "%h %i %p") AS 'Time',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 6,Employee.`Name`,NULL) ORDER BY Employee.`Name` SEPARATOR ', '),'[Unscheduled]') AS 'Sunday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 0,Employee.`Name`,NULL) ORDER BY Employee.`Name` SEPARATOR ', '),'[Unscheduled]') AS 'Monday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 1,Employee.`Name`,NULL) ORDER BY Employee.`Name` SEPARATOR ', '),'[Unscheduled]') AS 'Tuesday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 2,Employee.`Name`,NULL) ORDER BY Employee.`Name` SEPARATOR ', '),'[Unscheduled]') AS 'Wednesday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 3,Employee.`Name`,NULL) ORDER BY Employee.`Name` SEPARATOR ', '),'[Unscheduled]') AS 'Thursday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 4,Employee.`Name`,NULL) ORDER BY Employee.`Name` SEPARATOR ', '),'[Unscheduled]') AS 'Friday',
  IFNULL(GROUP_CONCAT(IF(WEEKDAY(`Date`) = 5,Employee.`Name`,NULL) ORDER BY Employee.`Name` SEPARATOR ', '),'[Unscheduled]') AS 'Saturday'
FROM Appointment
INNER JOIN Person AS Employee ON Employee.`ID` =  Appointment.`Employee ID`
WHERE `Date` BETWEEN DATE_ADD(NOW(),INTERVAL -1 DAY) AND DATE_ADD(NOW(),INTERVAL 6 DAY)
GROUP BY TIME(`Date`);

\! echo "Done creating views!";

-- Populate tables --
\! echo "";
\! echo "╔══════════════════════════════════════╗";
\! echo "║                                      ║";
\! echo "║          Populating tables           ║";
\! echo "║                                      ║";
\! echo "╚══════════════════════════════════════╝";
\! echo "";
\! echo "Populating table: Services";
INSERT INTO Service VALUES
('Hair','35.00'),
('Makeup','65.00'),
('Manicure','15.00'),
('Massage','95.00'),
('Wax','45.00');
\! echo "Populating table: Person";
INSERT INTO Person (`Name`, `Phone Number`, `Physical Address`, `Email`) VALUES
('Alice',     '555-555-1234', '123 Any St, Anytown, AK',           'alice@example.com'),
('Bob',       '555-555-2345', '234 Barclay Blvd, Boystown, IL',    'bob@example.com'),
('Charlie',   '555-555-3456', '345 Circle Ct, Canterbury, CT',     'charlie@example.com'),
('David',     '555-555-4567', '456 Dunn Dr, Dover, DE',            'david@example.com'),
('Eve',       '555-555-5678', '567 Edgewater Estate, Ewing, IL',   'eve@example.com'),
('Faythe',    '555-555-6789', '678 Flamingo Falls, Freeport, IL',  'faythe@exampl.ecom'),
('Grace',     '555-555-7890', '789 Green Grove, Genoa, IL',        'grace@example.com'),
('Heidi',     '555-555-8901', '890 Happy Hill, Hawthorn, IL',      'heidi@example.com'),
('Ivan',      '555-555-9012', '901 Ivory Isle, Indianapolis, IN',  'ivan@example.com'),
('Judy',      '555-555-0123', '12 Juniper Jct, Jacksonville, IL',  'judy@example.com'),
('Ken',       '555-555-1234', '23 Killborne St, Kankakee, IL',     'ken@example.com'),
('Lisa',      '555-555-2345', '34 Little Rock Ln, Louisville, LA', 'lisa@example.com'),
('Mallory',   '555-555-3456', '45 Marigold Manor, Malta, MO',      'mal@example.com'),
('Naomi',     '555-555-4567', '56 Normal St, New York, NY',        'naomi@example.com'),
('Olivia',    '555-555-5678', '67 Old Orchard, Oxford, OH',        'olivia@example.com'),
('Peggy',     '555-555-6789', '78 Penny Pl, Palmer, PA',           'margaret@example.com'),
('Quinn',     '555-555-7890', '89 Queen Ann Rd, Quincy, IL',       'qqq@example.com'),
('Rachel',    '555-555-8901', '90 Round Rd, Round Lake, IL',       'rach@example.com'),
('Sybil',     '555-555-9012', '901 Ivory Isle, Indianapolis, IN',  'sysy@example.com'),
('Trudy',     '555-555-0123', '12 Juniper Jct, Jacksonville, IL',  '2d@example.com'),
('Una',       '555-123-4567', '123 Fake St, Chicago, IL',          'una@example.com'),
('Victoria',  '555-234-5678', '124 Fake St, Chicago, IL',          'vikky@example.com'),
('Wendy',     '555-345-6789', '125 Fake St, Chicago, IL',          'wendy@example.com'),
('Xavier',    '555-456-7890', '126 Fake St, Chicago, IL',          'profx@example.com'),
('Yvette',    '555-567-8901', '127 Fake St, Chicago, IL',          'yvette@example.com'),
('Zoey',      '555-678-9012', '128 Fake St, Chicago, IL',          'zoey@example.com'),
('Angela',    '555-789-0123', '129 Fake St, Chicago, IL',          'angela@example.com'),
('Brittany',  '555-890-1234', '130 Fake St, Chicago, IL',          'brit@example.com'),
('Chelsea',   '555-901-2345', '131 Fake St, Chicago, IL',          'chelsea@example.com'),
('Danielle',  '555-012-3456', '132 Fake St, Chicago, IL',          'dani@example.com'),
('Emily',     '555-555-5551', '1 Help Im, Nowhere, XX',            'em@example.com'),
('Franchesca','555-555-5552', '2 Stuck in a, Nowhere, XX',         'franny@example.com'),
('Gina',      '555-555-5553', '3 Database Factory, Nowhere, XX',   'gina@example.com'),
('Hannah',    '555-555-5554', '4 If you can hear me, Nowhere, XX', 'hannah@example.com'),
('Isabelle',  '555-555-5555', '5 Then please, Nowhere, XX',        'bell@example.com'),
('Julia',     '555-555-5556', '6 Let me out!, Nowhere, XX',        'julia@exmaple.com'),
('Kaitlyn',   '555-555-5557', '7 All out of, Nowhere, XX',         'kt@example.com'),
('Luna',      '555-555-5558', '8 Street names, Nowhere, XX',       'luna@example.com'),
('Mary',      '555-555-5559', '9 Nein, Nowhere, XX',               'mary@example.com'),
('Nancy',     '555-555-5510','10 Were no strangers to love',       'nancy@example.com'),
('Ophelia',   '555-555-5520','20 You know the rules and so do I',  'lia@example.com'),
('Patricia',  '555-555-5530','30 A full commitments what Im thinking of','patty@example.com'),
('Queen',     '555-555-5540','40 You wouldnt get this from any other guy','queen@example.com'),
('Rose',      '555-555-5550','50 I just wanna tell you how Im feeling','rose@example.com'),
('Scarlett',  '555-555-5560','60 Gotta make you understand',       'scar@example.com'),
('Terra',     '555-555-5570','70 Never gonna give you up',         'terra@example.com'),
('Ursula',    '555-555-5580','80 Never gonna let you down',        'ursula@example.com'),
('Valorie',   '555-555-5590','90 Never gonna run around and',      'val@example.com'),
('Willow',    '555-555-5100','100 Desert you',                     'willow@example.com'),
('Xena',      '555-555-5110','110 Never gonna make you cry',       'xena@example.com'),
('Yuki',      '555-555-5120','120 Never gonna say goodbye',        'yuki@example.com'),
('Zelda',     '555-555-5130','130 Never gonna tell a lie and hurt you','zelda@example.com');

\! echo "Populating table: Client"
INSERT INTO Client VALUES
(7),
(9),
(10),
(11),
(12),
(13),
(14),
(15),
(16),
(17),
(18),
(19),
(20),
(21),
(22),
(23),
(24),
(25),
(26),
(27),
(28),
(29),
(30),
(31),
(32),
(33),
(34),
(35),
(36),
(37),
(38),
(39),
(40),
(41),
(42),
(43),
(44),
(45),
(46),
(47),
(48),
(49),
(50),
(51),
(52);
\! echo "Populating table: Employee";
INSERT INTO Employee VALUES
(1, 'Manager',            'Makeup',  '30.00'),
(2, 'Head Stylist',       'Hair',    '20.00'),
(3, 'Head Masseuse',      'Massage', '25.00'),
(4, 'Part Time Employee', 'Manicure','10.00'),
(5, 'Part Time Employee', 'Hair',    '11.00'),
(6, 'Part Time Employee', 'Wax',     '10.50'),
(8, 'Part Time Employee', 'Massage', '12.00');
\! echo "Populating table: PreferredFor";
INSERT INTO PreferredFor VALUES
(7,  1, 'Hair'),
(7,  1, 'Makeup'),
(7,  4, 'Manicure'),
(7,  3, 'Massage'),
(7,  6, 'Wax'),
(9,  2, 'Hair'),
(9,  8, 'Massage'),
(10, 1, 'Hair'),
(10, 1, 'Makeup'),
(10, 5, 'Manicure'),
(10, 3, 'Massage'),
(10, 6, 'Wax'),
(11, 5, 'Hair'),
(11, 4, 'Manicure'),
(11, 8, 'Massage'),
(12, 2, 'Hair'),
(12, 5, 'Makeup'),
(12, 6, 'Manicure'),
(12, 3, 'Massage'),
(12, 6, 'Wax'),
(13, 1, 'Hair'),
(13, 1, 'Makeup'),
(13, 4, 'Manicure'),
(13, 8, 'Massage'),
(13, 6, 'Wax'),
(14, 5, 'Hair'),
(14, 1, 'Makeup'),
(14, 4, 'Manicure'),
(14, 3, 'Massage'),
(14, 6, 'Wax'),
(15, 2, 'Hair'),
(15, 5, 'Makeup'),
(15, 8, 'Manicure'),
(15, 8, 'Massage'),
(16, 1, 'Hair'),
(16, 4, 'Manicure'),
(16, 6, 'Wax'),
(17, 5, 'Hair'),
(17, 1, 'Makeup'),
(17, 4, 'Manicure'),
(17, 3, 'Massage'),
(17, 6, 'Wax'),
(18, 2, 'Hair'),
(18, 5, 'Makeup'),
(18, 5, 'Manicure'),
(18, 8, 'Massage'),
(19, 1, 'Hair'),
(19, 4, 'Manicure'),
(19, 3, 'Massage'),
(20, 5, 'Hair'),
(20, 1, 'Makeup'),
(20, 8, 'Manicure'),
(20, 8, 'Massage'),
(20, 6, 'Wax');
\! echo "Populating table: Appointment";
INSERT INTO Appointment (`Date`, `Service Name`, `Client ID`, `Employee ID`) VALUES
('2020-09-27 8:00:00', 'Hair', '9', '2'),
('2020-09-27 8:00:00', 'Hair', '27', '5'),
('2020-09-27 9:00:00', 'Wax', '24', '2'),
('2020-09-27 9:00:00', 'Hair', '11', '5'),
('2020-09-27 10:00:00', 'Hair', '24', '2'),
('2020-09-27 10:00:00', 'Hair', '14', '5'),
('2020-09-27 11:00:00', 'Hair', '12', '2'),
('2020-09-27 11:00:00', 'Manicure', '24', '5'),
('2020-09-27 12:00:00', 'Manicure', '17', '5'),
('2020-09-27 12:00:00', 'Wax', '25', '6'),
('2020-09-27 13:00:00', 'Hair', '17', '2'),
('2020-09-27 13:00:00', 'Hair', '25', '6'),
('2020-09-27 14:00:00', 'Hair', '26', '2'),
('2020-09-27 14:00:00', 'Makeup', '25', '6'),
('2020-09-27 15:00:00', 'Hair', '15', '2'),
('2020-09-27 15:00:00', 'Makeup', '26', '6'),
('2020-09-27 16:00:00', 'Hair', '18', '2'),
('2020-09-27 16:00:00', 'Manicure', '15', '8'),
('2020-09-27 17:00:00', 'Hair', '28', '8'),
('2020-09-27 17:00:00', 'Wax', '13', '6'),
('2020-09-27 18:00:00', 'Hair', '29', '8'),
('2020-09-27 18:00:00', 'Massage', '33', '6'),
('2020-09-27 19:00:00', 'Hair', '30', '8'),
('2020-09-27 19:00:00', 'Manicure', '33', '6'),
('2020-09-27 20:00:00', 'Hair', '32', '8'),
('2020-09-27 20:00:00', 'Wax', '33', '6'),
('2020-09-28 8:00:00', 'Hair', '7', '1'),
('2020-09-28 8:00:00', 'Manicure', '11', '4'),
('2020-09-28 9:00:00', 'Makeup', '7', '1'),
('2020-09-28 9:00:00', 'Hair', '39', '4'),
('2020-09-28 10:00:00', 'Hair', '34', '1'),
('2020-09-28 10:00:00', 'Hair', '41', '4'),
('2020-09-28 11:00:00', 'Hair', '35', '1'),
('2020-09-28 11:00:00', 'Manicure', '19', '4'),
('2020-09-28 12:00:00', 'Manicure', '17', '4'),
('2020-09-28 12:00:00', 'Manicure', '46', '3'),
('2020-09-28 13:00:00', 'Hair', '36', '1'),
('2020-09-28 13:00:00', 'Hair', '47', '3'),
('2020-09-28 14:00:00', 'Hair', '37', '1'),
('2020-09-28 14:00:00', 'Hair', '49', '3'),
('2020-09-28 15:00:00', 'makeup', '37', '1'),
('2020-09-28 15:00:00', 'Hair', '51', '3'),
('2020-09-28 16:00:00', 'Hair', '38', '1'),
('2020-09-28 16:00:00', 'Manicure', '12', '6'),
('2020-09-28 17:00:00', 'Hair', '39', '6'),
('2020-09-28 17:00:00', 'Massage', '7', '3'),
('2020-09-28 18:00:00', 'Wax', '14', '6'),
('2020-09-28 18:00:00', 'Massage', '7', '3'),
('2020-09-28 19:00:00', 'Wax', '7', '6'),
('2020-09-28 19:00:00', 'Massage', '14', '3'),
('2020-09-28 20:00:00', 'Wax', '14', '6'),
('2020-09-28 20:00:00', 'Massage', '9', '3'),
('2020-09-29 8:00:00', 'Hair', '10', '1'),
('2020-09-29 8:00:00', 'Hair', '40', '5'),
('2020-09-29 9:00:00', 'Makeup', '10', '1'),
('2020-09-29 9:00:00', 'Hair', '20', '5'),
('2020-09-29 10:00:00', 'Hair', '44', '1'),
('2020-09-29 10:00:00', 'Hair', '42', '5'),
('2020-09-29 11:00:00', 'Hair', '45', '1'),
('2020-09-29 12:00:00', 'Manicure', '47', '5'),
('2020-09-29 13:00:00', 'Hair', '49', '1'),
('2020-09-29 14:00:00', 'Hair', '50', '1'),
('2020-09-29 15:00:00', 'Hair', '52', '1'),
('2020-09-29 16:00:00', 'Manicure', '52', '1'),
('2020-09-29 17:00:00', 'Manicure', '20', '8'),
('2020-09-30 8:00:00', 'Makeup', '14', '1'),
('2020-09-30 8:00:00', 'Manicure', '13', '4'),
('2020-09-30 9:00:00', 'Hair', '13', '1'),
('2020-09-30 9:00:00', 'Manicure', '14', '4'),
('2020-09-30 10:00:00', 'Makeup', '13', '1'),
('2020-09-30 10:00:00', 'Manicure', '16', '4'),
('2020-09-30 11:00:00', 'Hair', '16', '1'),
('2020-09-30 12:00:00', 'Massage', '16', '4'),
('2020-09-30 13:00:00', 'Wax', '16', '6'),
('2020-09-30 14:00:00', 'Hair', '43', '1'),
('2020-09-30 16:00:00', 'Wax', '20', '6'),
('2020-10-01 9:00:00', 'Manicure', '18', '5'),
('2020-10-01 10:00:00', 'Hair', '18', '1'),
('2020-10-01 10:00:00', 'Makeup', '18', '1'),
('2020-10-02 9:00:00', 'Hair', '17', '1'),
('2020-10-02 10:00:00', 'Makeup', '17', '1'),
('2020-10-02 10:00:00', 'Manicure', '12', '4'),
('2020-10-02 12:00:00', 'Makeup', '20', '1'),
('2020-10-02 16:00:00', 'Hair', '19', '1'),
('2020-10-02 16:00:00', 'Massage', '17', '3'),
('2020-10-02 17:00:00', 'Massage', '10', '3'),
('2020-10-02 18:00:00', 'Massage', '11', '3'),
('2020-10-02 19:00:00', 'Massage', '13', '3'),
('2020-10-03 8:00:00', 'Manicure', '7', '6'),
('2020-10-03 9:00:00', 'Wax', '7', '6'),
('2020-10-03 9:00:00', 'Makeup', '12', '5'),
('2020-10-03 10:00:00', 'Wax', '12', '6'),
('2020-10-03 10:00:00', 'Makeup', '15', '5'),
('2020-10-03 11:00:00', 'Wax', '10', '6'),
('2020-10-03 11:00:00', 'Manicure', '18', '5'),
('2020-10-03 12:00:00', 'Manicure', '10', '5'),
('2020-10-03 12:00:00', 'Massage', '18', '3'),
('2020-10-03 13:00:00', 'Manicure', '19', '3'),
('2020-10-03 14:00:00', 'Massage', '19', '3'),
('2020-10-03 15:00:00', 'Hair', '19', '3'),
('2020-10-03 16:00:00', 'Wax', '17', '6'),
('2020-10-03 16:00:00', 'Makeup', '19', '8'),
('2020-10-03 17:00:00', 'Massage', '20', '3'),
('2020-10-04 8:00:00', 'Hair', '9', '2'),
('2020-10-04 8:00:00', 'Hair', '27', '5'),
('2020-10-04 9:00:00', 'Wax', '24', '2'),
('2020-10-04 9:00:00', 'Hair', '11', '5'),
('2020-10-04 10:00:00', 'Hair', '24', '2'),
('2020-10-04 10:00:00', 'Hair', '14', '5'),
('2020-10-04 11:00:00', 'Hair', '12', '2'),
('2020-10-04 11:00:00', 'Manicure', '24', '5'),
('2020-10-04 12:00:00', 'Manicure', '17', '5'),
('2020-10-04 12:00:00', 'Wax', '25', '6'),
('2020-10-04 13:00:00', 'Hair', '17', '2'),
('2020-10-04 13:00:00', 'Hair', '25', '6'),
('2020-10-04 16:00:00', 'Makeup', '19', '8'),
('2020-10-05 14:00:00', 'Hair', '26', '2'),
('2020-10-05 14:00:00', 'Makeup', '25', '6'),
('2020-10-05 15:00:00', 'Hair', '15', '2'),
('2020-10-05 15:00:00', 'Makeup', '26', '6'),
('2020-10-05 16:00:00', 'Hair', '18', '2'),
('2020-10-05 16:00:00', 'Manicure', '15', '8'),
('2020-10-05 17:00:00', 'Hair', '28', '8'),
('2020-10-05 17:00:00', 'Wax', '13', '6'),
('2020-10-05 18:00:00', 'Hair', '29', '8'),
('2020-10-05 18:00:00', 'Massage', '33', '6'),
('2020-10-05 19:00:00', 'Hair', '30', '8'),
('2020-10-05 19:00:00', 'Manicure', '33', '6'),
('2020-10-05 20:00:00', 'Hair', '32', '8'),
('2020-10-05 20:00:00', 'Wax', '33', '6'),
('2020-10-06 8:00:00', 'Hair', '7', '1'),
('2020-10-06 8:00:00', 'Manicure', '11', '4'),
('2020-10-06 9:00:00', 'Makeup', '7', '1'),
('2020-10-06 9:00:00', 'Hair', '39', '4'),
('2020-10-06 10:00:00', 'Hair', '34', '1'),
('2020-10-06 10:00:00', 'Hair', '41', '4'),
('2020-10-06 11:00:00', 'Hair', '35', '1'),
('2020-10-06 11:00:00', 'Manicure', '19', '4'),
('2020-10-06 12:00:00', 'Manicure', '17', '4'),
('2020-10-06 12:00:00', 'Manicure', '46', '3'),
('2020-10-06 13:00:00', 'Hair', '36', '1'),
('2020-10-06 13:00:00', 'Hair', '47', '3'),
('2020-10-06 14:00:00', 'Hair', '37', '1'),
('2020-10-06 14:00:00', 'Hair', '49', '3'),
('2020-10-06 15:00:00', 'makeup', '37', '1'),
('2020-10-06 15:00:00', 'Hair', '51', '3'),
('2020-10-06 16:00:00', 'Hair', '38', '1'),
('2020-10-06 16:00:00', 'Manicure', '12', '6'),
('2020-10-06 17:00:00', 'Hair', '39', '6'),
('2020-10-06 17:00:00', 'Massage', '7', '3'),
('2020-10-06 18:00:00', 'Wax', '14', '6'),
('2020-10-06 18:00:00', 'Massage', '7', '3'),
('2020-10-06 19:00:00', 'Wax', '7', '6'),
('2020-10-06 19:00:00', 'Massage', '14', '3'),
('2020-10-06 20:00:00', 'Wax', '14', '6'),
('2020-10-06 20:00:00', 'Massage', '9', '3'),
('2020-10-07 8:00:00', 'Hair', '10', '1'),
('2020-10-07 8:00:00', 'Hair', '40', '5'),
('2020-10-07 9:00:00', 'Makeup', '10', '1'),
('2020-10-07 9:00:00', 'Hair', '20', '5'),
('2020-10-07 10:00:00', 'Hair', '44', '1'),
('2020-10-07 10:00:00', 'Hair', '42', '5'),
('2020-10-07 11:00:00', 'Hair', '45', '1'),
('2020-10-07 12:00:00', 'Manicure', '47', '5'),
('2020-10-07 13:00:00', 'Hair', '49', '1'),
('2020-10-07 14:00:00', 'Hair', '50', '1'),
('2020-10-07 15:00:00', 'Hair', '52', '1'),
('2020-10-07 16:00:00', 'Manicure', '52', '1'),
('2020-10-07 17:00:00', 'Manicure', '20', '8'),
('2020-10-08 8:00:00', 'Makeup', '14', '1'),
('2020-10-08 8:00:00', 'Manicure', '13', '4'),
('2020-10-08 9:00:00', 'Hair', '13', '1'),
('2020-10-08 9:00:00', 'Manicure', '14', '4'),
('2020-10-08 10:00:00', 'Makeup', '13', '1'),
('2020-10-08 10:00:00', 'Manicure', '16', '4'),
('2020-10-08 11:00:00', 'Hair', '16', '1'),
('2020-10-08 12:00:00', 'Massage', '16', '4'),
('2020-10-08 13:00:00', 'Wax', '16', '6'),
('2020-10-07 14:00:00', 'Hair', '43', '1'),
('2020-10-07 16:00:00', 'Wax', '20', '6'),
('2020-10-08 9:00:00', 'Manicure', '18', '5'),
('2020-10-08 10:00:00', 'Hair', '18', '1'),
('2020-10-08 10:00:00', 'Makeup', '18', '1'),
('2020-10-09 9:00:00', 'Hair', '17', '1'),
('2020-10-09 10:00:00', 'Makeup', '17', '1'),
('2020-10-09 10:00:00', 'Manicure', '12', '4'),
('2020-10-09 12:00:00', 'Makeup', '20', '1'),
('2020-10-09 16:00:00', 'Hair', '19', '1'),
('2020-10-09 16:00:00', 'Massage', '17', '3'),
('2020-10-09 17:00:00', 'Massage', '10', '3'),
('2020-10-09 18:00:00', 'Massage', '11', '3'),
('2020-10-09 19:00:00', 'Massage', '13', '3'),
('2020-10-10 8:00:00', 'Manicure', '7', '6'),
('2020-10-10 9:00:00', 'Wax', '7', '6'),
('2020-10-10 9:00:00', 'Makeup', '12', '5'),
('2020-10-10 10:00:00', 'Wax', '12', '6'),
('2020-10-10 10:00:00', 'Makeup', '15', '5'),
('2020-10-10 11:00:00', 'Wax', '10', '6'),
('2020-10-10 11:00:00', 'Manicure', '18', '5'),
('2020-10-10 12:00:00', 'Manicure', '10', '5'),
('2020-10-10 12:00:00', 'Massage', '18', '3'),
('2020-10-10 13:00:00', 'Manicure', '19', '3'),
('2020-10-10 14:00:00', 'Massage', '19', '3'),
('2020-10-10 15:00:00', 'Hair', '19', '3'),
('2020-10-10 16:00:00', 'Wax', '17', '6'),
('2020-10-10 16:00:00', 'Makeup', '19', '8'),
('2020-10-10 17:00:00', 'Massage', '20', '3'),
('2020-10-11 8:00:00', 'Hair', '9', '2'),
('2020-10-11 8:00:00', 'Hair', '27', '5'),
('2020-10-11 9:00:00', 'Wax', '24', '2'),
('2020-10-11 9:00:00', 'Hair', '11', '5'),
('2020-10-11 10:00:00', 'Hair', '24', '2'),
('2020-10-11 10:00:00', 'Hair', '14', '5'),
('2020-10-11 11:00:00', 'Hair', '12', '2'),
('2020-10-11 11:00:00', 'Manicure', '24', '5'),
('2020-10-11 12:00:00', 'Manicure', '17', '5'),
('2020-10-11 12:00:00', 'Wax', '25', '6'),
('2020-10-11 13:00:00', 'Hair', '17', '2'),
('2020-10-11 13:00:00', 'Hair', '25', '6'),
('2020-10-11 14:00:00', 'Hair', '26', '2'),
('2020-10-11 14:00:00', 'Makeup', '25', '6'),
('2020-10-11 15:00:00', 'Hair', '15', '2'),
('2020-10-11 15:00:00', 'Makeup', '26', '6'),
('2020-10-11 16:00:00', 'Hair', '18', '2'),
('2020-10-11 16:00:00', 'Manicure', '15', '8'),
('2020-10-11 17:00:00', 'Hair', '28', '8'),
('2020-10-11 17:00:00', 'Wax', '13', '6'),
('2020-10-11 18:00:00', 'Hair', '29', '8'),
('2020-10-11 18:00:00', 'Massage', '33', '6'),
('2020-10-11 19:00:00', 'Hair', '30', '8'),
('2020-10-11 19:00:00', 'Manicure', '33', '6'),
('2020-10-11 20:00:00', 'Hair', '32', '8'),
('2020-10-11 20:00:00', 'Wax', '33', '6'),
('2020-10-12 8:00:00', 'Hair', '7', '1'),
('2020-10-12 8:00:00', 'Manicure', '11', '4'),
('2020-10-12 9:00:00', 'Makeup', '7', '1'),
('2020-10-12 9:00:00', 'Hair', '39', '4'),
('2020-10-12 10:00:00', 'Hair', '34', '1'),
('2020-10-12 10:00:00', 'Hair', '41', '4'),
('2020-10-12 11:00:00', 'Hair', '35', '1'),
('2020-10-12 11:00:00', 'Manicure', '19', '4'),
('2020-10-12 12:00:00', 'Manicure', '17', '4'),
('2020-10-12 12:00:00', 'Manicure', '46', '3'),
('2020-10-12 13:00:00', 'Hair', '36', '1'),
('2020-10-12 13:00:00', 'Hair', '47', '3'),
('2020-10-12 14:00:00', 'Hair', '37', '1'),
('2020-10-12 14:00:00', 'Hair', '49', '3'),
('2020-10-12 15:00:00', 'makeup', '37', '1'),
('2020-10-12 15:00:00', 'Hair', '51', '3'),
('2020-10-12 16:00:00', 'Hair', '38', '1'),
('2020-10-12 16:00:00', 'Manicure', '12', '6'),
('2020-10-12 17:00:00', 'Hair', '39', '6'),
('2020-10-12 17:00:00', 'Massage', '7', '3'),
('2020-10-12 18:00:00', 'Wax', '14', '6'),
('2020-10-12 18:00:00', 'Massage', '7', '3'),
('2020-10-12 19:00:00', 'Wax', '7', '6'),
('2020-10-12 19:00:00', 'Massage', '14', '3'),
('2020-10-12 20:00:00', 'Wax', '14', '6'),
('2020-10-12 20:00:00', 'Massage', '9', '3'),
('2020-10-13 8:00:00', 'Hair', '10', '1'),
('2020-10-13 8:00:00', 'Hair', '40', '5'),
('2020-10-13 9:00:00', 'Makeup', '10', '1'),
('2020-10-13 9:00:00', 'Hair', '20', '5'),
('2020-10-13 10:00:00', 'Hair', '44', '1'),
('2020-10-13 10:00:00', 'Hair', '42', '5'),
('2020-10-13 11:00:00', 'Hair', '45', '1'),
('2020-10-13 12:00:00', 'Manicure', '47', '5'),
('2020-10-13 13:00:00', 'Hair', '49', '1'),
('2020-10-13 14:00:00', 'Hair', '50', '1'),
('2020-10-13 15:00:00', 'Hair', '52', '1'),
('2020-10-13 16:00:00', 'Manicure', '52', '1'),
('2020-10-13 17:00:00', 'Manicure', '20', '8'),
('2020-10-14 8:00:00', 'Makeup', '14', '1'),
('2020-10-14 8:00:00', 'Manicure', '13', '4'),
('2020-10-14 9:00:00', 'Hair', '13', '1'),
('2020-10-14 9:00:00', 'Manicure', '14', '4'),
('2020-10-14 10:00:00', 'Makeup', '13', '1'),
('2020-10-14 10:00:00', 'Manicure', '16', '4'),
('2020-10-14 11:00:00', 'Hair', '16', '1'),
('2020-10-14 12:00:00', 'Massage', '16', '4'),
('2020-10-14 13:00:00', 'Wax', '16', '6'),
('2020-10-14 14:00:00', 'Hair', '43', '1'),
('2020-10-14 16:00:00', 'Wax', '20', '6'),
('2020-10-15 9:00:00', 'Manicure', '18', '5'),
('2020-10-15 10:00:00', 'Hair', '18', '1'),
('2020-10-15 10:00:00', 'Makeup', '18', '1'),
('2020-10-16 9:00:00', 'Hair', '17', '1'),
('2020-10-16 10:00:00', 'Makeup', '17', '1'),
('2020-10-16 10:00:00', 'Manicure', '12', '4'),
('2020-10-16 12:00:00', 'Makeup', '20', '1'),
('2020-10-16 16:00:00', 'Hair', '19', '1'),
('2020-10-16 16:00:00', 'Massage', '17', '3'),
('2020-10-16 17:00:00', 'Massage', '10', '3'),
('2020-10-16 18:00:00', 'Massage', '11', '3'),
('2020-10-16 19:00:00', 'Massage', '13', '3'),
('2020-10-17 8:00:00', 'Manicure', '7', '6'),
('2020-10-17 9:00:00', 'Wax', '7', '6'),
('2020-10-17 9:00:00', 'Makeup', '12', '5'),
('2020-10-17 10:00:00', 'Wax', '12', '6'),
('2020-10-17 10:00:00', 'Makeup', '15', '5'),
('2020-10-17 11:00:00', 'Wax', '10', '6'),
('2020-10-17 11:00:00', 'Manicure', '18', '5'),
('2020-10-17 12:00:00', 'Manicure', '10', '5'),
('2020-10-17 12:00:00', 'Massage', '18', '3'),
('2020-10-17 13:00:00', 'Manicure', '19', '3'),
('2020-10-17 14:00:00', 'Massage', '19', '3'),
('2020-10-17 15:00:00', 'Hair', '19', '3'),
('2020-10-17 16:00:00', 'Wax', '17', '6'),
('2020-10-17 16:00:00', 'Makeup', '19', '8'),
('2020-10-17 17:00:00', 'Massage', '20', '3'),
('2020-10-18 8:00:00', 'Hair', '9', '2'),
('2020-10-18 8:00:00', 'Hair', '27', '5'),
('2020-10-18 9:00:00', 'Wax', '24', '2'),
('2020-10-18 9:00:00', 'Hair', '11', '5'),
('2020-10-18 10:00:00', 'Hair', '24', '2'),
('2020-10-18 10:00:00', 'Hair', '14', '5'),
('2020-10-18 11:00:00', 'Hair', '12', '2'),
('2020-10-18 11:00:00', 'Manicure', '24', '5'),
('2020-10-18 12:00:00', 'Manicure', '17', '5'),
('2020-10-18 12:00:00', 'Wax', '25', '6'),
('2020-10-18 13:00:00', 'Hair', '17', '2'),
('2020-10-18 13:00:00', 'Hair', '25', '6'),
('2020-10-18 14:00:00', 'Hair', '26', '2'),
('2020-10-18 14:00:00', 'Makeup', '25', '6'),
('2020-10-18 15:00:00', 'Hair', '15', '2'),
('2020-10-18 15:00:00', 'Makeup', '26', '6'),
('2020-10-18 16:00:00', 'Hair', '18', '2'),
('2020-10-18 16:00:00', 'Manicure', '15', '8'),
('2020-10-18 17:00:00', 'Hair', '28', '8'),
('2020-10-18 17:00:00', 'Wax', '13', '6'),
('2020-10-18 18:00:00', 'Hair', '29', '8'),
('2020-10-18 18:00:00', 'Massage', '33', '6'),
('2020-10-18 19:00:00', 'Hair', '30', '8'),
('2020-10-18 19:00:00', 'Manicure', '33', '6'),
('2020-10-18 20:00:00', 'Hair', '32', '8'),
('2020-10-18 20:00:00', 'Wax', '33', '6'),
('2020-10-19 8:00:00', 'Hair', '7', '1'),
('2020-10-19 8:00:00', 'Manicure', '11', '4'),
('2020-10-19 9:00:00', 'Makeup', '7', '1'),
('2020-10-19 9:00:00', 'Hair', '39', '4'),
('2020-10-19 10:00:00', 'Hair', '34', '1'),
('2020-10-19 10:00:00', 'Hair', '41', '4'),
('2020-10-19 11:00:00', 'Hair', '35', '1'),
('2020-10-19 11:00:00', 'Manicure', '19', '4'),
('2020-10-19 12:00:00', 'Manicure', '17', '4'),
('2020-10-19 12:00:00', 'Manicure', '46', '3'),
('2020-10-19 13:00:00', 'Hair', '36', '1'),
('2020-10-19 13:00:00', 'Hair', '47', '3'),
('2020-10-19 14:00:00', 'Hair', '37', '1'),
('2020-10-19 14:00:00', 'Hair', '49', '3'),
('2020-10-19 15:00:00', 'makeup', '37', '1'),
('2020-10-19 15:00:00', 'Hair', '51', '3'),
('2020-10-19 16:00:00', 'Hair', '38', '1'),
('2020-10-19 16:00:00', 'Manicure', '12', '6'),
('2020-10-19 17:00:00', 'Hair', '39', '6'),
('2020-10-19 17:00:00', 'Massage', '7', '3'),
('2020-10-19 18:00:00', 'Wax', '14', '6'),
('2020-10-19 18:00:00', 'Massage', '7', '3'),
('2020-10-19 19:00:00', 'Wax', '7', '6'),
('2020-10-19 19:00:00', 'Massage', '14', '3'),
('2020-10-19 20:00:00', 'Wax', '14', '6'),
('2020-10-19 20:00:00', 'Massage', '9', '3'),
('2020-10-20 8:00:00', 'Hair', '10', '1'),
('2020-10-20 8:00:00', 'Hair', '40', '5'),
('2020-10-20 9:00:00', 'Makeup', '10', '1'),
('2020-10-20 9:00:00', 'Hair', '20', '5'),
('2020-10-20 10:00:00', 'Hair', '44', '1'),
('2020-10-20 10:00:00', 'Hair', '42', '5'),
('2020-10-20 11:00:00', 'Hair', '45', '1'),
('2020-10-20 12:00:00', 'Manicure', '47', '5'),
('2020-10-20 13:00:00', 'Hair', '49', '1'),
('2020-10-20 14:00:00', 'Hair', '50', '1'),
('2020-10-20 15:00:00', 'Hair', '52', '1'),
('2020-10-20 16:00:00', 'Manicure', '52', '1'),
('2020-10-20 17:00:00', 'Manicure', '20', '8'),
('2020-10-21 8:00:00', 'Makeup', '14', '1'),
('2020-10-21 8:00:00', 'Manicure', '13', '4'),
('2020-10-21 9:00:00', 'Hair', '13', '1'),
('2020-10-21 9:00:00', 'Manicure', '14', '4'),
('2020-10-21 10:00:00', 'Makeup', '13', '1'),
('2020-10-21 10:00:00', 'Manicure', '16', '4'),
('2020-10-21 11:00:00', 'Hair', '16', '1'),
('2020-10-21 12:00:00', 'Massage', '16', '4'),
('2020-10-21 13:00:00', 'Wax', '16', '6'),
('2020-10-21 14:00:00', 'Hair', '43', '1'),
('2020-10-21 16:00:00', 'Wax', '20', '6'),
('2020-10-21 9:00:00', 'Manicure', '18', '5'),
('2020-10-21 10:00:00', 'Hair', '18', '1'),
('2020-10-21 10:00:00', 'Makeup', '18', '1'),
('2020-10-22 9:00:00', 'Hair', '17', '1'),
('2020-10-22 10:00:00', 'Makeup', '17', '1'),
('2020-10-22 10:00:00', 'Manicure', '12', '4'),
('2020-10-22 12:00:00', 'Makeup', '20', '1'),
('2020-10-22 16:00:00', 'Hair', '19', '1'),
('2020-10-22 16:00:00', 'Massage', '17', '3'),
('2020-10-22 17:00:00', 'Massage', '10', '3'),
('2020-10-22 18:00:00', 'Massage', '11', '3'),
('2020-10-22 19:00:00', 'Massage', '13', '3'),
('2020-10-23 8:00:00', 'Manicure', '7', '6'),
('2020-10-23 9:00:00', 'Wax', '7', '6'),
('2020-10-23 9:00:00', 'Makeup', '12', '5'),
('2020-10-23 10:00:00', 'Wax', '12', '6'),
('2020-10-23 10:00:00', 'Makeup', '15', '5'),
('2020-10-23 11:00:00', 'Wax', '10', '6'),
('2020-10-23 11:00:00', 'Manicure', '18', '5'),
('2020-10-23 12:00:00', 'Manicure', '10', '5'),
('2020-10-23 12:00:00', 'Massage', '18', '3'),
('2020-10-23 13:00:00', 'Manicure', '19', '3'),
('2020-10-23 14:00:00', 'Massage', '19', '3'),
('2020-10-23 15:00:00', 'Hair', '19', '3'),
('2020-10-23 16:00:00', 'Wax', '17', '6'),
('2020-10-23 16:00:00', 'Makeup', '19', '8'),
('2020-10-23 17:00:00', 'Massage', '20', '3');

\! echo "Finished inserting records!";

-- Show table schema --
\! echo "";
\! echo "╔══════════════════════════════════════╗";
\! echo "║                                      ║";
\! echo "║   Tables Schema & Raw data preview   ║";
\! echo "║                                      ║";
\! echo "╚══════════════════════════════════════╝";
\! echo "";
\! echo "╔══════ Tables list ════════════╗";
SHOW TABLES;
\! echo "";
\! echo "╔══════ Service table schema  ════════════════════════╗";
DESCRIBE Service;
\! echo "╔══════ Preview  ══╗";
SELECT * FROM Service LIMIT 5;
\! echo"";
\! echo "╔══════ Person table schema  ═════════════════════════════════════════════╗";
DESCRIBE Person;
\! echo "╔══════ Preview ══════════════════════════════════════════════════════════════════════╗";
SELECT * FROM Person LIMIT 5;
\! echo"";
\! echo "╔══════ Client table schema  ════════════════════╗";
DESCRIBE Client;
\! echo "╔Prev╗";
SELECT * FROM Client LIMIT 5;
\! echo"";
\! echo "╔══════ Employee table schema  ═══════════════════════════╗";
DESCRIBE Employee;
\! echo "╔══════ Preview ═════════════════════════════════╗";
SELECT * FROM Employee LIMIT 5;
\! echo"";
\! echo "╔══════ PreferredFor table schema  ═════════════════════════╗";
DESCRIBE PreferredFor;
\! echo "╔══════ Preview ═════════════════════════╗";
SELECT * FROM PreferredFor LIMIT 5;
\! echo"";
\! echo "╔══════ Appointment table schema  ══════════════════════════╗";
DESCRIBE Appointment;
\! echo "╔══════ Preview ═══════════════════════════════════════════════╗";
SELECT * FROM Appointment LIMIT 5;
\! echo"";

-- Printing views --
\! echo "";
\! echo "╔══════════════════════════════════════╗";
\! echo "║                                      ║";
\! echo "║       Printing Reports (VIEWs)       ║";
\! echo "║                                      ║";
\! echo "╚══════════════════════════════════════╝";
\! echo "";
\! echo "╔══════ Services ═══╗";
SELECT * FROM `Services List`;
\! echo "";
\! echo "╔══════ Employee List ════════════════════════════════════════════════════════════════════════════════════════════════════════╗";
SELECT * FROM `Employee List`;
\! echo "";
\! echo "╔══════ Client List ════════════════════════════════════════════════════════════════════════════╗";
SELECT * FROM `Client List`;
\! echo "";
\! echo "╔══════ Client Preferences ══════════════════════════════╗";
\! echo "+---------+----------------------------------------------+";
\! echo "|         |                   Service                    |";
SELECT * FROM `Client Preferences Matrix`;
\! echo "";
\! echo "╔══════ Client Appointments Today ══════════╗";
SELECT * FROM `Clients Scheduled Today`;
\! echo "";
\! echo "╔══════ Client Appointments By Day ═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗";
SELECT * FROM `Client Appointments This Week`;
\! echo "";
\! echo "╔══════ Employee Schedules By Day ════════════════════════════════════════════════════════════════════════════════════════════╗";
SELECT * FROM `Employee Schedules This Week`;
\! echo "";
\! echo "╔══════ Appointment Reminders ════════════════════════════════════════════════════════╗";
SELECT * FROM `Appointment Reminders`;
\! echo "";
\! echo "╔══════ Billing ════════════════════════╗";
SELECT * FROM `Billing` ORDER BY Name;

