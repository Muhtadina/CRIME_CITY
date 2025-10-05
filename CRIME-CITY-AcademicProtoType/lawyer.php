<?php
// lawyer.php
// Include database connection
require 'backend/db.php';

// Fetch lawyer data
$sql = "SELECT Lawyer_Name, Lawyer_Type, Email_Address, Contact_Number, Portfolio_Link FROM Lawyer";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyers | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">

</head>
<body>
<?php include 'include/header.php'; ?>

<div class="page">
    <h1><b class="violet">EMER</b><b class="red">GENCY</b></h1>
    <h2>BANGLADESH GOV EMERGENCY CONTACTS - Lawyers</h2>
    <div class="table_for_sql_display">
        <table class="emergency-table">
            <thead>
                <tr>
                    <th>Lawyer Name</th>
                    <th>Categorization</th>
                    <th>Email Address</th>
                    <th>Contact Number</th>
                    <th>Portfolio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Lawyer_Name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Lawyer_Type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Email_Address']) . "</td>";
                        echo "<td><b class='green'>" . htmlspecialchars($row['Contact_Number']) . "</b></td>";
                        echo "<td><a href='" . htmlspecialchars($row['Portfolio_Link']) . "' target='_blank' class='btn-link'>LINK &gt&gt;</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No lawyer data found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>

</body>
</html>
