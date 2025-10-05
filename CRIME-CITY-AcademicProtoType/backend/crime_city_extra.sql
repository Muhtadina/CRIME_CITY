CREATE DATABASE crime_city_extra;
USE crime_city_extra;

START TRANSACTION;

-- ===========================
-- Emergency & Lawyer
-- ===========================

CREATE TABLE Emergency (
    Emergency_ID INT PRIMARY KEY,
    Emergency_Name VARCHAR(100),
    Emergency_Type VARCHAR(50),
    Contact_Number VARCHAR(20)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE Lawyer (
    Lawyer_ID INT PRIMARY KEY,
    Lawyer_Name VARCHAR(100),
    Lawyer_Type VARCHAR(50),
    Contact_Number VARCHAR(20),
    Email_Address VARCHAR(100),
    Portfolio_Link VARCHAR(255)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------
-- Insert test data
INSERT INTO Citizen_User (NID, User_ID, Gender, Past_Address, Occupation, Marital_Status, Father_Name, Father_NID, Mother_Name, Mother_NID, Account_Verification, DOB, Blood_Group, NID_Image)
VALUES ('NID001', NULL, 'Male', '123 Main St', 'Engineer', 'Single', 'John Doe', 'NID002', 'Jane Doe', 'NID003', 'Pending', '1990-05-15', 'A+', NULL);

INSERT INTO Complainant (NID, Complain_Count, Visibility)
VALUES ('NID001', 1, 'Private');

INSERT INTO Contact_Us (NID, First_Name, Last_Name, Work_Email, Site_Message)
VALUES ('NID001', 'John', 'Doe', 'john.doe@email.com', 'Need assistance with a report');

INSERT INTO Witness (Report_ID, Existence, NID, First_Name, Last_Name, Gender, Age, Description)
VALUES (NULL, 'Yes', 'NID001', 'John', 'Doe', 'Male', 35, 'Witnessed the incident');

INSERT INTO CrimeCategory (Crime_Type)
VALUES ('Theft');

INSERT INTO Crime (Category_ID, Occurence_Address, District, Division, Postal_Code, Crime_Description, Occurrence_Time, Complainant_ID, Submission_Status, Case_Progress)
VALUES (1, '456 Oak St', 'Downtown', 'Central', '10001', 'Theft of a laptop', '2025-08-21 14:30:00', 1, 'Submitted', 'Investigation Ongoing');

INSERT INTO Victim (Report_ID, Acquaintance, NID, First_Name, Last_Name, Gender, Age, Attire)
VALUES (1, 'No', 'NID004', 'Alice', 'Smith', 'Female', 28, 'Blue jacket');

INSERT INTO Suspect (Report_ID, Acquaintance, Enlistment, NID, First_Name, Last_Name, Gender, Age, Attire)
VALUES (1, 'No', 'No', 'NID005', 'Bob', 'Johnson', 'Male', 40, 'Black hoodie');

INSERT INTO Evidence (Report_ID, Images, Audio, Video)
VALUES (1, NULL, NULL, NULL);

INSERT INTO Criminal_Record (NID, Criminal_Type, First_Name, Last_Name, Gender, DOH, Father_Name, Mother_Name, Permanent_Address, Crime_Count, Comments, Criminal_Record, Blood_Group, Criminal_Status, Criminal_Image)
VALUES ('NID005', 'Theft', 'Bob', 'Johnson', 'Male', '2025-08-20', 'Mike Johnson', 'Sarah Johnson', '789 Pine St', 1, 'Suspected thief', 'Record details', 'B+', 'Wanted', NULL);

INSERT INTO Case_File (Report_ID, Court_Status)
VALUES (1, 'Pending');

INSERT INTO Administration (Admin_Name, Cell_Number, Email_Address, Admin_Password)
VALUES ('Admin1', '01712345678', 'admin1@email.com', 'adminpass1');

INSERT INTO CyberPolBD (First_Name, Last_Name, Police_ID, POL_Password, Cell_Number, Email_Address, Designation, Cyber_Division, Sex, DOB, Blood_Group)
VALUES ('Officer', 'Khan', 'POL001', 'polpass1', '01787654321', 'officer.khan@email.com', 'Inspector', 'CyberCrime', 'Male', '1985-03-10', 'O+');

INSERT INTO Emergency (Emergency_Name, Emergency_Type, Contact_Number)
VALUES ('Fire Dept', 'Fire', '01799998888');

INSERT INTO Lawyer (Lawyer_Name, Lawyer_Type, Contact_Number, Email_Address, Portfolio_Link)
VALUES ('Emma Wilson', 'Defense', '01755554444', 'emma.wilson@email.com', 'http://emma-portfolio.com');

INSERT INTO Citizen_Register (First_Name, Last_Name, Email_Address, Cell_Number, Present_Address, Citizen_Password)
VALUES ('John', 'Doe', 'john.doe@email.com', '01712345678', '123 Main St', 'citizenpass1');

INSERT INTO BD_Citizen_Record (BD_NID, NID_Image, CyberPol_ID)
VALUES ('NID001', NULL, 1);

-- Update test data
UPDATE Citizen_User
SET Gender = 'Female', Past_Address = '124 Main St'
WHERE NID = 'NID001';

UPDATE Complainant
SET Complain_Count = 2
WHERE Complainant_ID = 1;

UPDATE Crime
SET Case_Progress = 'Solved'
WHERE Report_ID = 1;

-- Delete test data
DELETE FROM Witness
WHERE Witness_ID = 1;

DELETE FROM Victim
WHERE Victim_ID = 1;

DELETE FROM Suspect
WHERE Suspect_ID = 1;