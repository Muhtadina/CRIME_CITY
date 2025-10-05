<?php
// citizen_verification_edit.php
session_start();
include 'backend/db.php';

$message = '';
$user = null;

if (!isset($_SESSION['cyberpol_id'])) {
    $message = "Error: Please log in as CyberPol to access this page.";
} elseif (!isset($_GET['nid'])) {
    $message = "Error: No citizen NID provided.";
} else {
    $nid = filter_input(INPUT_GET, 'nid', FILTER_VALIDATE_INT);
    if (!$nid) {
        $message = "Error: Invalid NID.";
    } else {
        // Verify CyberPol's division matches citizen's division
        $sql = "SELECT cu.Division 
                FROM Citizen_User cu 
                JOIN CyberPol cp ON cu.Division = cp.Division 
                WHERE cu.NID = ? AND cp.CyberPol_ID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $message = "Error: Failed to prepare division check: " . $conn->error;
        } else {
            $stmt->bind_param("is", $nid, $_SESSION['cyberpol_id']);
            if (!$stmt->execute()) {
                $message = "Error: Failed to execute division check: " . $stmt->error;
            } else {
                if ($stmt->get_result()->num_rows === 0) {
                    $message = "Error: You are not authorized to edit this citizen's profile.";
                } else {
                    // Fetch citizen data
                    $sql = "SELECT cr.First_Name, cr.Last_Name, cr.Email_Address, cr.Cell_Number, cr.Present_Address,
                            cu.NID, cu.Gender, cu.Past_Address, cu.Occupation, cu.Marital_Status, 
                            cu.Father_Name, cu.Father_NID, cu.Mother_Name, cu.Mother_NID, cu.DOB, 
                            cu.Blood_Group, cu.Account_Verification, cu.NID_Image
                            FROM Citizen_Register cr
                            JOIN Citizen_User cu ON cr.User_ID = cu.User_ID
                            WHERE cu.NID = ?";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        $message = "Error: Failed to prepare citizen query: " . $conn->error;
                    } else {
                        $stmt->bind_param("i", $nid);
                        if (!$stmt->execute()) {
                            $message = "Error: Failed to execute citizen query: " . $stmt->error;
                        } else {
                            $user = $stmt->get_result()->fetch_assoc();
                            if (!$user) {
                                $message = "Error: Citizen not found.";
                            }
                        }
                    }
                }
                $stmt->close();
            }
        }
    }
}

// Handle verification update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $message === '' && $user && isset($_POST['account_verification'])) {
    $account_verification = filter_input(INPUT_POST, 'account_verification', FILTER_SANITIZE_STRING);
    if (!in_array($account_verification, ['Pending', 'Verified'])) {
        $message = "Error: Invalid verification status.";
    } else {
        $sql = "UPDATE Citizen_User SET Account_Verification = ? WHERE NID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $message = "Error: Failed to prepare update query: " . $conn->error;
        } else {
            $stmt->bind_param("si", $account_verification, $nid);
            if (!$stmt->execute()) {
                $message = "Error: Failed to update verification status: " . $stmt->error;
            } else {
                $message = "Verification status updated successfully.";
                header("Location: cyberpol_dashboard.php");
                exit();
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
    <title>Citizen Verification | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
</head>
<body>
<?php include 'include/cyberpol_header.php'; ?>

<div class="profile-container">
    <div class="left-fixed">
        <h1 class="violet">CITIZEN <span class="red">PROFILE</span></h1>
        <h2><?php echo htmlspecialchars(($user['First_Name'] ?? '') . ' ' . ($user['Last_Name'] ?? '')) ?: 'Guest'; ?></h2>
        <img src="<?php echo isset($user['NID_Image']) && $user['NID_Image'] ? 'data:image/jpeg;base64,' . base64_encode($user['NID_Image']) : 'css/img/placeholder.png'; ?>" alt="Profile Image" class="profile-placeholder">
        <?php if ($message): ?>
            <p style="color: <?php echo strpos($message, 'Error') !== false ? 'red' : 'green'; ?>;">
                <?php echo htmlspecialchars($message); ?>
                <?php if (strpos($message, 'log in') !== false): ?>
                    <a href="cyberpol.php">Log in</a>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>
    <div class="right-scroll">
        <?php if ($message === '' && $user): ?>
            <div class="form-group">
                <label>NID No.:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['NID'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Full Name:</label>
                <span class="profileform"><?php echo htmlspecialchars($user['First_Name'] ?? 'Not Provided'); ?></span>
                <span class="profileform"><?php echo htmlspecialchars($user['Last_Name'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Gender:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Gender'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Occupation:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Occupation'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Present Address:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Present_Address'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Permanent Address:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Past_Address'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Date of Birth:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['DOB'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Cell Number:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Cell_Number'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Email Address:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Email_Address'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Marital Status:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Marital_Status'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Father's Name:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Father_Name'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Father's NID:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Father_NID'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Mother's Name:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Mother_Name'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Mother's NID:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Mother_NID'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Blood Group:</label>
                <span class="profileformI"><?php echo htmlspecialchars($user['Blood_Group'] ?? 'Not Provided'); ?></span>
            </div>
            <div class="form-group">
                <label>Account Verification:</label>
                <form method="post" action="citizen_verification_edit.php?nid=<?php echo urlencode($nid); ?>">
                    <select name="account_verification" class="profileformI" required>
                        <option value="Pending" <?php echo ($user['Account_Verification'] ?? '') == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Verified" <?php echo ($user['Account_Verification'] ?? '') == 'Verified' ? 'selected' : ''; ?>>Verified</option>
                    </select>
                    <button type="submit" class="edit-btn">Verify</button>
                </form>
            </div>
        <?php else: ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php include 'include/footer.php'; ?>
</body>
</html>