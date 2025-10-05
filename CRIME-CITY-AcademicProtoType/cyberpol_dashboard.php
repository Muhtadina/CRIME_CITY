<?php
// cyberpol_dashboard.php
session_start();
include 'backend/db.php';

$message = '';
$citizens = [];
$cyberpol_division = '';

if (!isset($_SESSION['cyberpol_id'])) {
    $message = "Error: Please log in to access the dashboard.";
} else {
    $cyberpol_id = $_SESSION['cyberpol_id'];
    // Get CyberPol's Division
    $sql = "SELECT Division FROM CyberPol WHERE CyberPol_ID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $message = "Error: Failed to prepare SQL statement: " . $conn->error;
    } else {
        $stmt->bind_param("s", $cyberpol_id);
        if (!$stmt->execute()) {
            $message = "Error: Failed to execute SQL query: " . $stmt->error;
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $message = "Error: CyberPol account not found.";
            } else {
                $cyberpol_division = $result->fetch_assoc()['Division'];
                // Fetch citizens in the same division
                $sql = "SELECT cu.NID, cu.Account_Verification 
                        FROM Citizen_User cu 
                        JOIN Citizen_Register cr ON cu.User_ID = cr.User_ID 
                        WHERE cu.Division = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $message = "Error: Failed to prepare citizen query: " . $conn->error;
                } else {
                    $stmt->bind_param("s", $cyberpol_division);
                    if (!$stmt->execute()) {
                        $message = "Error: Failed to execute citizen query: " . $stmt->error;
                    } else {
                        $citizens = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    }
                }
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <style>
        .dashboard-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            font-family: "Oswald", sans-serif;
        }
        .dashboard-table th, .dashboard-table td {
            border: 2px solid #423e5d;
            padding: 10px;
            text-align: left;
        }
        .dashboard-table th {
            background-color: #4e1313;
            color: white;
        }
        .dashboard-table td {
            background-color: #e4e4e4;
        }
        .edit-btn {
            background-color: #423e5d;
            color: white;
            border: 3px solid white;
            padding: 6px 12px;
            border-radius: 12px;
            font-family: "Oswald", sans-serif;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }
        .edit-btn:hover {
            background-color: #272438;
        }
    </style>
</head>
<body>
<?php include 'include/cyberpol_header.php'; ?>

<div class="page">
    <h1><b class="violet">CYBERPOL</b><b class="red"> DASHBOARD</b></h1>
    <?php if ($message): ?>
        <p style="color: red; text-align: center;"><?php echo htmlspecialchars($message); ?> <a href="cyberpol.php">Log in</a></p>
    <?php else: ?>
        <h2>Citizens in <?php echo htmlspecialchars($cyberpol_division); ?> Division</h2>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>NID</th>
                    <th>Verification Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($citizens)): ?>
                    <tr>
                        <td colspan="3">No citizens found in your division.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($citizens as $citizen): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($citizen['NID'] ?? 'Not Provided'); ?></td>
                            <td><?php echo htmlspecialchars($citizen['Account_Verification'] ?? 'Pending'); ?></td>
                            <td><a href="citizen_verification_edit.php?nid=<?php echo urlencode($citizen['NID']); ?>" class="edit-btn">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'include/footer.php'; ?>
</body>
</html>