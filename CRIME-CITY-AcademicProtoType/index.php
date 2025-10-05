<?php 
//index.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="container">
    <div class="left-panel">
        <h1>CRIME CITY</h1>
        <p>Fear shouldn’t silence you. 
            With CRIME CITY, report crimes—anonymously or openly—from anywhere. 
            Connect instantly to police, fire, ambulance, or legal aid. 
            Your voice can stop criminals and save lives. Speak up now. Stay safe. 
            <b>#BreakTheSilence</b>
        </p>
    </div>
    <div class="right-panel">
        <h2>STAND UP against CRIME</h2>
        <a href="register.php" class="btn-signup">REGISTER</a></br>
        <a href="citizen_login.php" class="btn-login"> LOGIN </a></br>
        <a href="cyberpol.php" class="btn-prime">CYBERPOL BD</a>
        <a href="admin_login.php" class="btn-prime">ADMINISTRATION</a>
    </div>
</div>
<?php
include 'include/fixedfoot.php';
?>
</body>
</html>