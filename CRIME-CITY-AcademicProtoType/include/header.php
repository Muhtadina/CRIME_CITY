<?php
// header.php
session_start();
include 'backend/db.php';

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout' && isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$conn->close();
?>

<header>
    <nav class="navbar">
        <div class="logo"><b class="red">CRIME</b><br><b class="space">CITY</b></div>
        <ul class="navbutton">
            <li><a href="home.php" class="nbuttons">HOME</a></li>
            <li><a href="solve_report.php" class="nbuttons">CASE FILES</a></li>
            <li><a href="emergency_list.php" class="nbuttons">EMERGENCY<span class="material-symbols-outlined">arrow_drop_down</span></a>
                <ul class="menu">
                    <li><a href="emergency_list.php?type=Police & Law" class="menu-btn">POLICE & LAW</a></li>
                    <li><a href="emergency_list.php?type=Medical & Health" class="menu-btn">MEDICAL & HEALTH</a></li>
                    <li><a href="emergency_list.php?type=Women & Children" class="menu-btn">WOMEN & CHILDREN</a></li>
                    <li><a href="emergency_list.php?type=Anti-corruption" class="menu-btn">ANTI-CORRUPTION</a></li>
                    <li><a href="emergency_list.php?type=Cyber Crime" class="menu-btn">CYBER CRIME</a></li>
                    <li><a href="emergency_list.php?type=Disaster & Relief" class="menu-btn">DISASTER & RELIEF</a></li>
                    <li><a href="emergency_list.php?type=Utility" class="menu-btn">UTILITY</a></li>
                </ul>
            </li>
            <li><a href="lawyer.php" class="nbuttons">LAWYER</a></li>
            <li><a href="criminal.php" class="nbuttons">RECORDS</a></li>
            <li><a href="cyberpol_list.php" class="nbuttons">CYBERPOL BD</a></li>
            <li><a href="profile.php" class="navprofile"><span class="material-symbols-outlined" style="font-size: 30px;">account_circle</span></a>
                <ul class="menu">
                    <li><a href="profile.php" class="menu-btn">PROFILE</a></li>
                    <li><a href="citizen_report_history.php" class="menu-btn">REPORT HISTORY</a></li>
                    <li><a href="password_edit.php" class="menu-btn">CHANGE PASSWORD</a></li>
                    <li><a href="profile_edit.php" class="menu-btn">SETTINGS</a></li>
                    <li><a href="?action=logout" class="menu-btn">LOG OUT</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>