CREATE DATABASE crime_city;
USE crime_city;

START TRANSACTION;
SET time_zone = "+00:00";

-- ===========================
-- Citizen Register 
-- ===========================
CREATE TABLE Citizen_Register (
    User_ID INT AUTO_INCREMENT PRIMARY KEY,
    First_Name VARCHAR(100),
    Last_Name VARCHAR(100),
    Email_Address VARCHAR(100) UNIQUE,
    Cell_Number CHAR(11) UNIQUE,
    Present_Address VARCHAR(255),
    Citizen_Password VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================
-- Citizen Login
-- ===========================
CREATE TABLE Citizen_Login (
    Login_ID INT AUTO_INCREMENT PRIMARY KEY,
    NID BIGINT,
    User_ID INT NOT NULL,
    Email_Address VARCHAR(100) NOT NULL,
    Login_Pass VARCHAR(100) NOT NULL,
    FOREIGN KEY (User_ID) REFERENCES Citizen_Register(User_ID) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- Citizen User (Profile + NID Info)
-- =========================================
CREATE TABLE Citizen_User (
    NID BIGINT UNIQUE,
    User_ID INT NOT NULL,
    Login_ID INT NOT NULL,
    Complainant_ID INT DEFAULT NULL,
    Gender VARCHAR(10) DEFAULT NULL,
    Past_Address VARCHAR(255) DEFAULT NULL,
    Occupation VARCHAR(100) DEFAULT NULL,
    Marital_Status VARCHAR(20) DEFAULT NULL,
    Father_Name VARCHAR(100) DEFAULT NULL,
    Father_NID BIGINT DEFAULT NULL,
    Mother_Name VARCHAR(100) DEFAULT NULL,
    Mother_NID BIGINT DEFAULT NULL,
    Account_Verification VARCHAR(10) DEFAULT 'Pending',
    DOB DATE,
    Blood_Group VARCHAR(5) DEFAULT NULL,
    NID_Image BLOB,
    PRIMARY KEY (NID),
    FOREIGN KEY (User_ID) REFERENCES Citizen_Register(User_ID) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Login_ID) REFERENCES Citizen_Login(Login_ID) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE Complainant (
    Complainant_ID INT AUTO_INCREMENT PRIMARY KEY,
    NID BIGINT,
    Complain_Count INT DEFAULT 0, 
    Visibility VARCHAR(15) DEFAULT 'Anonymous',
    FOREIGN KEY (NID) REFERENCES Citizen_User(NID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================
-- Contact Us
-- ===========================
CREATE TABLE Contact_Us (
    Contact_ID INT AUTO_INCREMENT PRIMARY KEY,
    NID BIGINT,
    First_Name VARCHAR(100),
    Last_Name VARCHAR(100),
    Work_Email VARCHAR(100),
    Site_Message TEXT,
    FOREIGN KEY (NID) REFERENCES Citizen_User(NID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================
-- Witness
-- ===========================
CREATE TABLE Witness (
    Witness_ID INT AUTO_INCREMENT PRIMARY KEY,
    Report_ID INT,
    Existence VARCHAR(3) DEFAULT 'No',
    NID BIGINT,
    First_Name VARCHAR(100),
    Last_Name VARCHAR(100),
    Gender VARCHAR(10),
    Age INT,
    Cell_Number CHAR(11),
    Attire TEXT,
    FOREIGN KEY (NID) REFERENCES Citizen_User(NID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================
-- Crime and Categories
-- ===========================
CREATE TABLE CrimeCategory (
    Category_ID INT AUTO_INCREMENT PRIMARY KEY,
    Crime_Type VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE Crime (
    Report_ID INT AUTO_INCREMENT PRIMARY KEY,
    Crime_Type VARCHAR(100),
    Occurence_Address VARCHAR(255),
    District VARCHAR(100),
    Division VARCHAR(100),
    Postal_Code VARCHAR(20),
    Crime_Description TEXT,
    Victim_Count INT DEFAULT 0,
    Suspect_Count INT DEFAULT 0,
    Witness_Count INT DEFAULT 0,
    Occurrence_Time DATETIME,
    Complainant_ID INT,
    CyberPol_ID VARCHAR(13) DEFAULT NULL, 
    Submission_Status VARCHAR(15) DEFAULT 'Draft',
    Case_Progress VARCHAR(30) DEFAULT 'Pending Request',
    FOREIGN KEY (Complainant_ID) REFERENCES Complainant(Complainant_ID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================
-- Victim and Suspect
-- ===========================
CREATE TABLE Victim (
    Victim_ID INT AUTO_INCREMENT PRIMARY KEY,
    Report_ID INT,
    Acquaintance VARCHAR(3) DEFAULT 'No',
    NID BIGINT,
    First_Name VARCHAR(100),
    Last_Name VARCHAR(100),
    Gender VARCHAR(10),
    Age INT,
    Cell_Number CHAR(11),
    Attire TEXT,
    FOREIGN KEY (Report_ID) REFERENCES Crime(Report_ID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE Criminal_Record (
    Criminal_ID INT AUTO_INCREMENT PRIMARY KEY,
    Criminal_Display_ID VARCHAR(10) UNIQUE,
    NID BIGINT,
    Criminal_Type VARCHAR(100),
    First_Name VARCHAR(100),
    Last_Name VARCHAR(100),
    Gender VARCHAR(10),
    DOH DATE,
    Father_Name VARCHAR(100),
    Mother_Name VARCHAR(100),
    Permanent_Address VARCHAR(255),
    Crime_Count INT DEFAULT 0,
    Comments TEXT,
    Criminal_Record TEXT,
    Blood_Group VARCHAR(5),
    Criminal_Status VARCHAR(15) DEFAULT 'Wanted',
    Criminal_Image BLOB
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE Suspect (
    Suspect_ID INT AUTO_INCREMENT PRIMARY KEY,
    Report_ID INT,
    Acquaintance VARCHAR(3) DEFAULT 'No',
    Enlistment VARCHAR(3) DEFAULT 'No',
    Criminal_Display_ID VARCHAR(10), 
    NID BIGINT,
    First_Name VARCHAR(100),
    Last_Name VARCHAR(100),
    Gender VARCHAR(10),
    Age INT,
    Cell_Number CHAR(11),
    Attire TEXT,
    FOREIGN KEY (Report_ID) REFERENCES Crime(Report_ID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Criminal_Display_ID) REFERENCES Criminal_Record(Criminal_Display_ID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================
-- Evidence
-- ===========================
CREATE TABLE Evidence (
    Evidence_No INT AUTO_INCREMENT PRIMARY KEY,
    Report_ID INT,
    Images BLOB,
    Audio BLOB,
    Video BLOB,
    Link NVARCHAR(2083),
    FOREIGN KEY (Report_ID) REFERENCES Crime(Report_ID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================
-- Records & Case Files
-- ===========================
CREATE TABLE Case_File (
    Case_ID INT AUTO_INCREMENT PRIMARY KEY,
    Report_ID INT,
    Court_Status VARCHAR(15) DEFAULT 'Pending',
    FOREIGN KEY (Report_ID) REFERENCES Crime(Report_ID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================
-- Admin & Cyberpolice
-- ===========================
CREATE TABLE Administration (
    Admin_ID VARCHAR(8) PRIMARY KEY,
    Admin_Name VARCHAR(100),
    Cell_Number CHAR(11),
    Email_Address VARCHAR(100),
    Admin_Password VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE CyberPolBD (
    CyberPol_ID VARCHAR(13) PRIMARY KEY,
    First_Name VARCHAR(100),
    Last_Name VARCHAR(100),
    Police_ID VARCHAR(50),
    POL_Password VARCHAR(100),
    Cell_Number CHAR(11),
    Email_Address VARCHAR(100),
    Designation VARCHAR(100),
    Cyber_Division VARCHAR(100),
    Sex VARCHAR(8),
    DOB DATE,
    Blood_Group VARCHAR(5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE Principle_Login (
    Login_ID INT AUTO_INCREMENT PRIMARY KEY,
    CyberPol_ID VARCHAR(13) NOT NULL,
    Cell_Number CHAR(11) NOT NULL,
    Login_Pass VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO CyberPolBD (CyberPol_ID, First_Name, Last_Name, Police_ID, POL_Password, Cell_Number, Email_Address, Designation, Cyber_Division, Sex, DOB, Blood_Group) VALUES
-- Dhaka
('BDXXINS456D', 'Ayesha', 'Rahman', 'POL123456', 'hash123pass', '01712345678', 'ayesha.rahman@cyberpol.bd', 'Inspector', 'Dhaka', 'Female', '1988-05-15', 'A+'),
('BDXYASI789D', 'Kamal', 'Hossain', 'POL456789', 'hash456pass', '01923456789', 'kamal.hossain@cyberpol.bd', 'Assistant Superintendent', 'Dhaka', 'Male', '1985-11-22', 'B+'),
-- Chittagong
('BDXYCON123C', 'Mohammad', 'Alam', 'POL789123', 'hash789pass', '01634567890', 'mohammad.alam@cyberpol.bd', 'Constable', 'Chittagong', 'Male', '1992-03-10', 'O+'),
('BDXXSUB234C', 'Fatima', 'Begum', 'POL234234', 'hash234pass', '01845678901', 'fatima.begum@cyberpol.bd', 'Sub-Inspector', 'Chittagong', 'Female', '1990-07-30', 'AB+'),
-- Barishal
('BDXXSER567B', 'Rina', 'Akter', 'POL345567', 'hash567pass', '01556789012', 'rina.akter@cyberpol.bd', 'Sergeant', 'Barishal', 'Female', '1995-01-25', 'A-'),
('BDXYINS890B', 'Rahim', 'Sheikh', 'POL678890', 'hash890pass', '01767890123', 'rahim.sheikh@cyberpol.bd', 'Inspector', 'Barishal', 'Male', '1987-09-12', 'B-'),
-- Rajshahi
('BDXYCON901R', 'Asif', 'Iqbal', 'POL123901', 'hash901pass', '01978901234', 'asif.iqbal@cyberpol.bd', 'Constable', 'Rajshahi', 'Male', '1993-04-18', 'O-'),
('BDXXASI678R', 'Sadia', 'Khan', 'POL456678', 'hash678pass', '01689012345', 'sadia.khan@cyberpol.bd', 'Assistant Superintendent', 'Rajshahi', 'Female', '1989-06-05', 'A+'),
-- Sylhet
('BDXXSUB345S', 'Laila', 'Haque', 'POL789345', 'hash345pass', '01890123456', 'laila.haque@cyberpol.bd', 'Sub-Inspector', 'Sylhet', 'Female', '1991-02-14', 'B+'),
('BDXYSER012S', 'Zubair', 'Ahmed', 'POL234012', 'hash012pass', '01701234567', 'zubair.ahmed@cyberpol.bd', 'Sergeant', 'Sylhet', 'Male', '1986-08-20', 'AB-'),
-- Khulna
('BDXYINS235K', 'Arif', 'Mollah', 'POL567235', 'hash235pass', '01912345678', 'arif.mollah@cyberpol.bd', 'Inspector', 'Khulna', 'Male', '1988-12-01', 'O+'),
('BDXXCON568K', 'Nusrat', 'Jahan', 'POL890568', 'hash568pass', '01523456789', 'nusrat.jahan@cyberpol.bd', 'Constable', 'Khulna', 'Female', '1994-05-28', 'A-'),
-- Rangpur
('BDXXASI679R', 'Shabnam', 'Parvin', 'POL123679', 'hash679pass', '01634567890', 'shabnam.parvin@cyberpol.bd', 'Assistant Superintendent', 'Rangpur', 'Female', '1987-03-15', 'B-'),
('BDXYSUB902R', 'Imran', 'Hossain', 'POL456902', 'hash902pass', '01845678901', 'imran.hossain@cyberpol.bd', 'Sub-Inspector', 'Rangpur', 'Male', '1990-10-10', 'O-'),
-- Mymensingh
('BDXYCON013M', 'Sajid', 'Rahman', 'POL789013', 'hash013pass', '01756789012', 'sajid.rahman@cyberpol.bd', 'Constable', 'Mymensingh', 'Male', '1993-07-07', 'A+'),
('BDXXINS236M', 'Tania', 'Sultana', 'POL234236', 'hash236pass', '01967890123', 'tania.sultana@cyberpol.bd', 'Inspector', 'Mymensingh', 'Female', '1989-11-19', 'AB+');

-- ===========================
-- BD Citizen Record (Verification)
-- ===========================
CREATE TABLE BD_Citizen_Record (
    BD_NID BIGINT PRIMARY KEY,
    NID_Image BLOB,
    CyberPol_ID VARCHAR(13),
    FOREIGN KEY (CyberPol_ID) REFERENCES CyberPolBD(CyberPol_ID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================
-- Division & District Reference
-- ===========================
CREATE TABLE Crime_City_Division (
    division VARCHAR(50),
    district VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO Crime_City_Division (division, district) VALUES
('Dhaka', 'Dhaka'),
('Dhaka', 'Faridpur'),
('Dhaka', 'Gazipur'),
('Dhaka', 'Gopalganj'),
('Dhaka', 'Kishoreganj'),
('Dhaka', 'Madaripur'),
('Dhaka', 'Manikganj'),
('Dhaka', 'Munshiganj'),
('Dhaka', 'Narayanganj'),
('Dhaka', 'Narsingdi'),
('Dhaka', 'Rajbari'),
('Dhaka', 'Shariatpur'),
('Dhaka', 'Tangail'),
('Chittagong', 'Bandarban'),
('Chittagong', 'Brahmanbaria'),
('Chittagong', 'Chandpur'),
('Chittagong', 'Chittagong'),
('Chittagong', 'Comilla'),
('Chittagong', 'Cox’s Bazar'),
('Chittagong', 'Feni'),
('Chittagong', 'Khagrachhari'),
('Chittagong', 'Lakshmipur'),
('Chittagong', 'Noakhali'),
('Chittagong', 'Rangamati'),
('Barishal', 'Barguna'),
('Barishal', 'Barishal'),
('Barishal', 'Bhola'),
('Barishal', 'Jhalokati'),
('Barishal', 'Patuakhali'),
('Barishal', 'Pirojpur'),
('Rajshahi', 'Bogura'),
('Rajshahi', 'Chapainawabganj'),
('Rajshahi', 'Joypurhat'),
('Rajshahi', 'Naogaon'),
('Rajshahi', 'Natore'),
('Rajshahi', 'Pabna'),
('Rajshahi', 'Rajshahi'),
('Rajshahi', 'Sirajganj'),
('Sylhet', 'Habiganj'),
('Sylhet', 'Moulvibazar'),
('Sylhet', 'Sunamganj'),
('Sylhet', 'Sylhet'),
('Khulna', 'Bagerhat'),
('Khulna', 'Chuadanga'),
('Khulna', 'Jashore'),
('Khulna', 'Jhenaidah'),
('Khulna', 'Khulna'),
('Khulna', 'Kushtia'),
('Khulna', 'Magura'),
('Khulna', 'Meherpur'),
('Khulna', 'Narail'),
('Khulna', 'Satkhira'),
('Rangpur', 'Dinajpur'),
('Rangpur', 'Gaibandha'),
('Rangpur', 'Kurigram'),
('Rangpur', 'Lalmonirhat'),
('Rangpur', 'Nilphamari'),
('Rangpur', 'Panchagarh'),
('Rangpur', 'Rangpur'),
('Rangpur', 'Thakurgaon'),
('Mymensingh', 'Jamalpur'),
('Mymensingh', 'Mymensingh'),
('Mymensingh', 'Netrokona'),
('Mymensingh', 'Sherpur');

-- ===========================
-- Emergency & Lawyer
-- ===========================
CREATE TABLE Emergency (
    Emergency_ID INT PRIMARY KEY,
    Emergency_Name VARCHAR(100),
    Emergency_Type VARCHAR(50),
    Contact_Number VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO Emergency (Emergency_ID, Emergency_Name, Emergency_Type, Contact_Number) VALUES
(1, 'Bangladesh Police Emergency', 'Police & Law', '999'),
(2, 'Dhaka Medical College Hospital', 'Medical & Health', '1066'),
(3, 'National Helpline Centre for Women and Children', 'Women & Children', '109'),
(4, 'Anti-Corruption Commission Helpline', 'Anti-corruption', '09666996699'),
(5, 'Cyber Crime Investigation Department', 'Cyber Crime', '+880-2-8931195'),
(6, 'Directorate of Disaster Management', 'Disaster & Relief', '1090'),
(7, 'Dhaka WASA Emergency', 'Utility', '16166');

CREATE TABLE Lawyer (
    Lawyer_ID INT PRIMARY KEY,
    Lawyer_Name VARCHAR(100),
    Lawyer_Type VARCHAR(50),
    Contact_Number VARCHAR(20),
    Email_Address VARCHAR(100),
    Portfolio_Link VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO Lawyer (Lawyer_ID, Lawyer_Name, Lawyer_Type, Contact_Number, Email_Address, Portfolio_Link) VALUES
(1, 'Barrister Mahbubey Alam', 'Criminal Law', '+880-1711-123456', 'mahbubey.alam@lawbd.com', 'https://portfolio.mahbubeyalam.com'),
(2, 'Sara Hossain', 'Family Law', '+880-1711-234567', 'sara.hossain@legalbd.com', 'https://portfolio.sarahossain.com'),
(3, 'Syeda Rizwana Hasan', 'Environmental & Cyber Crime', '+880-1711-345678', 'rizwana.hasan@lawbd.com', 'https://portfolio.rizwanahasan.com'),
(4, 'Barrister Al Amin Rahman', 'Corporate & Anti-corruption', '+880-1711-456789', 'alamin.rahman@fmassociates.com', 'https://portfolio.alaminrahman.com'),
(5, 'Tania Amir', 'Criminal Law', '+880-1711-567890', 'tania.amir@lawassociates.com', 'https://portfolio.taniaamir.com'),
(6, 'Rashna Imam', 'Family Law', '+880-1711-678901', 'rashna.imam@legalbd.com', 'https://portfolio.rashnaimam.com'),
(7, 'Nihad Kabir', 'Corporate Law', '+880-1711-789012', 'nihad.kabir@syedlaw.com', 'https://portfolio.nihadkabir.com');


ALTER TABLE Citizen_Register AUTO_INCREMENT = 1;
ALTER TABLE Citizen_Login AUTO_INCREMENT = 1;
ALTER TABLE Complainant AUTO_INCREMENT = 1;

COMMIT;
