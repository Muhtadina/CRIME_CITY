<?php
// home.php
session_start();
include 'backend/db.php';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
</head>
<body>
<?php include 'include/header.php'; ?>

<div class="page">
    <h1><b class="violet">STAY SAFE IN </b><b class="red">CRIME CITY</b></h1>
    <h2>REPORT AGAINST CRIME, SUPPRESSION & INJUSTICE</h2>
    <div class="home-report">
        <p>Fear shouldn’t silence you. In CRIME CITY, report crimes—anonymously or openly—from anywhere. Connect instantly to police, fire, ambulance, or legal aid. Your voice can stop criminals and save lives. Speak up now. Stay safe in CRIME CITY. 
<b>#BreakTheSilence</b></p>
        <a href="report.php" class="report">REPORT CRIME</a>
    </div>
    <p class="break">...</p>
</div>

<div id="aboutus">
<?php include 'include/abouts.php'; ?>
</div>

<div id="faq">
<?php include 'include/faq.php'; ?>
</div>

<div id="contact">
<?php include 'include/contact.php'; ?>
</div>

<?php include 'include/footer.php'; ?>
</body>
</html>