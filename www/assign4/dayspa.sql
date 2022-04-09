Create TABLE Service(
   Name VARCHAR(20) PRIMARY KEY,
   Price         DECIMAL(5,2) NOT NULL
);
CREATE TABLE Person(
   ID INT AUTO_INCREMENT PRIMARY KEY,
   Name VARCHAR(255) NOT NULL,
  `Phone Number` VARCHAR(25), -- Phone numbers are normally in format "(000) 000-0000" but might be as long as "+000 00 0000 0000, 000000"
  `Physical Address` VARCHAR(255),
  `Email` VARCHAR(255)
);
CREATE TABLE Client(
   ID INT PRIMARY KEY,
   FOREIGN KEY (ID) REFERENCES Person(ID)
);
CREATE TABLE Employee(
   ID INT PRIMARY KEY,
  `Job Title` VARCHAR(255) NOT NULL,
  `Pay Rate` DECIMAL(5,2) NOT NULL,
   Specialty VARCHAR(20) NOT NULL,
   FOREIGN KEY (ID) REFERENCES Person(ID),
   FOREIGN KEY (Specialty) REFERENCES Service(Name)
);
Create TABLE PreferredFor(
  `Client ID` INT NOT NULL,
  `Employee ID` INT NOT NULL,
  `Service Name` VARCHAR(20) NOT NULL,
  Primary KEY (`Client ID`, `Employee ID`, `Service Name`),
  FOREIGN KEY (`Client ID`) REFERENCES Client(ID),
  FOREIGN KEY (`Employee ID`) REFERENCES Employee(ID),
  FOREIGN KEY (`Service Name`) REFERENCES Service(Name)
);
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