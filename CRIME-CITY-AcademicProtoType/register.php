<?php
// register.php
session_start();
include 'backend/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $first_name = mysqli_real_escape_string($conn, $_POST['First_Name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['Last_Name']);
    $email_address = mysqli_real_escape_string($conn, $_POST['Email_Address']);
    $cell_number = mysqli_real_escape_string($conn, $_POST['Cell_Number']);
    $present_address = mysqli_real_escape_string($conn, $_POST['Present_Address']);
    $password = $_POST['Citizen_Password'];
    $confirm_password = $_POST['Confirm_Password'];

    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($email_address) || empty($cell_number) || empty($present_address) || empty($password)) {
        $message = "Error: All fields are required.";
    } elseif (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $message = "Error: Invalid email format.";
    } elseif (strlen($cell_number) != 11 || !ctype_digit($cell_number)) {
        $message = "Error: Cell Number must be exactly 11 digits.";
    } elseif ($password !== $confirm_password) {
        $message = "Error: Passwords do not match.";
    } else {
        // Check for duplicate email or cell number
        $sql = "SELECT User_ID FROM Citizen_Register WHERE Email_Address = '$email_address' OR Cell_Number = '$cell_number'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $message = "Error: Email or Cell Number already registered.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into Citizen_Register
            $sql = "INSERT INTO Citizen_Register (First_Name, Last_Name, Email_Address, Cell_Number, Present_Address, Citizen_Password) 
                    VALUES ('$first_name', '$last_name', '$email_address', '$cell_number', '$present_address', '$hashed_password')";
            if (mysqli_query($conn, $sql)) {
                $user_id = mysqli_insert_id($conn);

                // Insert into Citizen_Login
                $sql = "INSERT INTO Citizen_Login (User_ID, Email_Address, Login_Pass) 
                        VALUES ('$user_id', '$email_address', '$hashed_password')";
                if (mysqli_query($conn, $sql)) {
                    $login_id = mysqli_insert_id($conn);

                    // Insert into Citizen_User
                    $sql = "INSERT INTO Citizen_User (User_ID, Login_ID, Account_Verification) 
                            VALUES ('$user_id', '$login_id', 'Pending')";
                    if (mysqli_query($conn, $sql)) {
                        $_SESSION['user_id'] = $user_id;
                        $message = "Registration successful! Redirecting to profile...";
                        header("Location: profile.php");
                        exit();
                    } else {
                        $message = "Error: Failed to create user profile.";
                    }
                } else {
                    $message = "Error: Failed to create login record.";
                }
            } else {
                $message = "Error: Failed to register user.";
            }
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CRIME CITY</title>
    <link rel="shortcut icon" href="img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <h1>CRIME CITY</h1>
    <div class="middlebox">
        <h2>CITIZEN ACCOUNT | REGISTER</h2>
        <?php if (!empty($message)): ?>
            <p style="color: <?php echo strpos($message, 'Error') !== false ? 'red' : 'green'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
        <form class="fillup" action="register.php" method="post">
            <input type="text" placeholder="First Name" class="form_inputII" name="First_Name" required>
            <input type="text" placeholder="Last Name" class="form_inputII" name="Last_Name" required>
            <input type="email" placeholder="Email" class="form_inputII" name="Email_Address" required>
            <input type="text" placeholder="Cell Number" class="form_inputII" name="Cell_Number" required pattern="\d{11}" title="Cell Number must be exactly 11 digits">
            <input type="text" placeholder="Present Address" class="form_input" name="Present_Address" required>
            <input type="password" placeholder="Create Password" class="form_inputII" name="Citizen_Password" required>
            <input type="password" placeholder="Confirm Password" class="form_inputII" name="Confirm_Password" required>
            <button type="submit" class="btn-login">CREATE ACCOUNT</button>
            <a href="citizen_login.php" class="btn-prime">Already have an account? LOGIN</a>
        </form>
    </div>
    <?php include 'include/fixedfoot.php'; ?>
</body>
</html>