<?php
// profile.php
session_start();
include 'backend/db.php';

$message = '';
$user = null;

if (!isset($_SESSION['user_id'])) {
    $message = "Please log in to view your profile.";
} else {
    $user_id = (int)$_SESSION['user_id'];
    $sql = "SELECT cr.First_Name, cr.Last_Name, cr.Email_Address, cr.Cell_Number, cr.Present_Address,
                   cu.NID, cu.Gender, cu.Past_Address, cu.Occupation, cu.Marital_Status, 
                   cu.Father_Name, cu.Father_NID, cu.Mother_Name, cu.Mother_NID, cu.DOB, 
                   cu.Blood_Group, cu.Account_Verification
            FROM Citizen_Register cr
            LEFT JOIN Citizen_User cu ON cr.User_ID = cu.User_ID
            WHERE cr.User_ID = '$user_id'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        $message = "Error: Database query failed - " . mysqli_error($conn);
    } elseif (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        $message = "Error: User not found.";
        session_destroy();
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:wght@300;400;600;700&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<?php include 'include/header.php'; ?>

<div class="profile-container">
    <div class="left-fixed">
        <h1 class="violet">PRO<span class="red">FILE</span></h1>
        <h2>
            <?php
            if (is_array($user)) {
                echo htmlspecialchars(($user['First_Name'] ?? '') . ' ' . ($user['Last_Name'] ?? ''));
            } else {
                echo 'Guest';
            }
            ?>
        </h2>
        <?php
        $profileImg = "css/img/placeholder.png";
        if (is_array($user) && !empty($user['NID_Image'])) {
            if (strlen($user['NID_Image']) > 200) {
                $profileImg = "data:image/jpeg;base64," . base64_encode($user['NID_Image']);
            } else {
                $profileImg = htmlspecialchars($user['NID_Image']);
            }
        }
        ?>
        <img src="<?php echo $profileImg; ?>" alt="Profile Image" class="profile-placeholder">
    </div>

    <div class="right-scroll">
        <?php if ($message): ?>
            <p style="color: red;"><?php echo htmlspecialchars($message); ?> 
                <a href="citizen_login.php">Log in</a>
            </p>
        <?php elseif (is_array($user) && $user): ?>
            <?php
            $fields = [
                "NID" => "NID No.",
                "First_Name" => "First Name",
                "Last_Name" => "Last Name",
                "Gender" => "Gender",
                "Occupation" => "Occupation",
                "Present_Address" => "Present Address",
                "Past_Address" => "Permanent Address",
                "DOB" => "Date of Birth",
                "Cell_Number" => "Cell Number",
                "Email_Address" => "Email Address",
                "Marital_Status" => "Marital Status",
                "Father_Name" => "Father's Name",
                "Father_NID" => "Father's NID",
                "Mother_Name" => "Mother's Name",
                "Mother_NID" => "Mother's NID",
                "Blood_Group" => "Blood Group",
                "Account_Verification" => "Account Verification"
            ];
            foreach ($fields as $key => $label): ?>
                <div class="form-group">
                    <label><?php echo $label; ?>:</label>
                    <span class="profileformI">
                        <?php echo htmlspecialchars($user[$key] ?? 'Not Provided'); ?>
                    </span>
                </div>
            <?php endforeach; ?>
            <p><strong>Last Login:</strong> <?php echo htmlspecialchars($_SESSION['last_login'] ?? 'Not recorded'); ?></p>
            <a href="citizen_login.php?logout=true">Logout</a>
        <?php else: ?>
            <p><?php echo htmlspecialchars($message ?: "Error: User data unavailable."); ?> 
               <a href="citizen_login.php">Log in</a>
            </p>
        <?php endif; ?>
    </div>
</div>

<?php include 'include/footer.php'; ?>
</body>
</html>