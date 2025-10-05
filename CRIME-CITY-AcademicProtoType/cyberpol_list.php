<?php
// cyberpol_list.php
// Include database connection
require 'backend/db.php';

// Fetch CyberPol data
$sql = "SELECT CyberPol_ID, First_Name, Last_Name, Police_ID, Email_Address, Cell_Number, 
               Designation, Cyber_Division, Sex, DOB, Blood_Group 
        FROM CyberPolBD";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Police List | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="x-icon">
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:wght@300;400;600;700&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<?php include 'include/header.php'; ?>

<div class="page">
    <h1><b class="violet">CYBER</b><b class="red"> POLICE</b></h1>
    <h2>Bangladesh Cyber Police Division Officers</h2>
    
    <div class="table_for_sql_display">
        <table class="emergency-table">
            <thead>
                <tr>
                    <th>Officer Name</th>
                    <th>Police ID</th>
                    <th>Email Address</th>
                    <th>Contact Number</th>
                    <th>Designation</th>
                    <th>Cyber Division</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Blood Group</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><b>" . htmlspecialchars($row['First_Name'] . " " . $row['Last_Name']) . "</b></td>";
                        echo "<td><b class='violet'>" . htmlspecialchars($row['Police_ID']) . "</b></td>";
                        echo "<td>" . htmlspecialchars($row['Email_Address']) . "</td>";
                        echo "<td><b class='green'>" . htmlspecialchars($row['Cell_Number']) . "</b></td>";
                        echo "<td>" . htmlspecialchars($row['Designation']) . "</td>";
                        echo "<td><b class='red'>" . htmlspecialchars($row['Cyber_Division']) . "</b></td>";
                        echo "<td>" . htmlspecialchars($row['Sex']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['DOB']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Blood_Group']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No Cyber Police officer data found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>
</body>
</html>
