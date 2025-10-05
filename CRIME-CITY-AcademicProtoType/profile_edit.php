<?php
// profile_edit.php
session_start();
include 'backend/db.php';

global $conn;
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$message = '';

if (!$user_id) {
    $message = "Error: No user session found. Please log in or register.";
} else {
    // Fetch user data
    $sql = "SELECT cr.First_Name, cr.Last_Name, cr.Email_Address, cr.Cell_Number, cr.Present_Address,
                   cu.NID, cu.Gender, cu.Past_Address, cu.Occupation, cu.Marital_Status, 
                   cu.Father_Name, cu.Father_NID, cu.Mother_Name, cu.Mother_NID, cu.DOB, cu.Blood_Group, cu.NID_Image
            FROM Citizen_Register cr
            LEFT JOIN Citizen_User cu ON cr.User_ID = cu.User_ID
            WHERE cr.User_ID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $message = "Error: Failed to prepare SQL statement: " . $conn->error;
    } else {
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            $message = "Error: Failed to execute SQL query: " . $stmt->error;
        } else {
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            if (!$user) {
                $message = "Error: Unauthorized access or user not found.";
            }
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_changes']) && $message === '') {
    // Get form data
    $nid = filter_input(INPUT_POST, 'nid', FILTER_VALIDATE_INT) ?: null;
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $email_address = filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL);
    $cell_number = filter_input(INPUT_POST, 'cell_number', FILTER_SANITIZE_STRING);
    $present_address = filter_input(INPUT_POST, 'present_address', FILTER_SANITIZE_STRING);
    $past_address = filter_input(INPUT_POST, 'past_address', FILTER_SANITIZE_STRING);
    $occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_STRING);
    $marital_status = filter_input(INPUT_POST, 'marital_status', FILTER_SANITIZE_STRING);
    $father_name = filter_input(INPUT_POST, 'father_name', FILTER_SANITIZE_STRING);
    $father_nid = filter_input(INPUT_POST, 'father_nid', FILTER_VALIDATE_INT) ?: null;
    $mother_name = filter_input(INPUT_POST, 'mother_name', FILTER_SANITIZE_STRING);
    $mother_nid = filter_input(INPUT_POST, 'mother_nid', FILTER_VALIDATE_INT) ?: null;
    $dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_STRING);
    $blood_group = filter_input(INPUT_POST, 'blood_group', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);

    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($email_address) || empty($cell_number) || empty($present_address)) {
        $message = "Error: Required fields (First Name, Last Name, Email, Cell Number, Present Address) are missing.";
    } elseif (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $message = "Error: Invalid email format.";
    } elseif (!preg_match('/^\d{11}$/', $cell_number)) {
        $message = "Error: Cell Number must be exactly 11 digits.";
    } elseif ($nid && !preg_match('/^\d+$/', $nid)) {
        $message = "Error: NID must be a valid number.";
    } else {
        // Check if NID is being set for the first time or updated
        $is_new_nid = $user['NID'] === null && $nid !== null;
        if ($nid) {
            // Check login session if NID exists
            $sql = "SELECT COUNT(*) as login_count FROM Citizen_Login WHERE NID = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $message = "Error: Failed to prepare login count check: " . $conn->error;
            } else {
                $stmt->bind_param("i", $nid);
                $stmt->execute();
                $login_count = $stmt->get_result()->fetch_assoc()['login_count'];
                $stmt->close();
                if (!$is_new_nid && $login_count != 1) {
                    $message = "Error: Invalid session. Please log in again.";
                }
            }
        }

        if (!$message) {
            // Check for duplicate email (excluding current user)
            $sql = "SELECT User_ID FROM Citizen_Register WHERE Email_Address = ? AND User_ID != ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $message = "Error: Failed to prepare duplicate email check: " . $conn->error;
            } else {
                $stmt->bind_param("si", $email_address, $user_id);
                if (!$stmt->execute()) {
                    $message = "Error: Failed to execute duplicate email check: " . $stmt->error;
                } elseif ($stmt->get_result()->num_rows > 0) {
                    $message = "Error: Email already registered.";
                }
                $stmt->close();
            }
        }

        if (!$message && $nid) {
            // Check for duplicate NID (excluding current user)
            $sql = "SELECT User_ID FROM Citizen_User WHERE NID = ? AND User_ID != ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $message = "Error: Failed to prepare duplicate NID check: " . $conn->error;
            } else {
                $stmt->bind_param("ii", $nid, $user_id);
                if (!$stmt->execute()) {
                    $message = "Error: Failed to execute duplicate NID check: " . $stmt->error;
                } elseif ($stmt->get_result()->num_rows > 0) {
                    $message = "Error: NID already registered.";
                }
                $stmt->close();
            }
        }

        if (!$message) {
            // Handle NID image upload
            $nid_image = null;
            if (isset($_FILES['uploadNID']) && $_FILES['uploadNID']['error'] == UPLOAD_ERR_OK) {
                $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
                $file_type = mime_content_type($_FILES['uploadNID']['tmp_name']);
                if (!in_array($file_type, $allowed_types)) {
                    $message = "Error: Only PNG, JPG, or JPEG files are allowed.";
                } elseif ($_FILES['uploadNID']['size'] > 5 * 1024 * 1024) {
                    $message = "Error: File size must not exceed 5MB.";
                } else {
                    $nid_image = file_get_contents($_FILES['uploadNID']['tmp_name']);
                }
            } elseif (isset($_FILES['uploadNID']) && $_FILES['uploadNID']['error'] != UPLOAD_ERR_NO_FILE) {
                $message = "Error: File upload failed with error code " . $_FILES['uploadNID']['error'];
            }

            if (!$message) {
                // Begin transaction
                $conn->begin_transaction();
                try {
                    // Update Citizen_Register
                    $sql = "UPDATE Citizen_Register SET First_Name = ?, Last_Name = ?, Email_Address = ?, Cell_Number = ?, Present_Address = ? WHERE User_ID = ?";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        throw new Exception("Failed to prepare Citizen_Register update: " . $conn->error);
                    }
                    $stmt->bind_param("sssssi", $first_name, $last_name, $email_address, $cell_number, $present_address, $user_id);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to execute Citizen_Register update: " . $stmt->error);
                    }
                    $stmt->close();

                    // Update Citizen_User
                    $sql = "UPDATE Citizen_User SET NID = ?, Gender = ?, Past_Address = ?, Occupation = ?, Marital_Status = ?, Father_Name = ?, Father_NID = ?, Mother_Name = ?, Mother_NID = ?, DOB = ?, Blood_Group = ?" . ($nid_image !== null ? ", NID_Image = ?" : "") . " WHERE User_ID = ?";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        throw new Exception("Failed to prepare Citizen_User update: " . $conn->error);
                    }
                    if ($nid_image !== null) {
                        $stmt->bind_param("isssssissssi", $nid, $gender, $past_address, $occupation, $marital_status, $father_name, $father_nid, $mother_name, $mother_nid, $dob, $blood_group, $nid_image, $user_id);
                    } else {
                        $stmt->bind_param("isssssisssi", $nid, $gender, $past_address, $occupation, $marital_status, $father_name, $father_nid, $mother_name, $mother_nid, $dob, $blood_group, $user_id);
                    }
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to execute Citizen_User update: " . $stmt->error);
                    }
                    $stmt->close();

                    // If NID is newly set, insert into Citizen_Login
                    if ($is_new_nid) {
                        $sql = "SELECT Citizen_Password FROM Citizen_Register WHERE User_ID = ?";
                        $stmt = $conn->prepare($sql);
                        if ($stmt === false) {
                            throw new Exception("Failed to prepare password fetch: " . $conn->error);
                        }
                        $stmt->bind_param("i", $user_id);
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to execute password fetch: " . $stmt->error);
                        }
                        $password = $stmt->get_result()->fetch_assoc()['Citizen_Password'];
                        $stmt->close();

                        $sql = "INSERT INTO Citizen_Login (NID, Email_Address, Login_Pass) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        if ($stmt === false) {
                            throw new Exception("Failed to prepare Citizen_Login insert: " . $conn->error);
                        }
                        $stmt->bind_param("iss", $nid, $email_address, $password);
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to execute Citizen_Login insert: " . $stmt->error);
                        }
                        $login_id = $conn->insert_id;
                        $stmt->close();

                        // Update Citizen_User with Login_ID
                        $sql = "UPDATE Citizen_User SET Login_ID = ? WHERE User_ID = ?";
                        $stmt = $conn->prepare($sql);
                        if ($stmt === false) {
                            throw new Exception("Failed to prepare Citizen_User update: " . $conn->error);
                        }
                        $stmt->bind_param("ii", $login_id, $user_id);
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to execute Citizen_User update: " . $stmt->error);
                        }
                        $stmt->close();
                    }

                    // Update Complainant with NID if newly set
                    if ($is_new_nid) {
                        $sql = "UPDATE Complainant c JOIN Citizen_User cu ON c.Complainant_ID = cu.Complainant_ID SET c.NID = ? WHERE cu.User_ID = ?";
                        $stmt = $conn->prepare($sql);
                        if ($stmt === false) {
                            throw new Exception("Failed to prepare Complainant update: " . $conn->error);
                        }
                        $stmt->bind_param("ii", $nid, $user_id);
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to execute Complainant update: " . $stmt->error);
                        }
                        $stmt->close();
                    }

                    // Commit transaction
                    $conn->commit();
                    $message = "Profile updated successfully!";
                    header("Location: profile.php"); // Redirect to profile page
                    exit();
                } catch (Exception $e) {
                    $conn->rollback();
                    $message = "Error: " . $e->getMessage();
                }
            }
        }
    }
}

// Handle account deletion
if (isset($_POST['delete_account']) && $message === '') {
    $conn->begin_transaction();
    try {
        // Delete from Citizen_Login
        $sql = "DELETE cl FROM Citizen_Login cl JOIN Citizen_User cu ON cl.NID = cu.NID WHERE cu.User_ID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare Citizen_Login delete: " . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute Citizen_Login delete: " . $stmt->error);
        }
        $stmt->close();

        // Delete from Citizen_User (cascades to Complainant, etc.)
        $sql = "DELETE FROM Citizen_User WHERE User_ID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare Citizen_User delete: " . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute Citizen_User delete: " . $stmt->error);
        }
        $stmt->close();

        // Delete from Citizen_Register (already cascaded)
        $sql = "DELETE FROM Citizen_Register WHERE User_ID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare Citizen_Register delete: " . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute Citizen_Register delete: " . $stmt->error);
        }
        $stmt->close();

        $conn->commit();
        session_destroy();
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error deleting account: " . $e->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | CRIME CITY</title>
    <link rel="shortcut icon" href="img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
</head>
<body>
<?php include 'include/header.php'; ?>

<div class="profile-container">
    <div class="left-fixed">
        <h1 class="violet">PRO<span class="red">FILE</span></h1>
        <h2>
    <?php 
        echo htmlspecialchars(($user['First_Name'] ?? '') . ' ' . ($user['Last_Name'] ?? '') ?: 'Guest'); 
    ?>
</h2>

        <img src="<?php echo isset($user['NID_Image']) && $user['NID_Image'] ? 'data:image/jpeg;base64,' . base64_encode($user['NID_Image']) : 'css/img/placeholder.png'; ?>" alt="Profile Image" class="profile-placeholder">
        <p id="nid-label">Insert your NID Image and your profile image will be shown here.</p>
        <?php if ($message): ?>
            <p style="color: <?php echo strpos($message, 'Error') !== false ? 'red' : 'green'; ?>;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
        <?php if ($message === ''): ?>
            <form method="post" style="display: inline;">
                <button type="submit" name="save_changes" class="save-changes-btn">SAVE CHANGES</button>
            </form>
            <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                <button type="submit" name="delete_account" class="delete-account-btn">DELETE ACCOUNT</button>
            </form>
        <?php else: ?>
            <p><a href="citizen_login.php">Please log in to continue.</a></p>
        <?php endif; ?>
    </div>
    <div class="right-scroll">
        <?php if ($message === ''): ?>
            <form method="post" action="profile_edit.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label>NID IMAGE:</label>
                    <input type="file" accept=".png,.jpg,.jpeg" id="uploadNID" name="uploadNID">
                    <label for="uploadNID" id="file-label">INSERT YOUR NID HERE</label>
                </div>
                <div class="form-group">
                    <label>NID No.:</label>
                    <input type="text" name="nid" class="profileformI" value="<?php echo htmlspecialchars($user['NID'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="first_name" placeholder="First Name" class="profileform" value="<?php echo htmlspecialchars($user['First_Name'] ?? ''); ?>" >
                    <input type="text" name="last_name" placeholder="Last Name" class="profileform" value="<?php echo htmlspecialchars($user['Last_Name'] ?? ''); ?>" >
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <select name="gender" class="profileformI">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($user['Gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($user['Gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($user['Gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Occupation:</label>
                    <input type="text" name="occupation" class="profileformI" value="<?php echo htmlspecialchars($user['Occupation'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Present Address:</label>
                    <input type="text" name="present_address" class="profileformI" value="<?php echo htmlspecialchars($user['Present_Address'] ?? ''); ?>" >
                </div>
                <div class="form-group">
                    <label>Permanent Address:</label>
                    <input type="text" name="past_address" class="profileformI" value="<?php echo htmlspecialchars($user['Past_Address'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Date of Birth:</label>
                    <input type="date" name="dob" class="profileformI" value="<?php echo htmlspecialchars($user['DOB'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Cell Number:</label>
                    <input type="text" name="cell_number" class="profileformI" maxlength="11" pattern="\d{11}" title="Cell Number must be exactly 11 digits" value="<?php echo htmlspecialchars($user['Cell_Number'] ?? ''); ?>" >
                </div>
                <div class="form-group">
                    <label>Email Address:</label>
                    <input type="email" name="email_address" class="profileformI" value="<?php echo htmlspecialchars($user['Email_Address'] ?? ''); ?>" >
                </div>
                <div class="form-group">
                    <label>Marital Status:</label>
                    <select name="marital_status" class="profileformI">
                        <option value="">Select Status</option>
                        <option value="Single" <?php echo ($user['Marital_Status'] ?? '') == 'Single' ? 'selected' : ''; ?>>Single</option>
                        <option value="Married" <?php echo ($user['Marital_Status'] ?? '') == 'Married' ? 'selected' : ''; ?>>Married</option>
                        <option value="Divorced" <?php echo ($user['Marital_Status'] ?? '') == 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
                        <option value="Widowed" <?php echo ($user['Marital_Status'] ?? '') == 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Father's Name:</label>
                    <input type="text" name="father_name" class="profileformI" value="<?php echo htmlspecialchars($user['Father_Name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Father's NID:</label>
                    <input type="text" name="father_nid" class="profileformI" value="<?php echo htmlspecialchars($user['Father_NID'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Mother's Name:</label>
                    <input type="text" name="mother_name" class="profileformI" value="<?php echo htmlspecialchars($user['Mother_Name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Mother's NID:</label>
                    <input type="text" name="mother_nid" class="profileformI" value="<?php echo htmlspecialchars($user['Mother_NID'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Blood Group:</label>
                    <select name="blood_group" class="profileformI">
                        <option value="">Select Blood Group</option>
                        <option value="A+" <?php echo ($user['Blood_Group'] ?? '') == 'A+' ? 'selected' : ''; ?>>A+</option>
                        <option value="A-" <?php echo ($user['Blood_Group'] ?? '') == 'A-' ? 'selected' : ''; ?>>A-</option>
                        <option value="B+" <?php echo ($user['Blood_Group'] ?? '') == 'B+' ? 'selected' : ''; ?>>B+</option>
                        <option value="B-" <?php echo ($user['Blood_Group'] ?? '') == 'B-' ? 'selected' : ''; ?>>B-</option>
                        <option value="AB+" <?php echo ($user['Blood_Group'] ?? '') == 'AB+' ? 'selected' : ''; ?>>AB+</option>
                        <option value="AB-" <?php echo ($user['Blood_Group'] ?? '') == 'AB-' ? 'selected' : ''; ?>>AB-</</option>
                        <option value="O+" <?php echo ($user['Blood_Group'] ?? '') == 'O+' ? 'selected' : ''; ?>>O+</option>
                        <option value="O-" <?php echo ($user['Blood_Group'] ?? '') == 'O-' ? 'selected' : ''; ?>>O-</option>
                    </select>
                </div>
            </form>
        <?php else: ?>
            <p><?php echo htmlspecialchars($message); ?> <a href="citizen_login.php">Log in</a></p>
        <?php endif; ?>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nidImageInput = document.querySelector('#uploadNID');
    const fileLabel = document.querySelector('#file-label');
    const profileImage = document.querySelector('.profile-placeholder');
    const nidLabel = document.querySelector('#nid-label');
    const form = document.querySelector('form[action="profile_edit.php"]');
    const nidInput = document.querySelector('input[name="nid"]');
    const cellNumberInput = document.querySelector('input[name="cell_number"]');
    const emailInput = document.querySelector('input[name="email_address"]');

    nidImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Only PNG, JPG, or JPEG files are allowed.');
                this.value = '';
                fileLabel.textContent = 'INSERT YOUR NID HERE';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must not exceed 5MB.');
                this.value = '';
                fileLabel.textContent = 'INSERT YOUR NID HERE';
                return;
            }
            fileLabel.textContent = file.name;
            nidLabel.textContent = '';
            const reader = new FileReader();
            reader.onload = function(e) {
                profileImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            fileLabel.textContent = 'INSERT YOUR NID HERE';
            nidLabel.textContent = 'Insert your NID Image and your profile image will be shown here.';
        }
    });

    if (form) {
        form.addEventListener('submit', function(e) {
            if (nidInput.value && !/^\d+$/.test(nidInput.value)) {
                e.preventDefault();
                alert('NID must be a valid number.');
                return;
            }
            if (!cellNumberInput.value || !/^\d{11}$/.test(cellNumberInput.value)) {
                e.preventDefault();
                alert('Cell Number must be exactly 11 digits.');
                return;
            }
            if (!emailInput.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
        });
    }
});
</script>
</body>
</html>