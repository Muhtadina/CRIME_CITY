<?php 
//pol_header.php only for police
?>

<!--<span class="material-symbols-outlined">arrow_drop_up</span>-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    
    <!--Google Icons-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">

</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo"><b class="red">CRIME</b></br><b class="space">CITY</b></div>
            <ul class="navbutton">
                <li><a href="cyberpol_dashboard.php" class="nbuttons">DASHBOARD</a></li>
                <li><a href="solved_report.php" class="nbuttons">CASE FILES</a></li>
                <li><a href="emergency_list.php" class="nbuttons">EMERGENCY<span class="material-symbols-outlined">arrow_drop_down</span></a>
                    <ul class="menu">
                        <li><a href="#" class="menu-btn">POLICE & LAW</a></li>
                        <li><a href="#" class="menu-btn">MEDICAL & HEALTH</a></li>
                        <li><a href="#" class="menu-btn">WOMEN & CHILDREN</a></li>
                        <li><a href="#" class="menu-btn">ANTI-CORRUPTION</a></li>
                        <li><a href="#" class="menu-btn">CYBER CRIME</a></li>
                        <li><a href="#" class="menu-btn">DISASTER & RELIEF</a></li>
                        <li><a href="#" class="menu-btn">UTILITY</a></li>
                    </ul>
                </li>
                    
                <li><a href="lawyer.php" class="nbuttons">LAWYER</a></li>
                <li><a href="criminal.php" class="nbuttons">RECORDS</a></li>
                <li><a href="cyberpol_list.php" class="nbuttons">CYBERPOL BD</a></li>
                <li><a href="profile.php" class="navprofile"><span class="material-symbols-outlined" style="font-size: 30px;">account_circle</span></a>
                    <ul class="menu">
                        <li><a href="profile.php" class="menu-btn">PROFILE</a></li>
                        <li><a href="#" class="menu-btn">CHANGE PASSWORD</a></li>
                        <li><a href="index.php" class="menu-btn">LOG OUT</a></li>
                    </ul>
                </li>
            </ul>
    </nav>
</header>
    
</body>
</html>