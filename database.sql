SET FOREIGN_KEY_CHECKS=0;
DROP TABLE Theater;
DROP TABLE Ticket;
DROP TABLE Movie;
DROP TABLE EMPLOYEE;
DROP TABLE Customer;
DROP TABLE ARRANGE;
CREATE TABLE Theater 
( 
Location char(255) PRIMARY KEY,
Name char(255),
OpenTime int,
CloseTime int
);

CREATE TABLE Ticket
( 
ConfirmationNo int PRIMARY KEY AUTO_INCREMENT,
SeatsNo int,
AuditoriumNo int,
CEmail char(255),
ArrangeId int,
CONSTRAINT FK_ArrangeId FOREIGN KEY (ArrangeId) references Arrange(ArrangeId),
CONSTRAINT FK_CEmail FOREIGN KEY (CEmail) references Customer(CEmail)
);

CREATE TABLE Movie
(
Name char(255) PRIMARY KEY,
Type char(255),
Duration int,
StartingDate date,
Price int
);

CREATE TABLE Employee
(
ID int PRIMARY KEY AUTO_INCREMENT,
Name char(255),
EEmail char(255),
EPassword char(255)
);

CREATE TABLE Customer
(
CEmail char(255) PRIMARY KEY,
Age int,
CPassword char(255)
);

CREATE TABLE Arrange
(
ArrangeId int PRIMARY KEY AUTO_INCREMENT,
Showtime datetime,
Location char(255),
Name char(255),
SeatsLeft int,
CONSTRAINT FK_Location FOREIGN KEY (Location) REFERENCES Theater(Location),
CONSTRAINT FK_Name FOREIGN KEY (Name) REFERENCES Movie(Name)
);

SET FOREIGN_KEY_CHECKS=1;