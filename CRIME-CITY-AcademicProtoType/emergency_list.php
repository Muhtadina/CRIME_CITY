<?php
// emergency_list.php
// Include database connection
require 'backend/db.php';

// Get the Emergency_Type filter from query parameter, if any
$emergency_type = isset($_GET['type']) ? urldecode($_GET['type']) : 'ALL';

// Prepare the SQL query with prepared statements
if ($emergency_type === 'ALL') {
    $query = "SELECT Emergency_Name, Emergency_Type, Contact_Number FROM Emergency ORDER BY Emergency_ID";
    $stmt = $conn->prepare($query);
} else {
    $query = "SELECT Emergency_Name, Emergency_Type, Contact_Number FROM Emergency WHERE Emergency_Type = ? ORDER BY Emergency_ID";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $emergency_type);
}
$stmt->execute();
$result = $stmt->get_result();
$emergencies = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency | CRIME CITY</title>
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
    <h2>BANGLADESH GOV EMERGENCY CONTACTS - <?php echo htmlspecialchars($emergency_type); ?></h2>
    <div class="table_for_sql_display">
        <table class="emergency-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Contact Number</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($emergencies) > 0): ?>
                    <?php foreach ($emergencies as $emergency): ?>
                        <tr>
                            <td><b><?php echo htmlspecialchars($emergency['Emergency_Name']); ?></b></td>
                            <td><?php echo htmlspecialchars($emergency['Emergency_Type']); ?></td>
                            <td><b class="green"><?php echo htmlspecialchars($emergency['Contact_Number']); ?></b></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No emergency contacts found for this type.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to the emergency dropdown menu items
    const menuItems = document.querySelectorAll('.menu-btn');
    menuItems.forEach(item => {
        if (item.parentElement.parentElement.previousElementSibling.textContent.includes('EMERGENCY')) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const type = this.textContent.trim();
                window.location.href = 'emergency_list.php?type=' + encodeURIComponent(type);
            });
        }
    });
});
</script>
</body>
</html>