<?php
// citizen_report_history.php
session_start();
include 'backend/db.php';

$message = '';
$reports = [];
$user = null;

if (!isset($_SESSION['user_id'])) {
    $message = "Error: Please log in to view your report history.";
} else {
    $user_id = (int)$_SESSION['user_id'];
    // Fetch user details for display
    $sql = "SELECT First_Name, Last_Name FROM Citizen_Register WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Fetch reports
    $sql = "SELECT Report_ID, Crime_Type, Submission_Status FROM Crime_Report WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $reports = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Handle delete
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_report'])) {
        $report_id = filter_input(INPUT_POST, 'report_id', FILTER_VALIDATE_INT);
        $sql = "DELETE FROM Crime_Report WHERE Report_ID = ? AND User_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $report_id, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: citizen_report_history.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report History | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <style>
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .report-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<?php include 'include/header.php'; ?>

<div class="profile-container">
    <div class="left-fixed">
        <h1 class="violet">PRO<span class="red">FILE</span></h1>
        <h2><?php echo htmlspecialchars(($user['First_Name'] ?? '') . ' ' . ($user['Last_Name'] ?? '')) ?: 'Guest'; ?></h2>
        <img src="<?php echo isset($user['NID_Image']) && $user['NID_Image'] ? 'data:image/jpeg;base64,' . base64_encode($user['NID_Image']) : 'css/img/placeholder.png'; ?>" alt="Profile Image" class="profile-placeholder">
        <?php if ($message): ?>
            <p style="color: red;"><?php echo htmlspecialchars($message); ?> <a href="citizen_login.php">Log in</a></p>
        <?php else: ?>
            <a href="profile_edit.php" class="edit-account-btn">EDIT ACCOUNT</a>
            <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                <button type="submit" name="delete_account" class="delete-account-btn">DELETE ACCOUNT</button>
            </form>
        <?php endif; ?>
    </div>
    <div class="right-scroll">
        <?php if ($message === ''): ?>
            <h3>Your Report History</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Crime Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="3">No reports found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['Crime_Type']); ?></td>
                                <td><?php echo htmlspecialchars($report['Submission_Status']); ?></td>
                                <td>
                                    <a href="edit_report.php?report_id=<?php echo $report['Report_ID']; ?>" class="edit-btn">Edit</a>
                                    <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this report?');">
                                        <input type="hidden" name="report_id" value="<?php echo $report['Report_ID']; ?>">
                                        <button type="submit" name="delete_report" class="delete-account-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include 'include/footer.php'; ?>
</body>
</html>