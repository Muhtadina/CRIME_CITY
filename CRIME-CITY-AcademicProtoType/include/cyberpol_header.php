<?php
// cyberpol_header.php
session_start();
include 'backend/db.php';

// Handle logout for CyberPol
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    if (isset($_SESSION['cyberpol_id'])) {
        $cyberpol_id = $_SESSION['cyberpol_id'];
        $sql = "DELETE FROM Principle_Login WHERE CyberPol_ID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $cyberpol_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    session_destroy();
    header("Location: index.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo"><b class="red">CRIME</b><br><b class="space">CITY</b></div>
        <ul class="navbutton">
            <li><a href="cyberpol_dashboard.php" class="nbuttons">DASHBOARD</a></li>
            <li><a href="solve_report.php" class="nbuttons">CASE FILES</a></li>
            
            <li><a href="criminal.php" class="nbuttons">RECORDS</a></li>
            <li><a href="cyberpol_list.php" class="nbuttons">CYBERPOL BD</a></li>
            <li><a href="?action=logout" class="navprofile"><span class="material-symbols-outlined" style="font-size: 30px;">account_circle</span></a>
                <ul class="menu">
                    <li><a href="?action=logout" class="menu-btn">LOG OUT</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
</body>
</html>